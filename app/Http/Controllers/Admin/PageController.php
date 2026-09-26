<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::withCount('buttons')->orderBy('sort_order')->get();
        $pagesBySlug = $pages->keyBy('slug');
        $pagesById = $pages->keyBy('id');

        $home = $pagesBySlug->get('home') ?? $pages->first();

        // Halaman yang sedang difokuskan (drill-down). Default = homepage.
        $focusId = $request->query('focus');
        $focus = $focusId && $pagesById->has((int) $focusId)
            ? $pagesById->get((int) $focusId)
            : $home;

        if ($focus) {
            $focus->load(['buttons' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')]);
        }

        // Petakan setiap tombol focus -> apakah submenu internal / link luar / belum ada halaman.
        $buttonMeta = [];
        if ($focus) {
            foreach ($focus->buttons as $btn) {
                $slug = self::internalSlug($btn->url);
                if ($slug === null) {
                    $buttonMeta[$btn->id] = ['type' => 'external', 'slug' => null, 'linkedPage' => null];
                } elseif ($slug === 'home' || $pagesBySlug->has($slug)) {
                    $buttonMeta[$btn->id] = ['type' => 'submenu', 'slug' => $slug, 'linkedPage' => $pagesBySlug->get($slug)];
                } else {
                    $buttonMeta[$btn->id] = ['type' => 'missing', 'slug' => $slug, 'linkedPage' => null];
                }
            }
        }

        // Breadcrumb: cari jalur Home -> ... -> focus lewat tombol internal (BFS).
        $breadcrumbs = $this->breadcrumbs($home, $focus, $pagesBySlug);

        // Halaman yang tidak ditautkan dari tombol mana pun (biar tidak hilang).
        $linkedSlugs = [];
        foreach (Page::with('buttons:id,page_id,url')->get() as $p) {
            foreach ($p->buttons as $b) {
                $s = self::internalSlug($b->url);
                if ($s) {
                    $linkedSlugs[$s] = true;
                }
            }
        }
        $orphans = $pages->filter(fn ($p) => $p->slug !== 'home' && ! isset($linkedSlugs[$p->slug]));

        return view('admin.pages.index', compact('pages', 'pagesBySlug', 'home', 'focus', 'buttonMeta', 'breadcrumbs', 'orphans'));
    }

    public function create(Request $request)
    {
        $page = new Page(['is_active' => true]);
        if ($request->query('slug')) {
            $page->slug = $request->query('slug');
            $page->name = ucwords(str_replace(['-', '_'], ' ', $request->query('slug')));
            $page->header_title = $page->name;
        }
        $parent = null;
        if ($request->query('parent_id')) {
            $parent = Page::with('buttons')->find($request->query('parent_id'));
        }
        return view('admin.pages.form', compact('page', 'parent'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handleUploads($request, $data);
        $page = Page::create($data);

        // Kalau dibuat sebagai sub-halaman, otomatis buatkan tombol di parent.
        $parentId = $request->input('parent_id');
        $fromButtonId = $request->input('from_button_id');
        if ($parentId && ($parent = Page::find($parentId))) {
            \App\Models\LinkButton::create([
                'page_id' => $parent->id,
                'title' => $page->name,
                'url' => '/' . $page->slug,
                'icon_width' => 40,
                'open_new_tab' => false,
                'is_active' => true,
                'sort_order' => ($parent->buttons()->max('sort_order') ?? 0) + 1,
            ]);
            return redirect()->route('admin.pages.index', ['focus' => $page->id])
                ->with('success', "Sub-halaman /{$page->slug} dibuat + tombol otomatis di {$parent->name}. Tinggal isi link di dalamnya.");
        }
        if ($fromButtonId && ($btn = \App\Models\LinkButton::find($fromButtonId))) {
            $btn->update(['url' => '/' . $page->slug, 'open_new_tab' => false]);
            return redirect()->route('admin.pages.index', ['focus' => $page->id])
                ->with('success', "Halaman /{$page->slug} dibuat & tombol \"{$btn->title}\" sudah ditautkan. Tinggal isi link di dalamnya.");
        }

        return redirect()->route('admin.pages.index', ['focus' => $page->id])->with('success', 'Halaman berhasil dibuat. Sekarang isi tombol/link di dalamnya.');
    }

    public function edit(Page $page)
    {
        $page->load('buttons');
        $parent = null;
        return view('admin.pages.form', compact('page', 'parent'));
    }

    /**
     * Deteksi apakah URL tombol adalah link internal ke /slug.
     * Return slug string, atau null kalau link eksternal.
     */
    public static function internalSlug(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || $url === '/') {
            return 'home';
        }
        // Hanya path internal satu segmen: /acara-tv, /pricelist, dst.
        if (! str_starts_with($url, '/') || str_starts_with($url, '//')) {
            return null;
        }
        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $path = trim($path, '/');
        if ($path === '' || str_contains($path, '/')) {
            return null;
        }
        if (! preg_match('/^[a-z0-9\-]+$/', $path)) {
            return null;
        }
        // Abaikan route sistem.
        if (in_array($path, ['admin', 'link', 'api', 'storage', 'build'])) {
            return null;
        }
        return $path;
    }

    /** Breadcrumb Home -> ... -> focus dengan BFS lewat tombol internal. */
    protected function breadcrumbs($home, $focus, $pagesBySlug): array
    {
        if (! $home || ! $focus) {
            return [];
        }
        if ($home->id === $focus->id) {
            return [$home];
        }
        $adj = [];
        foreach (Page::with('buttons:id,page_id,url')->get() as $p) {
            foreach ($p->buttons as $b) {
                $s = self::internalSlug($b->url);
                if ($s && $pagesBySlug->has($s)) {
                    $adj[$p->id][] = $pagesBySlug->get($s)->id;
                }
            }
        }
        $queue = [[$home->id]];
        $visited = [$home->id => true];
        while ($queue) {
            $path = array_shift($queue);
            $last = end($path);
            foreach ($adj[$last] ?? [] as $next) {
                if (isset($visited[$next])) {
                    continue;
                }
                $visited[$next] = true;
                $newPath = [...$path, $next];
                if ($next === $focus->id) {
                    $all = Page::whereIn('id', $newPath)->get()->keyBy('id');
                    return array_map(fn ($id) => $all[$id], $newPath);
                }
                $queue[] = $newPath;
            }
        }
        return [$home, $focus];
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validated($request, $page->id);
        $data = $this->handleUploads($request, $data, $page);
        $page->update($data);
        return back()->with('success', 'Halaman berhasil disimpan.');
    }

    public function destroy(Page $page)
    {
        if ($page->slug === 'home') {
            return back()->with('error', 'Halaman home tidak boleh dihapus.');
        }
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Halaman dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'slug' => ['required', 'alpha_dash', 'max:60', Rule::unique('pages', 'slug')->ignore($ignoreId)],
            'name' => 'required|max:100',
            'header_title' => 'required|max:120',
            'subtitle' => 'nullable|max:255',
            'show_logo' => 'nullable|boolean',
            'show_back_button' => 'nullable|boolean',
            'use_global_background' => 'nullable|boolean',
            'background_type' => 'required|in:image,color,gradient',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'background_color' => 'nullable|max:30',
            'background_gradient' => 'nullable|max:255',
            'logo_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'meta_title' => 'nullable|max:150',
            'meta_description' => 'nullable|max:500',
            'meta_keywords' => 'nullable|max:255',
            'meta_author' => 'nullable|max:255',
            'og_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'footer_text' => 'nullable|max:150',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['show_logo'] = $request->boolean('show_logo');
        $data['show_back_button'] = $request->boolean('show_back_button');
        $data['use_global_background'] = $request->boolean('use_global_background');
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    protected function handleUploads(Request $request, array $data, ?Page $page = null): array
    {
        if ($request->hasFile('logo_upload')) {
            if ($page?->logo_path && ! str_starts_with($page->logo_path, 'assets-legacy')) {
                Storage::disk('public')->delete($page->logo_path);
            }
            $data['logo_path'] = $request->file('logo_upload')->store('pages/logos', 'public');
        }
        if ($request->hasFile('background_image')) {
            if ($page?->background_image && ! str_starts_with($page->background_image, 'assets-legacy')) {
                Storage::disk('public')->delete($page->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('pages/backgrounds', 'public');
        }
        if ($request->hasFile('og_image_upload')) {
            if ($page?->og_image && ! str_starts_with($page->og_image, 'assets-legacy')) {
                Storage::disk('public')->delete($page->og_image);
            }
            $data['og_image'] = $request->file('og_image_upload')->store('pages/og', 'public');
        }
        unset($data['logo_upload'], $data['og_image_upload']);
        // jika tidak upload background baru, jangan timpa kolom dengan null dari validasi file
        if (! $request->hasFile('background_image')) {
            unset($data['background_image']);
        }
        return $data;
    }
}
