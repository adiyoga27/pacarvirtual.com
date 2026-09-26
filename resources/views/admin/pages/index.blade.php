@extends('admin.layout')
@section('title', 'Halaman')
@section('header', 'Halaman & Tombol Link')
@section('subheader', 'Semua halaman link-in-bio (pengganti file PHP lama) — SEO tiap halaman bisa diubah')

@section('content')
<div class="flex justify-between items-center mb-4">
    <p class="text-sm text-slate-500">{{ $pages->count() }} halaman aktif. Klik <b>Kelola Tombol</b> untuk ubah isi tiap halaman.</p>
    <a href="{{ route('admin.pages.create') }}" class="px-4 py-2.5 rounded-2xl bg-rose-500 text-white text-sm font-bold hover:bg-rose-600"><i class="bi bi-plus-lg"></i> Halaman Baru</a>
</div>
<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($pages as $p)
    <div class="bg-white rounded-3xl border shadow-sm p-5">
        <div class="flex items-start justify-between">
            <div>
                <div class="font-bold">{{ $p->name }}</div>
                <div class="text-xs text-slate-400 font-mono">/{{ $p->slug }} • {{ $p->buttons_count }} tombol</div>
            </div>
            <span class="text-[11px] font-bold px-2 py-1 rounded-full {{ $p->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">{{ $p->is_active ? 'AKTIF' : 'NONAKTIF' }}</span>
        </div>
        <div class="text-sm text-slate-500 mt-2 truncate">{{ $p->meta_title ?: $p->header_title }}</div>
        <div class="flex flex-wrap gap-2 mt-4">
            <a href="{{ route('page.show', $p->slug) }}" target="_blank" class="px-3 py-2 text-xs font-bold rounded-xl bg-slate-100 hover:bg-slate-200"><i class="bi bi-eye"></i> Lihat</a>
            <a href="{{ route('admin.pages.buttons.index', $p) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"><i class="bi bi-menu-button-wide"></i> Kelola Tombol</a>
            <a href="{{ route('admin.pages.edit', $p) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-amber-400 hover:bg-amber-500"><i class="bi bi-pencil"></i> SEO & Tampilan</a>
            @if($p->slug !== 'home')
            <form method="POST" action="{{ route('admin.pages.destroy', $p) }}" onsubmit="return confirm('Hapus halaman ini?')">@csrf @method('DELETE')
                <button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700 hover:bg-rose-200"><i class="bi bi-trash"></i></button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
