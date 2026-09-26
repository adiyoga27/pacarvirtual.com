@extends('admin.layout')
@section('title', ($button->exists ? 'Edit' : 'Tambah').' Tombol')
@section('header', ($button->exists ? 'Edit' : 'Tambah').' Tombol — '.$page->name)
@section('subheader', 'Icon, link tujuan, urutan & status tampil')

@section('content')
<form method="POST" action="{{ $button->exists ? route('admin.pages.buttons.update', [$page, $button]) : route('admin.pages.buttons.store', $page) }}" enctype="multipart/form-data" class="max-w-2xl bg-white rounded-3xl border p-6 space-y-4">
    @csrf @if($button->exists) @method('PUT') @endif
    <div><label class="text-xs font-bold text-slate-500">JUDUL TOMBOL</label><input name="title" value="{{ old('title', $button->title) }}" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border" placeholder="cth: Info order di WA"></div>
    <div><label class="text-xs font-bold text-slate-500">URL TUJUAN</label><input name="url" value="{{ old('url', $button->url) }}" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border" placeholder="/admin-contact atau https://..."><p class="text-[11px] text-slate-400 mt-1">Bisa slug internal (/pricelist) atau link eksternal (https://...). Klik tercatat otomatis.</p></div>
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="text-xs font-bold text-slate-500">UPLOAD ICON</label><input type="file" name="icon_upload" accept="image/*" class="mt-1 w-full text-sm">@if($button->icon_path)<img src="{{ pv_asset($button->icon_path) }}" class="h-14 mt-2 rounded-xl" alt="">@endif</div>
        <div><label class="text-xs font-bold text-slate-500">LEBAR ICON (px)</label><input type="number" name="icon_width" value="{{ old('icon_width', $button->icon_width ?? 40) }}" min="16" max="120" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        <div><label class="text-xs font-bold text-slate-500">URUTAN</label><input type="number" name="sort_order" value="{{ old('sort_order', $button->sort_order ?? 0) }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
        <div class="flex items-end gap-5 pb-2 text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="open_new_tab" value="1" {{ old('open_new_tab', $button->open_new_tab ?? true) ? 'checked' : '' }} class="rounded"> Tab baru</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $button->is_active ?? true) ? 'checked' : '' }} class="rounded"> Aktif</label>
        </div>
    </div>
    <div class="flex gap-2"><button class="px-6 py-3 rounded-2xl bg-rose-500 text-white font-bold">Simpan</button><a href="{{ route('admin.pages.buttons.index', $page) }}" class="px-6 py-3 rounded-2xl bg-slate-100 font-bold">Batal</a></div>
</form>
@endsection
