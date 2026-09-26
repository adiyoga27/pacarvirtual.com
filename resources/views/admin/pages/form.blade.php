@extends('admin.layout')
@section('title', $page->exists ? 'Edit Halaman' : 'Halaman Baru')
@section('header', $page->exists ? 'Edit Halaman: '.$page->name : 'Halaman Baru')
@section('subheader', 'Konten, background, icon & SEO semuanya dinamis dari database')

@section('content')
@if(!$page->exists && !empty($parent))
<div class="mb-4 p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-sm text-indigo-900">
    <b><i class="bi bi-folder-plus"></i> Membuat sub-halaman di dalam “{{ $parent->name }}”.</b>
    Setelah disimpan, tombol <b>/{{ old('slug', $page->slug) ?: 'slug-baru' }}</b> otomatis muncul di “{{ $parent->name }}”. Selanjutnya klik <b>Masuk →</b> dari struktur untuk mengisi link di dalamnya.
    @if(request('from_button_id'))<br>Setelah jadi, tombol yang tadi juga otomatis ditautkan ke halaman ini. ✓@endif
</div>
@endif
<form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" enctype="multipart/form-data" class="grid xl:grid-cols-3 gap-4">
    @csrf @if($page->exists) @method('PUT') @endif
    @if(!$page->exists && !empty($parent))<input type="hidden" name="parent_id" value="{{ $parent->id }}">@endif
    @if(!$page->exists && request('from_button_id'))<input type="hidden" name="from_button_id" value="{{ request('from_button_id') }}">@endif
    <div class="xl:col-span-2 space-y-4">
        <div class="bg-white rounded-3xl border p-6">
            <h3 class="font-bold mb-4">Konten Halaman</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-slate-500">SLUG (URL)</label><input name="slug" value="{{ old('slug', $page->slug) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border" placeholder="cth: pricelist" required><p class="text-[11px] text-slate-400 mt-1">URL: /<span id="slugPreview">{{ old('slug', $page->slug) }}</span></p></div>
                <div><label class="text-xs font-bold text-slate-500">NAMA HALAMAN</label><input name="name" value="{{ old('name', $page->name) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border" required></div>
                <div><label class="text-xs font-bold text-slate-500">JUDUL HEADER</label><input name="header_title" value="{{ old('header_title', $page->header_title) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border" required></div>
                <div><label class="text-xs font-bold text-slate-500">SUBTITLE</label><input name="subtitle" value="{{ old('subtitle', $page->subtitle) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border" placeholder="opsional"></div>
                <div><label class="text-xs font-bold text-slate-500">FOOTER</label><input name="footer_text" value="{{ old('footer_text', $page->footer_text) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border" placeholder="kosongkan = pakai global"></div>
                <div><label class="text-xs font-bold text-slate-500">URUTAN</label><input type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order ?? 0) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
            </div>
            <div class="flex flex-wrap gap-4 mt-4 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="show_logo" value="1" {{ old('show_logo', $page->show_logo ?? true) ? 'checked' : '' }} class="rounded"> Tampilkan logo</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="show_back_button" value="1" {{ old('show_back_button', $page->show_back_button) ? 'checked' : '' }} class="rounded"> Tombol kembali</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active ?? true) ? 'checked' : '' }} class="rounded"> Aktif</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="use_global_background" value="1" {{ old('use_global_background', $page->use_global_background ?? true) ? 'checked' : '' }} class="rounded"> Pakai background global</label>
            </div>
        </div>

        <div class="bg-white rounded-3xl border p-6">
            <h3 class="font-bold mb-4">Background & Logo Khusus Halaman Ini</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-slate-500">TIPE BACKGROUND</label>
                    <select name="background_type" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"><option value="image" {{ old('background_type', $page->background_type) === 'image' ? 'selected' : '' }}>Gambar</option><option value="color" {{ old('background_type', $page->background_type) === 'color' ? 'selected' : '' }}>Warna</option><option value="gradient" {{ old('background_type', $page->background_type) === 'gradient' ? 'selected' : '' }}>Gradient</option></select></div>
                <div><label class="text-xs font-bold text-slate-500">WARNA / GRADIENT</label><input name="background_color" value="{{ old('background_color', $page->background_color) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border" placeholder="#1a0b2e"><input name="background_gradient" value="{{ old('background_gradient', $page->background_gradient) }}" class="mt-2 w-full px-4 py-2.5 rounded-2xl border" placeholder="linear-gradient(...)"></div>
                <div><label class="text-xs font-bold text-slate-500">UPLOAD BACKGROUND</label><input type="file" name="background_image" accept="image/*" class="mt-1 w-full text-sm">@if($page->background_image)<img src="{{ pv_asset($page->background_image) }}" class="h-16 rounded-xl mt-2" alt="">@endif</div>
                <div><label class="text-xs font-bold text-slate-500">LOGO KHUSUS (kosongkan = global)</label><input type="file" name="logo_upload" accept="image/*" class="mt-1 w-full text-sm">@if($page->logo_path)<img src="{{ pv_asset($page->logo_path) }}" class="h-16 rounded-full mt-2" alt="">@endif</div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border p-6">
            <h3 class="font-bold mb-4">SEO Halaman Ini (dynamic)</h3>
            <div class="grid gap-4">
                <div><label class="text-xs font-bold text-slate-500">META TITLE</label><input name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                <div><label class="text-xs font-bold text-slate-500">META DESCRIPTION</label><textarea name="meta_description" rows="2" class="mt-1 w-full px-4 py-2.5 rounded-2xl border">{{ old('meta_description', $page->meta_description) }}</textarea></div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div><label class="text-xs font-bold text-slate-500">KEYWORDS</label><input name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                    <div><label class="text-xs font-bold text-slate-500">AUTHOR</label><input name="meta_author" value="{{ old('meta_author', $page->meta_author) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                </div>
                <div><label class="text-xs font-bold text-slate-500">OG IMAGE</label><input type="file" name="og_image_upload" accept="image/*" class="mt-1 w-full text-sm">@if($page->og_image)<img src="{{ pv_asset($page->og_image) }}" class="h-16 rounded-xl mt-2" alt="">@endif</div>
            </div>
        </div>
    </div>
    <div class="space-y-4">
        <div class="bg-slate-900 text-white rounded-3xl p-6">
            <h3 class="font-bold">Publikasi</h3>
            <p class="text-xs text-slate-400 mb-4">Perubahan langsung tampil di website.</p>
            <button class="w-full py-3 rounded-2xl bg-rose-500 font-bold hover:bg-rose-600">Simpan & Isi Tombol →</button>
            @if($page->exists)<a href="{{ route('page.show', $page->slug) }}" target="_blank" class="block text-center mt-2 py-3 rounded-2xl bg-white/10 font-bold hover:bg-white/20">Lihat Halaman</a>@endif
            <a href="{{ route('admin.pages.index', $page->exists ? ['focus' => $page->id] : []) }}" class="block text-center mt-2 text-xs text-slate-400 hover:text-white">← Kembali ke struktur</a>
        </div>
        @if($page->exists)
        <div class="bg-white rounded-3xl border p-6">
            <h3 class="font-bold mb-2">Tombol ({{ $page->buttons->count() }})</h3>
            <a href="{{ route('admin.pages.buttons.index', $page) }}" class="block text-center py-2.5 rounded-2xl bg-indigo-600 text-white text-sm font-bold">Kelola Tombol →</a>
        </div>
        @endif
    </div>
</form>
@endsection
