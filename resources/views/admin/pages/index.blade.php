@extends('admin.layout')
@section('title', 'Halaman')
@section('header', 'Struktur Halaman')
@section('subheader', 'Mulai dari Homepage → klik isi tiap menu → buat sub-halaman / isi link')

@section('content')
{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-sm mb-4 flex-wrap">
    <span class="text-slate-400 text-xs font-bold uppercase tracking-wide">Posisi:</span>
    @foreach($breadcrumbs as $i => $crumb)
        @if($i > 0)<i class="bi bi-chevron-right text-slate-300 text-xs"></i>@endif
        @if($i === count($breadcrumbs) - 1)
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 text-white font-bold">{{ $crumb->name }}</span>
        @else
            <a href="{{ route('admin.pages.index', ['focus' => $crumb->id]) }}" class="px-3 py-1.5 rounded-xl bg-white border hover:border-slate-900 font-semibold">{{ $crumb->name }}</a>
        @endif
    @endforeach
    @if($focus && $home && $focus->id !== $home->id)
        <a href="{{ route('admin.pages.index') }}" class="ml-2 text-xs text-slate-500 hover:text-slate-900">⌂ Kembali ke Homepage</a>
    @endif
</nav>

@if(!$focus)
    <div class="bg-white rounded-3xl border p-8 text-center text-slate-500">Belum ada halaman. Buat Homepage dulu.</div>
@else
<div class="grid xl:grid-cols-3 gap-4">
    {{-- Kolom kiri: isi halaman yang sedang dibuka --}}
    <div class="xl:col-span-2 space-y-4">
        {{-- Info halaman fokus --}}
        <div class="bg-slate-900 text-white rounded-3xl p-6 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                    {{ $focus->id === $home?->id ? 'HOMEPAGE — tampil di /' : 'SUB-HALAMAN — tampil di /'.$focus->slug }}
                </div>
                <div class="text-xl font-black mt-1">{{ $focus->name }}</div>
                <div class="text-xs text-slate-400 font-mono mt-1">/{{ $focus->slug }} • {{ $focus->buttons->count() }} tombol • {{ $focus->is_active ? 'AKTIF' : 'NONAKTIF' }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('page.show', $focus->slug) }}" target="_blank" class="px-3 py-2 text-xs font-bold rounded-xl bg-white/10 hover:bg-white/20"><i class="bi bi-eye"></i> Lihat</a>
                <a href="{{ route('admin.pages.edit', $focus) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-white/10 hover:bg-white/20"><i class="bi bi-pencil"></i> SEO & Tampilan</a>
            </div>
        </div>

        {{-- Aksi tambah --}}
        <div class="grid sm:grid-cols-2 gap-3">
            <a href="{{ route('admin.pages.create', ['parent_id' => $focus->id]) }}" class="bg-white rounded-3xl border-2 border-dashed border-indigo-300 p-5 hover:border-indigo-600 hover:bg-indigo-50/50 text-left">
                <div class="font-black text-indigo-700"><i class="bi bi-folder-plus"></i> Buat Sub-Halaman Baru</div>
                <div class="text-xs text-slate-500 mt-1">Contoh: dari Homepage buat “Pricelist”. Otomatis muncul tombolnya di sini. Lalu klik lagi untuk isi link di dalamnya.</div>
            </a>
            <a href="{{ route('admin.pages.buttons.create', $focus) }}" class="bg-white rounded-3xl border-2 border-dashed border-rose-300 p-5 hover:border-rose-500 hover:bg-rose-50/50 text-left">
                <div class="font-black text-rose-600"><i class="bi bi-link-45deg"></i> Tambah Tombol Link</div>
                <div class="text-xs text-slate-500 mt-1">Untuk link luar (WA, GDrive, TikTok, berita). Kalau link ke halaman sendiri, pilih slug /nama-halaman.</div>
            </a>
        </div>

        {{-- Daftar tombol = daftar isi halaman --}}
        <div class="bg-white rounded-3xl border shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="font-black">Isi “{{ $focus->name }}” <span class="text-slate-400 font-normal text-sm">— klik untuk masuk ke dalam</span></h3>
                <span class="text-xs text-slate-400">{{ $focus->buttons->count() }} item</span>
            </div>
            <div class="divide-y">
                @forelse($focus->buttons as $i => $b)
                    @php $meta = $buttonMeta[$b->id] ?? ['type' => 'external', 'slug' => null, 'linkedPage' => null]; @endphp
                    <div class="p-4 flex items-center gap-3 hover:bg-slate-50">
                        <span class="w-7 h-7 rounded-lg bg-slate-100 text-xs font-black flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                        @if($b->icon_path)
                            <img src="{{ pv_asset($b->icon_path) }}" class="w-10 h-10 rounded-xl object-cover bg-slate-100 shrink-0" alt="">
                        @else
                            <span class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0"><i class="bi bi-link text-slate-400"></i></span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="font-bold truncate">{{ $b->title }}</div>
                            <div class="text-xs text-slate-400 font-mono truncate">{{ $b->url }}</div>
                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                @if($meta['type'] === 'submenu')
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700"><i class="bi bi-check-circle-fill"></i> Submenu ✓ — {{ $meta['linkedPage']->buttons_count }} tombol</span>
                                @elseif($meta['type'] === 'missing')
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700"><i class="bi bi-exclamation-triangle-fill"></i> /{{ $meta['slug'] }} belum ada halamannya</span>
                                @else
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-sky-100 text-sky-700"><i class="bi bi-box-arrow-up-right"></i> Link luar</span>
                                @endif
                                @if(!$b->is_active)<span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-500">OFF</span>@endif
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-1.5 shrink-0">
                            @if($meta['type'] === 'submenu')
                                <a href="{{ route('admin.pages.index', ['focus' => $meta['linkedPage']->id]) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-center">Masuk →</a>
                            @elseif($meta['type'] === 'missing')
                                <a href="{{ route('admin.pages.create', ['slug' => $meta['slug'], 'from_button_id' => $b->id]) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-500 text-white hover:bg-rose-600 text-center">+ Buat Halaman</a>
                            @endif
                            <a href="{{ route('admin.pages.buttons.edit', [$focus, $b]) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-slate-100 hover:bg-slate-200 text-center">Edit link</a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="text-3xl mb-2">📭</div>
                        <div class="font-bold">Halaman ini masih kosong</div>
                        <p class="text-sm text-slate-500 mt-1">Pilih di atas: buat <b>Sub-Halaman</b> (punya isi lagi di dalamnya) atau <b>Tombol Link</b> (langsung ke WA / web lain).</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Kolom kanan: peta semua halaman --}}
    <div class="space-y-4">
        <div class="bg-white rounded-3xl border p-5">
            <h3 class="font-black mb-1">Peta Halaman</h3>
            <p class="text-xs text-slate-500 mb-3">Semua halaman. Klik untuk melompat ke posisinya.</p>
            <div class="space-y-1.5 max-h-[420px] overflow-auto">
                @foreach($pages as $p)
                    <a href="{{ route('admin.pages.index', ['focus' => $p->id]) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm {{ $focus->id === $p->id ? 'bg-slate-900 text-white font-bold' : 'hover:bg-slate-100' }}">
                        <i class="bi {{ $p->slug === 'home' ? 'bi-house-fill' : 'bi-file-earmark' }} {{ $focus->id === $p->id ? 'text-white' : 'text-slate-400' }}"></i>
                        <span class="flex-1 truncate">{{ $p->name }}</span>
                        <span class="text-[11px] font-mono opacity-60">{{ $p->buttons_count }}</span>
                        @if($p->slug === 'home')<span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-emerald-500 text-white">HOME</span>@endif
                    </a>
                @endforeach
            </div>
            <a href="{{ route('admin.pages.create') }}" class="mt-3 block text-center py-2.5 rounded-2xl bg-rose-500 text-white text-sm font-bold hover:bg-rose-600"><i class="bi bi-plus-lg"></i> Halaman Baru (bebas)</a>
        </div>

        @if($orphans->count())
        <div class="bg-amber-50 rounded-3xl border border-amber-200 p-5">
            <h3 class="font-black text-amber-800 text-sm"><i class="bi bi-question-circle"></i> Belum ditautkan dari mana pun</h3>
            <p class="text-xs text-amber-700 mt-1 mb-3">Halaman ini tidak bisa dibuka pengunjung karena tidak ada tombol yang mengarah ke sana.</p>
            @foreach($orphans as $p)
                <div class="flex items-center gap-2 text-sm mb-1.5">
                    <span class="flex-1 truncate font-semibold">/{{ $p->slug }}</span>
                    <a href="{{ route('admin.pages.index', ['focus' => $p->id]) }}" class="text-xs font-bold text-indigo-700 hover:underline">Buka</a>
                </div>
            @endforeach
        </div>
        @endif

        <div class="bg-sky-50 rounded-3xl border border-sky-200 p-5 text-xs text-sky-900 leading-relaxed">
            <b>Cara pakai:</b><br>
            1. Mulai dari <b>Homepage</b> — isinya mis. Info order WA, Acara TV, Artikel Berita.<br>
            2. Klik <b>Masuk →</b> (tanda ✓ submenu) untuk mengisi halaman di dalamnya.<br>
            3. Klik <b>+ Buat Halaman</b> kalau tombol belum punya halaman.<br>
            4. Ulangi sampai semua tombol berisi link / submenu. Link luar cukup <b>Edit link</b>.
        </div>
    </div>
</div>
@endif
@endsection
