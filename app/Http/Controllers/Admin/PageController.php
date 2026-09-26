<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::withCount('buttons')->orderBy('sort_order')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->handleUploads($request, $data);
        $page = Page::create($data);
        return redirect()->route('admin.pages.edit', $page)->with('success', 'Halaman berhasil dibuat.');
    }

    public function edit(Page $page)
    {
        $page->load('buttons');
        return view('admin.pages.form', compact('page'));
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
