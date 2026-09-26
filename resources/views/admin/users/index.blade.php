@extends('admin.layout')
@section('title', 'Kelola Admin')
@section('header', 'Kelola Admin')
@section('subheader', 'Login bisa pakai username atau email — klik Edit untuk ubah profil & reset password')

@section('content')
@php
$gradients = [
    'from-rose-500 to-pink-400', 'from-indigo-500 to-sky-400', 'from-emerald-500 to-teal-400',
    'from-amber-500 to-orange-400', 'from-violet-500 to-purple-400', 'from-cyan-500 to-blue-400',
];
@endphp

{{-- Statistik ringkas --}}
<div class="grid sm:grid-cols-3 gap-3 mb-4">
    <div class="bg-white rounded-3xl border p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center"><i class="bi bi-people-fill text-lg"></i></div>
        <div><div class="text-2xl font-black">{{ $users->count() }}</div><div class="text-xs text-slate-500">Total admin</div></div>
    </div>
    <div class="bg-white rounded-3xl border p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="bi bi-person-check-fill text-lg"></i></div>
        <div><div class="font-bold truncate">{{ auth()->user()->name }}</div><div class="text-xs text-slate-500">{{ '@' . (auth()->user()->username ?? '-') }} • Anda login sebagai ini</div></div>
    </div>
    <div class="bg-white rounded-3xl border p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center"><i class="bi bi-activity text-lg"></i></div>
        <div><div class="font-bold">{{ $recentLogs->count() ? $recentLogs->first()->created_at->diffForHumans() : '-' }}</div><div class="text-xs text-slate-500">Aktivitas terakhir <a href="{{ route('admin.activity.index') }}" class="text-sky-600 font-bold hover:underline">lihat log →</a></div></div>
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-4" x-data="{ previewName: '', previewUser: '' }">
    {{-- Form tambah + preview langsung --}}
    <div class="space-y-4">
        <div class="bg-white rounded-3xl border p-6 h-fit">
            <h3 class="font-bold mb-1">Tambah Admin</h3>
            <p class="text-xs text-slate-500 mb-4">Username dipakai untuk login (boleh huruf, angka, _ , -).</p>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">@csrf
                <div><label class="text-xs font-bold text-slate-500">NAMA LENGKAP</label>
                    <input name="name" required placeholder="cth: Admin Utama" value="{{ old('name') }}" x-model="previewName" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                <div><label class="text-xs font-bold text-slate-500">USERNAME (untuk login)</label>
                    <input name="username" required minlength="3" placeholder="cth: adminutama" value="{{ old('username') }}" x-model="previewUser" class="mt-1 w-full px-4 py-2.5 rounded-2xl border font-mono"></div>
                <div><label class="text-xs font-bold text-slate-500">EMAIL (bisa juga untuk login)</label>
                    <input type="email" name="email" required placeholder="admin@pacarvirtual.com" value="{{ old('email') }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-xs font-bold text-slate-500">PASSWORD</label>
                        <input type="password" name="password" required minlength="6" placeholder="min 6" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                    <div><label class="text-xs font-bold text-slate-500">KONFIRMASI</label>
                        <input type="password" name="password_confirmation" required placeholder="ulangi" class="mt-1 w-full px-4 py-2.5 rounded-2xl border"></div>
                </div>
                <button class="w-full py-2.5 rounded-2xl bg-rose-500 text-white font-bold hover:bg-rose-600">Tambah Admin</button>
            </form>
        </div>

        {{-- Preview kartu --}}
        <div class="bg-slate-900 rounded-3xl p-6 text-white">
            <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold mb-3">Preview kartu admin</div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-400 flex items-center justify-center font-black text-lg" x-text="(previewName || 'A').trim().charAt(0).toUpperCase()">A</div>
                <div class="min-w-0">
                    <div class="font-bold truncate" x-text="previewName || 'Nama admin'">Nama admin</div>
                    <div class="text-xs text-slate-400 font-mono truncate">@<span x-text="previewUser || 'username'">username</span></div>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-3">Seperti ini tampilnya di daftar & di log sistem.</p>
        </div>
    </div>

    {{-- Daftar admin --}}
    <div class="xl:col-span-2 space-y-3">
        <form method="GET" class="flex gap-2">
            <input name="q" value="{{ $q }}" placeholder="Cari nama / username / email..." class="flex-1 px-4 py-2.5 rounded-2xl border bg-white text-sm">
            <button class="px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-bold">Cari</button>
            @if($q)<a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-2xl bg-white border text-sm font-bold">Reset</a>@endif
        </form>

        @forelse($users as $u)
        @php $g = $gradients[$u->id % count($gradients)]; @endphp
        <div class="bg-white rounded-3xl border overflow-hidden" x-data="{ open: false }">
            <div class="p-5 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $g }} flex items-center justify-center font-black text-white text-xl shrink-0">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold truncate">{{ $u->name }}
                        @if($u->id === auth()->id())<span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full bg-sky-100 text-sky-700">ANDA</span>@endif
                        <span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full {{ $u->role === 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">{{ strtoupper($u->role ?? 'admin') }}</span>
                    </div>
                    <div class="text-xs text-slate-500 font-mono truncate">{{ '@' . ($u->username ?? '-') }} • {{ $u->email }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">
                        Login terakhir: <b>{{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'belum pernah' }}</b>
                        • {{ $logCounts[$u->id] ?? 0 }} aktivitas
                        • dibuat {{ $u->created_at->format('d M Y') }}
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-1.5 shrink-0">
                    <button @click="open = !open" class="px-3 py-2 text-xs font-bold rounded-xl bg-amber-400 hover:bg-amber-500 whitespace-nowrap"><i class="bi bi-pencil-square"></i> Edit</button>
                    @if($u->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus {{ '@' . $u->username }}?')" class="inline">@csrf @method('DELETE')<button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700 hover:bg-rose-200">Hapus</button></form>
                    @endif
                </div>
            </div>
            <div x-show="open" x-cloak class="border-t bg-amber-50/60 p-5">
                <form method="POST" action="{{ route('admin.users.update', $u) }}" class="grid md:grid-cols-2 gap-3">@csrf @method('PUT')
                    <div><label class="text-xs font-bold text-slate-500">NAMA</label>
                        <input name="name" required value="{{ $u->name }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div><label class="text-xs font-bold text-slate-500">USERNAME (login)</label>
                        <input name="username" required minlength="3" value="{{ $u->username }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white font-mono"></div>
                    <div class="md:col-span-2"><label class="text-xs font-bold text-slate-500">EMAIL (bisa juga login)</label>
                        <input type="email" name="email" required value="{{ $u->email }}" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div><label class="text-xs font-bold text-slate-500">PASSWORD BARU <span class="font-normal text-slate-400">(kosongkan = tetap)</span></label>
                        <input type="password" name="password" minlength="6" placeholder="min 6 karakter" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div><label class="text-xs font-bold text-slate-500">KONFIRMASI PASSWORD</label>
                        <input type="password" name="password_confirmation" placeholder="ulangi password baru" class="mt-1 w-full px-4 py-2.5 rounded-2xl border bg-white"></div>
                    <div class="md:col-span-2 flex gap-2">
                        <button class="px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700"><i class="bi bi-check-lg"></i> Simpan perubahan</button>
                        <button type="button" @click="open = false" class="px-5 py-2.5 rounded-2xl bg-white border text-xs font-bold">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl border p-8 text-center text-slate-500">Tidak ada admin yang cocok dengan “{{ $q }}”.</div>
        @endforelse

        {{-- Aktivitas terakhir user-user ini --}}
        @if($recentLogs->count())
        <div class="bg-white rounded-3xl border p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold">Aktivitas terakhir</h3>
                <a href="{{ route('admin.activity.index') }}" class="text-xs font-bold text-sky-600 hover:underline">Semua log →</a>
            </div>
            <div class="space-y-2 text-sm">
                @foreach($recentLogs as $log)
                <div class="flex items-start gap-2 text-xs">
                    <span class="font-mono text-slate-400 shrink-0">{{ $log->created_at->format('d/m H:i') }}</span>
                    <span class="font-bold shrink-0">{{ $log->user_name }}</span>
                    <span class="px-1.5 py-0.5 rounded font-black {{ $log->action === 'delete' ? 'bg-rose-100 text-rose-700' : ($log->action === 'login' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">{{ strtoupper($log->action) }}</span>
                    <span class="text-slate-500 truncate">{{ $log->description }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
