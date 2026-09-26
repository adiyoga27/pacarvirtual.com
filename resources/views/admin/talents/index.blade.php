@extends('admin.layout')
@section('title', 'Talent')
@section('header', 'Master Talent')
@section('subheader', 'Nama + metode & nomor pembayaran — otomatis jadi pilihan di form order')

@section('content')
{{-- Statistik ringkas --}}
<div class="grid sm:grid-cols-3 gap-3 mb-4">
    <div class="bg-white rounded-3xl border p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center"><i class="bi bi-person-badge text-lg"></i></div>
        <div><div class="text-2xl font-black">{{ $stats['total'] }}</div><div class="text-xs text-slate-500">Total talent</div></div>
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

<div class="grid xl:grid-cols-3 gap-4" x-data="{ nama: '', norek: '' }">
    {{-- Form tambah + preview langsung --}}
    <div class="space-y-4">
        <div class="bg-white rounded-3xl border p-6 h-fit">
            <h3 class="font-bold mb-1">Tambah Talent</h3>
            <p class="text-xs text-slate-500 mb-4">Metode & nomor dipakai untuk bayar komisi.</p>
            <form method="POST" action="{{ route('admin.talents.store') }}" class="space-y-3">
                @csrf
                <div><label class="text-xs font-bold text-slate-500">NAMA TALENT</label>
                    <input name="name" required maxlength="120" placeholder="cth: Cantika" value="{{ old('name') }}" x-model="nama" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                <div><label class="text-xs font-bold text-slate-500">METODE PEMBAYARAN</label>
                    <select name="payment_method_id" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white">
                        <option value="">— pilih metode —</option>
                        @foreach($paymentMethods as $pm)<option value="{{ $pm->id }}" {{ old('payment_method_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>@endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Diambil dari master <a href="{{ route('admin.payment-methods.index') }}" class="text-indigo-600 font-bold hover:underline">Metode Pembayaran</a>.</p></div>
                <div><label class="text-xs font-bold text-slate-500">NOMOR PEMBAYARAN</label>
                    <input name="account_number" maxlength="100" placeholder="cth: 081234567890" value="{{ old('account_number') }}" x-model="norek" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                <button class="w-full py-2.5 rounded-2xl bg-rose-500 text-white font-bold hover:bg-rose-600">Tambah Talent</button>
            </form>
        </div>

        {{-- Preview --}}
        <div class="bg-slate-900 rounded-3xl p-6 text-white">
            <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-3">Preview</div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-400 flex items-center justify-center font-black text-lg" x-text="(nama || 'T').trim().charAt(0).toUpperCase()">T</div>
                <div class="min-w-0">
                    <div class="font-bold truncate" x-text="nama || 'Nama talent'">Nama talent</div>
                    <div class="text-xs text-slate-400 font-mono truncate" x-text="norek || 'nomor pembayaran'">nomor pembayaran</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar talent --}}
    <div class="xl:col-span-2 space-y-3">
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Cari talent / nomor..." class="flex-1 px-4 py-2.5 rounded-2xl border bg-white text-sm">
            <button class="px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-bold">Cari</button>
            @if($q)<a href="{{ route('admin.talents.index') }}" class="px-4 py-2.5 rounded-2xl bg-white border text-sm font-bold">Reset</a>@endif
        </form>

        @forelse($talents as $t)
        <div class="bg-white rounded-3xl border overflow-hidden" x-data="{ open: false }">
            <div class="p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl {{ $t->is_active ? 'bg-gradient-to-br from-violet-500 to-purple-400' : 'bg-slate-200' }} flex items-center justify-center font-black text-white text-lg shrink-0">{{ strtoupper(substr($t->name, 0, 1)) }}</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold truncate">{{ $t->name }}
                        @if($t->is_active)<span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">AKTIF</span>
                        @else<span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full bg-slate-200 text-slate-500">OFF</span>@endif
                    </div>
                    <div class="text-sm font-mono mt-0.5 truncate">{{ $t->paymentMethod?->name ?? '—' }} • {{ $t->account_number ?: 'no. belum diisi' }}</div>
                </div>
                <div class="flex flex-col sm:flex-row gap-1.5 shrink-0">
                    <button @click="open = !open" class="px-3 py-2 text-xs font-bold rounded-xl bg-amber-400 hover:bg-amber-500 whitespace-nowrap"><i class="bi bi-pencil-square"></i> Edit</button>
                    <form method="POST" action="{{ route('admin.talents.destroy', $t) }}" onsubmit="return confirm('Hapus talent ini?')" class="inline">@csrf @method('DELETE')<button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700 hover:bg-rose-200">Hapus</button></form>
                </div>
            </div>
            <div x-show="open" x-cloak class="border-t bg-amber-50/60 p-5">
                <form method="POST" action="{{ route('admin.talents.update', $t) }}" class="grid md:grid-cols-2 gap-3">@csrf @method('PUT')
                    <div><label class="text-xs font-bold text-slate-500">NAMA</label>
                        <input name="name" required maxlength="120" value="{{ $t->name }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div><label class="text-xs font-bold text-slate-500">NOMOR PEMBAYARAN</label>
                        <input name="account_number" maxlength="100" value="{{ $t->account_number }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div><label class="text-xs font-bold text-slate-500">METODE PEMBAYARAN</label>
                        <select name="payment_method_id" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white">
                            <option value="">— pilih metode —</option>
                            @foreach($paymentMethods as $pm)<option value="{{ $pm->id }}" {{ $t->payment_method_id == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>@endforeach
                        </select></div>
                    <div class="flex items-end pb-2 text-sm">
                        <label class="flex items-center gap-2 font-semibold"><input type="checkbox" name="is_active" value="1" {{ $t->is_active ? 'checked' : '' }} class="rounded w-5 h-5"> Aktif</label>
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button class="px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700"><i class="bi bi-check-lg"></i> Simpan perubahan</button>
                        <button type="button" @click="open = false" class="px-5 py-2.5 rounded-2xl bg-white border text-xs font-bold">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl border p-8 text-center text-slate-500">Belum ada talent. Tambahkan dulu lewat form di samping.</div>
        @endforelse
    </div>
</div>
@endsection
