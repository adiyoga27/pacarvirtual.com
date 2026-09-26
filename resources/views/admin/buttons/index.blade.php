@extends('admin.layout')
@section('title', 'Tombol - '.$page->name)
@section('header', 'Tombol: '.$page->name)
@section('subheader', 'Atur urutan, icon, link & aktif/nonaktif per tombol')

@section('content')
<div class="flex justify-between items-center mb-4">
    <a href="{{ route('admin.pages.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Semua halaman</a>
    <a href="{{ route('admin.pages.buttons.create', $page) }}" class="px-4 py-2.5 rounded-2xl bg-rose-500 text-white text-sm font-bold"><i class="bi bi-plus-lg"></i> Tambah Tombol</a>
</div>
<div class="bg-white rounded-3xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase text-slate-400 border-b"><th class="p-4">Tombol</th><th>URL</th><th class="text-center">Icon</th><th class="text-center">Klik</th><th class="text-center">Status</th><th class="text-right p-4">Aksi</th></tr></thead>
        <tbody>
            @forelse($buttons as $b)
            <tr class="border-b last:border-0 hover:bg-slate-50">
                <td class="p-4"><div class="flex items-center gap-3">@if($b->icon_path)<img src="{{ pv_asset($b->icon_path) }}" class="w-9 h-9 rounded-xl object-cover bg-slate-100" alt="">@endif<div><div class="font-bold">{{ $b->title }}</div><div class="text-xs text-slate-400">urutan: {{ $b->sort_order }} • {{ $b->open_new_tab ? 'tab baru' : 'tab sama' }}</div></div></div></td>
                <td class="max-w-xs truncate text-slate-500 text-xs">{{ $b->url }}</td>
                <td class="text-center text-xs">{{ $b->icon_width }}px</td>
                <td class="text-center font-black">{{ number_format($b->click_count) }}</td>
                <td class="text-center"><span class="text-[11px] font-bold px-2 py-1 rounded-full {{ $b->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">{{ $b->is_active ? 'AKTIF' : 'OFF' }}</span></td>
                <td class="p-4 text-right whitespace-nowrap">
                    <a href="{{ route('admin.pages.buttons.edit', [$page, $b]) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-amber-400">Edit</a>
                    <form method="POST" action="{{ route('admin.pages.buttons.destroy', [$page, $b]) }}" class="inline" onsubmit="return confirm('Hapus tombol?')">@csrf @method('DELETE')<button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700">Hapus</button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-8 text-center text-slate-400">Belum ada tombol. Tambahkan yang pertama.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
