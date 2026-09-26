@extends('admin.layout')
@section('title', 'Layanan')
@section('header', 'Master Layanan')
@section('subheader', 'Daftar layanan untuk dropdown order & laporan')

@section('content')
<div class="grid xl:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border p-6 h-fit">
        <h3 class="font-bold mb-4">Tambah Layanan</h3>
        <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-3">
            @csrf
            <input name="name" required placeholder="Nama layanan" class="w-full px-4 py-2.5 rounded-2xl border">
            <input type="number" name="default_price" required min="0" placeholder="Harga default" class="w-full px-4 py-2.5 rounded-2xl border">
            <textarea name="description" rows="2" placeholder="Deskripsi (opsional)" class="w-full px-4 py-2.5 rounded-2xl border"></textarea>
            <button class="w-full py-2.5 rounded-2xl bg-rose-500 text-white font-bold">Tambah</button>
        </form>
    </div>
    <div class="xl:col-span-2 bg-white rounded-3xl border overflow-hidden h-fit">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs uppercase text-slate-400 border-b"><th class="p-4">Layanan</th><th class="text-right">Harga</th><th class="text-center">Aktif</th><th class="text-right p-4">Aksi</th></tr></thead>
            <tbody>
                @foreach($services as $s)
                <tr class="border-b last:border-0">
                    <form method="POST" action="{{ route('admin.services.update', $s) }}">@csrf @method('PUT')
                    <td class="p-4"><input name="name" value="{{ $s->name }}" class="px-3 py-2 rounded-xl border w-full"></td>
                    <td><input type="number" name="default_price" value="{{ $s->default_price }}" class="px-3 py-2 rounded-xl border w-36 text-right"></td>
                    <td class="text-center"><input type="checkbox" name="is_active" value="1" {{ $s->is_active ? 'checked' : '' }} class="rounded w-5 h-5"></td>
                    <td class="p-4 text-right whitespace-nowrap"><button class="px-3 py-2 text-xs font-bold rounded-xl bg-emerald-500 text-white">Simpan</button></form>
                    <form method="POST" action="{{ route('admin.services.destroy', $s) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700">Hapus</button></form></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
