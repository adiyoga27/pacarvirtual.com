<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinkButton;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LinkButtonController extends Controller
{
    public function index(Page $page)
    {
        $buttons = $page->buttons()->orderBy('sort_order')->get();
        return view('admin.buttons.index', compact('page', 'buttons'));
    }

    public function create(Page $page)
    {
        $pages = Page::orderBy('sort_order')->get();
        return view('admin.buttons.form', ['page' => $page, 'pages' => $pages, 'button' => new LinkButton(['page_id' => $page->id, 'icon_width' => 40, 'is_active' => true, 'open_new_tab' => true])]);
    }

    public function store(Request $request, Page $page)
    {
        $data = $this->validated($request);
        $data['page_id'] = $page->id;
        if ($request->hasFile('icon_upload')) {
            $data['icon_path'] = $request->file('icon_upload')->store('icons', 'public');
        }
        LinkButton::create($data);
        return redirect()->route('admin.pages.index', ['focus' => $page->id])->with('success', 'Tombol berhasil ditambah.');
    }

    public function edit(Page $page, LinkButton $button)
    {
        $pages = Page::orderBy('sort_order')->get();
        return view('admin.buttons.form', compact('page', 'button', 'pages'));
    }

    public function update(Request $request, Page $page, LinkButton $button)
    {
        $data = $this->validated($request);
        if ($request->hasFile('icon_upload')) {
            if ($button->icon_path && ! str_starts_with($button->icon_path, 'assets-legacy')) {
                Storage::disk('public')->delete($button->icon_path);
            }
            $data['icon_path'] = $request->file('icon_upload')->store('icons', 'public');
        }
        $button->update($data);
        return redirect()->route('admin.pages.index', ['focus' => $page->id])->with('success', 'Tombol berhasil disimpan.');
    }

    public function destroy(Page $page, LinkButton $button)
    {
        if ($button->icon_path && ! str_starts_with($button->icon_path, 'assets-legacy')) {
            Storage::disk('public')->delete($button->icon_path);
        }
        $button->delete();
        return back()->with('success', 'Tombol dihapus.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|max:150',
            'url' => 'required|max:1000',
            'icon_width' => 'nullable|integer|min:16|max:120',
            'open_new_tab' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'icon_upload' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ]);
        $data['open_new_tab'] = $request->boolean('open_new_tab');
        $data['is_active'] = $request->boolean('is_active');
        $data['icon_width'] = $data['icon_width'] ?? 40;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['icon_upload']);
        return $data;
    }
}
