@extends('admin.layout')
@section('title', 'Log Sistem')
@section('header', 'Log Sistem')
@section('subheader', 'Siapa login & apa yang diubah (CRUD) — otomatis tercatat')

@section('content')
<form method="GET" class="bg-white rounded-3xl border p-4 mb-4 grid sm:grid-cols-5 gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Cari nama / aksi / url..." class="px-4 py-2.5 rounded-2xl border text-sm sm:col-span-2">
    <select name="module" class="px-4 py-2.5 rounded-2xl border text-sm bg-white">
        <option value="">Semua modul</option>
        @foreach($modules as $m)<option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ $m }}</option>@endforeach
    </select>
    <select name="action" class="px-4 py-2.5 rounded-2xl border text-sm bg-white">
        <option value="">Semua aksi</option>
        @foreach($actions as $a)<option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ strtoupper($a) }}</option>@endforeach
    </select>
    <button class="px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-bold">Filter</button>
</form>

<div class="bg-white rounded-3xl border overflow-hidden">
    <table class="w-full text-sm">
        <thead><tr class="text-left text-xs uppercase text-slate-400 border-b">
            <th class="p-4">Waktu</th><th>Admin</th><th>Aksi</th><th>Modul</th><th>Keterangan</th><th class="text-right p-4">IP</th>
        </tr></thead>
        <tbody>
            @forelse($logs as $log)
            <tr class="border-b last:border-0 hover:bg-slate-50 align-top">
                <td class="p-4 whitespace-nowrap text-xs text-slate-500">{{ $log->created_at->format('d M Y H:i:s') }}<br><span class="text-slate-400">{{ $log->created_at->diffForHumans() }}</span></td>
                <td class="p-4"><div class="font-bold">{{ $log->user_name ?? '-' }}</div><div class="text-xs text-slate-400 font-mono">{{ $log->user?->username ? '@'.$log->user->username : '' }}</div></td>
                <td class="p-4"><span class="text-[11px] font-black px-2 py-1 rounded-full {{ $log->action === 'delete' ? 'bg-rose-100 text-rose-700' : ($log->action === 'login' ? 'bg-emerald-100 text-emerald-700' : ($log->action === 'logout' ? 'bg-slate-200 text-slate-500' : ($log->action === 'create' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700'))) }}">{{ strtoupper($log->action) }}</span></td>
                <td class="p-4"><span class="text-xs font-bold px-2 py-1 rounded-lg bg-slate-100">{{ $log->module }}</span></td>
                <td class="p-4 text-xs text-slate-600 max-w-md"><div class="break-words">{{ $log->description }}</div><div class="font-mono text-[11px] text-slate-400 mt-1">{{ $log->method }} {{ $log->url }}</div></td>
                <td class="p-4 text-right text-xs font-mono text-slate-400">{{ $log->ip_address }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-8 text-center text-slate-400">Belum ada log. Login & ubah data dulu, otomatis tercatat di sini.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($logs->hasPages())
    <div class="p-4 border-t">{{ $logs->links() }}</div>
    @endif
</div>

<form method="POST" action="{{ route('admin.activity.destroy') }}" onsubmit="return confirm('Hapus log lama?')" class="mt-4 flex items-center gap-2 text-sm">@csrf @method('DELETE')
    <span class="text-slate-500 text-xs">Bersihkan log lebih lama dari</span>
    <select name="days" class="px-3 py-2 rounded-xl border text-sm bg-white"><option value="30">30 hari</option><option value="90" selected>90 hari</option><option value="180">180 hari</option></select>
    <button class="px-4 py-2 rounded-xl bg-rose-100 text-rose-700 text-xs font-bold">Hapus log lama</button>
</form>
@endsection
