@extends('admin.layout')
@section('title', ($button->exists ? 'Edit' : 'Tambah').' Tombol')
@section('header', ($button->exists ? 'Edit' : 'Tambah').' Tombol — '.$page->name)
@section('subheader', 'Pilih: masuk ke sub-halaman (✓ submenu) atau link luar (WA / web lain)')

@section('content')
<form method="POST" action="{{ $button->exists ? route('admin.pages.buttons.update', [$page, $button]) : route('admin.pages.buttons.store', $page) }}" enctype="multipart/form-data" class="max-w-2xl bg-white rounded-3xl border p-6 space-y-4">
    @csrf @if($button->exists) @method('PUT') @endif
    <div><label class="text-xs font-bold text-slate-500">JUDUL TOMBOL</label><input name="title" value="{{ old('title', $button->title) }}" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border" placeholder="cth: Info order di WA"></div>

    <div class="rounded-2xl border p-4 space-y-3 bg-slate-50">
        <label class="text-xs font-bold text-slate-500">JENIS TUJUAN</label>
        @php
            $curUrl = old('url', $button->url);
            $curSlug = null;
            if ($curUrl && str_starts_with($curUrl, '/') && !str_starts_with($curUrl, '//')) {
                $p = trim(parse_url($curUrl, PHP_URL_PATH) ?? $curUrl, '/');
                if ($p && !str_contains($p, '/') && preg_match('/^[a-z0-9\-]+$/', $p)) $curSlug = $p;
            }
            $isSubmenu = $curSlug && ($pages ?? collect())->contains('slug', $curSlug);
        @endphp
        <div class="grid sm:grid-cols-2 gap-2" x-data="{ mode: '{{ $isSubmenu ? 'submenu' : 'link' }}' }">
            <label class="cursor-pointer"><input type="radio" name="__mode" value="submenu" class="peer sr-only" x-model="mode" {{ $isSubmenu ? 'checked' : '' }}>
                <div class="rounded-2xl border-2 p-3 peer-checked:border-emerald-500 peer-checked:bg-emerald-50"><div class="font-bold text-sm">✓ Submenu <span class="text-emerald-600">(punya isi lagi)</span></div><div class="text-xs text-slate-500">Contoh: Acara TV → Part 1, Part 2</div></div></label>
            <label class="cursor-pointer"><input type="radio" name="__mode" value="link" class="peer sr-only" x-model="mode" {{ !$isSubmenu ? 'checked' : '' }}>
                <div class="rounded-2xl border-2 p-3 peer-checked:border-sky-500 peer-checked:bg-sky-50"><div class="font-bold text-sm">↗ Link luar</div><div class="text-xs text-slate-500">Contoh: WA, GDrive, TikTok, berita</div></div></label>
            <div class="sm:col-span-2">
                <div x-show="mode === 'submenu'">
                    <label class="text-xs font-bold text-slate-500">PILIH SUB-HALAMAN</label>
                    <select id="submenuSelect" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white">
                        <option value="">— pilih halaman —</option>
                        @foreach($pages ?? [] as $p)
                            <option value="/{{ $p->slug }}" {{ ('/'.$curSlug) === ('/'.$p->slug) ? 'selected' : '' }}>/{{ $p->slug }} — {{ $p->name }} ({{ $p->buttons_count ?? $p->buttons->count() }} tombol)</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Belum ada halamannya? <a href="{{ route('admin.pages.create', ['parent_id' => $page->id]) }}" class="text-indigo-600 font-bold hover:underline">Buat sub-halaman baru dulu →</a></p>
                </div>
                <div x-show="mode === 'link'">
                    <label class="text-xs font-bold text-slate-500">URL TUJUAN</label>
                    <input id="linkInput" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white" placeholder="https://wa.me/... atau https://...">
                    <p class="text-[11px] text-slate-400 mt-1">Tempel link WA / Google Drive / TikTok / artikel berita di sini.</p>
                </div>
                <label class="text-xs font-bold text-slate-500 mt-3 block">URL TERSIMPAN (otomatis terisi)</label>
                <input name="url" id="urlField" value="{{ old('url', $button->url) }}" required class="mt-1 w-full px-4 py-2.5 rounded-2xl border font-mono text-sm bg-white" placeholder="/nama-halaman atau https://...">
            </div>
        </div>
    </div>

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
    <div class="flex gap-2"><button class="px-6 py-3 rounded-2xl bg-rose-500 text-white font-bold">Simpan</button><a href="{{ route('admin.pages.index', ['focus' => $page->id]) }}" class="px-6 py-3 rounded-2xl bg-slate-100 font-bold">Batal</a></div>
</form>
@push('scripts')
<script>
(function () {
    var sub = document.getElementById('submenuSelect');
    var link = document.getElementById('linkInput');
    var url = document.getElementById('urlField');
    if (sub) sub.addEventListener('change', function () { if (sub.value) url.value = sub.value; });
    if (link) link.addEventListener('input', function () { if (link.value.trim()) url.value = link.value.trim(); });
})();
</script>
@endpush
@endsection
