@extends('admin.layout')
@section('title', 'Layanan')
@section('header', 'Master Layanan')
@section('subheader', 'Harga jual + komisi talent per layanan — otomatis dipakai di form order & laporan')

@section('content')
{{-- Statistik ringkas --}}
<div class="grid sm:grid-cols-3 gap-3 mb-4">
    <div class="bg-white rounded-3xl border p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center"><i class="bi bi-tags-fill text-lg"></i></div>
        <div><div class="text-2xl font-black">{{ $stats['total'] }}</div><div class="text-xs text-slate-500">Total layanan</div></div>
    </div>
    <div class="bg-white rounded-3xl border p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="bi bi-check-circle-fill text-lg"></i></div>
        <div><div class="text-2xl font-black">{{ $stats['aktif'] }}</div><div class="text-xs text-slate-500">Aktif (muncul di form order)</div></div>
    </div>
    <div class="bg-white rounded-3xl border p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl bg-slate-200 text-slate-500 flex items-center justify-center"><i class="bi bi-pause-circle text-lg"></i></div>
        <div><div class="text-2xl font-black">{{ $stats['nonaktif'] }}</div><div class="text-xs text-slate-500">Nonaktif</div></div>
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-4" x-data="{ nama: '', harga: 0, komisi: 0, tipe: 'nominal' }">
    {{-- Form tambah + preview langsung --}}
    <div class="space-y-4">
        <div class="bg-white rounded-3xl border p-6 h-fit">
            <h3 class="font-bold mb-1">Tambah Layanan</h3>
            <p class="text-xs text-slate-500 mb-4">Komisi bisa <b>nominal (Rp)</b> atau <b>persen (%)</b> dari harga jual.</p>
            <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-3">
                @csrf
                <div><label class="text-xs font-bold text-slate-500">NAMA LAYANAN</label>
                    <input name="name" required maxlength="120" placeholder="cth: Video Call" value="{{ old('name') }}" x-model="nama" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                <div><label class="text-xs font-bold text-slate-500">HARGA JUAL DEFAULT (Rp)</label>
                    <input type="number" name="default_price" required min="0" placeholder="cth: 100000" value="{{ old('default_price') }}" x-model.number="harga" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                <div class="rounded-2xl border p-3 bg-slate-50 space-y-2">
                    <label class="text-xs font-bold text-slate-500">KOMISI TALENT</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer"><input type="radio" name="komisi_tipe" value="nominal" class="peer sr-only" x-model="tipe" {{ old('komisi_tipe', 'nominal') === 'nominal' ? 'checked' : '' }}>
                            <div class="text-center text-sm font-bold rounded-xl border-2 p-2 peer-checked:border-emerald-500 peer-checked:bg-emerald-50">Rp Nominal</div></label>
                        <label class="cursor-pointer"><input type="radio" name="komisi_tipe" value="persen" class="peer sr-only" x-model="tipe" {{ old('komisi_tipe') === 'persen' ? 'checked' : '' }}>
                            <div class="text-center text-sm font-bold rounded-xl border-2 p-2 peer-checked:border-sky-500 peer-checked:bg-sky-50">% Persen</div></label>
                    </div>
                    <input type="number" name="komisi_talent" min="0" placeholder="0 = tanpa komisi" value="{{ old('komisi_talent', 0) }}" x-model.number="komisi" class="w-full px-4 py-2.5 rounded-2xl border bg-white">
                    <p class="text-[11px] text-slate-400" x-show="tipe === 'persen'">Maksimal 100%. Dihitung dari harga jual.</p>
                </div>
                <div><label class="text-xs font-bold text-slate-500">DESKRIPSI (opsional)</label>
                    <textarea name="description" rows="2" placeholder="cth: Video call 15 menit via WA" class="mt-1 w-full px-4 py-2.5 rounded-2xl border">{{ old('description') }}</textarea></div>
                <button class="w-full py-2.5 rounded-2xl bg-rose-500 text-white font-bold hover:bg-rose-600">Tambah Layanan</button>
            </form>
        </div>

        {{-- Preview --}}
        <div class="bg-slate-900 rounded-3xl p-6 text-white">
            <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-3">Preview</div>
            <div class="font-bold text-lg" x-text="nama || 'Nama layanan'">Nama layanan</div>
            <div class="text-sm text-slate-300 mt-1">Harga: <b x-text="'Rp ' + Number(harga || 0).toLocaleString('id-ID')"></b></div>
            <div class="text-sm text-slate-300">Komisi: <b x-text="tipe === 'persen' ? (komisi || 0) + '%' : 'Rp ' + Number(komisi || 0).toLocaleString('id-ID')"></b></div>
            <div class="text-sm mt-1">Margin: <b class="text-emerald-400" x-text="'Rp ' + Math.max(0, (tipe === 'persen' ? (harga || 0) * (1 - (komisi || 0) / 100) : (harga || 0) - (komisi || 0))).toLocaleString('id-ID')"></b></div>
        </div>
    </div>

    {{-- Daftar layanan --}}
    <div class="xl:col-span-2 space-y-3">
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Cari layanan..." class="flex-1 px-4 py-2.5 rounded-2xl border bg-white text-sm">
            <button class="px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-bold">Cari</button>
            @if($q)<a href="{{ route('admin.services.index') }}" class="px-4 py-2.5 rounded-2xl bg-white border text-sm font-bold">Reset</a>@endif
        </form>

        @forelse($services as $s)
        <div class="bg-white rounded-3xl border overflow-hidden" x-data="{ open: false, tipe: '{{ $s->komisi_tipe }}' }">
            <div class="p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl {{ $s->is_active ? 'bg-gradient-to-br from-rose-500 to-pink-400' : 'bg-slate-200' }} flex items-center justify-center font-black text-white shrink-0"><i class="bi bi-tag-fill"></i></div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold truncate">{{ $s->name }}
                        @if($s->is_active)<span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">AKTIF</span>
                        @else<span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full bg-slate-200 text-slate-500">OFF</span>@endif
                    </div>
                    <div class="text-sm mt-0.5">Harga <b>{{ rupiah($s->default_price) }}</b>
                        <span class="text-slate-400">•</span> Komisi <b class="text-indigo-600">{{ $s->komisiLabel() }}</b>
                        <span class="text-slate-400">•</span> Margin <b class="text-emerald-600">{{ rupiah($s->marginDefault()) }}</b>
                    </div>
                    @if($s->description)<div class="text-xs text-slate-400 truncate mt-0.5">{{ $s->description }}</div>@endif
                </div>
                <div class="flex flex-col sm:flex-row gap-1.5 shrink-0">
                    <button @click="open = !open" class="px-3 py-2 text-xs font-bold rounded-xl bg-amber-400 hover:bg-amber-500 whitespace-nowrap"><i class="bi bi-pencil-square"></i> Edit</button>
                    <form method="POST" action="{{ route('admin.services.destroy', $s) }}" onsubmit="return confirm('Hapus layanan ini?')" class="inline">@csrf @method('DELETE')<button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700 hover:bg-rose-200">Hapus</button></form>
                </div>
            </div>
            <div x-show="open" x-cloak class="border-t bg-amber-50/60 p-5">
                <form method="POST" action="{{ route('admin.services.update', $s) }}" class="grid md:grid-cols-2 gap-3">@csrf @method('PUT')
                    <div><label class="text-xs font-bold text-slate-500">NAMA</label>
                        <input name="name" required maxlength="120" value="{{ $s->name }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div><label class="text-xs font-bold text-slate-500">HARGA JUAL (Rp)</label>
                        <input type="number" name="default_price" required min="0" value="{{ $s->default_price }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div><label class="text-xs font-bold text-slate-500">TIPE KOMISI</label>
                        <select name="komisi_tipe" x-model="tipe" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white">
                            <option value="nominal" {{ $s->komisi_tipe === 'nominal' ? 'selected' : '' }}>Rp Nominal</option>
                            <option value="persen" {{ $s->komisi_tipe === 'persen' ? 'selected' : '' }}>% Persen</option>
                        </select></div>
                    <div><label class="text-xs font-bold text-slate-500">KOMISI TALENT <span x-text="tipe === 'persen' ? '(%, maks 100)' : '(Rp)'"></span></label>
                        <input type="number" name="komisi_talent" min="0" value="{{ $s->komisi_talent }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div class="md:col-span-2"><label class="text-xs font-bold text-slate-500">DESKRIPSI</label>
                        <textarea name="description" rows="2" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white">{{ $s->description }}</textarea></div>
                    <div class="md:col-span-2 flex items-center gap-2 text-sm">
                        <label class="flex items-center gap-2 font-semibold"><input type="checkbox" name="is_active" value="1" {{ $s->is_active ? 'checked' : '' }} class="rounded w-5 h-5"> Aktif (tampil di form order)</label>
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button class="px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700"><i class="bi bi-check-lg"></i> Simpan perubahan</button>
                        <button type="button" @click="open = false" class="px-5 py-2.5 rounded-2xl bg-white border text-xs font-bold">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl border p-8 text-center text-slate-500">Tidak ada layanan yang cocok dengan “{{ $q }}”.</div>
        @endforelse
    </div>
</div>
@endsection
