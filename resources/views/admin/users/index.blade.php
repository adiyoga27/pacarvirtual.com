@extends('admin.layout')
@section('title', 'Kelola Admin')
@section('header', 'Kelola Admin')
@section('subheader', 'Akun yang bisa login ke panel ini')

@section('content')
<div class="grid xl:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border p-6 h-fit">
        <h3 class="font-bold mb-4">Tambah Admin</h3>
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">@csrf
            <input name="name" required placeholder="Nama" class="w-full px-4 py-2.5 rounded-2xl border">
            <input type="email" name="email" required placeholder="Email" class="w-full px-4 py-2.5 rounded-2xl border">
            <input name="password" required minlength="6" placeholder="Password (min 6)" class="w-full px-4 py-2.5 rounded-2xl border">
            <button class="w-full py-2.5 rounded-2xl bg-rose-500 text-white font-bold">Tambah</button>
        </form>
    </div>
    <div class="xl:col-span-2 bg-white rounded-3xl border overflow-hidden h-fit">
        <table class="w-full text-sm"><thead><tr class="text-left text-xs uppercase text-slate-400 border-b"><th class="p-4">Nama</th><th>Email</th><th class="text-right p-4">Aksi</th></tr></thead>
        <tbody>@foreach($users as $u)<tr class="border-b last:border-0"><td class="p-4 font-semibold">{{ $u->name }}</td><td class="text-slate-500">{{ $u->email }}</td><td class="p-4 text-right">@if($u->id !== auth()->id())<form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus admin ini?')" class="inline">@csrf @method('DELETE')<button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700">Hapus</button></form>@else<span class="text-xs text-slate-400">Anda</span>@endif</td></tr>@endforeach</tbody></table>
    </div>
</div>
@endsection
