@extends('admin.layout')
@section('title', 'Data Order')
@section('header', 'Data Order / Penjualan')
@section('subheader', 'Catat setiap transaksi — total dihitung otomatis (qty × harga − diskon)')

@section('content')
<form method="GET" class="bg-white rounded-3xl border p-4 grid md:grid-cols-6 gap-3 mb-4 text-sm">
    <input name="search" value="{{ request('search') }}" placeholder="Cari kode / nama / WA" class="px-4 py-2.5 rounded-2xl border md:col-span-2">
    <select name="status" class="px-4 py-2.5 rounded-2xl border"><option value="">Semua status</option>@foreach(['pending','paid','cancelled','refunded'] as $st)<option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>@endforeach</select>
    <select name="service" class="px-4 py-2.5 rounded-2xl border"><option value="">Semua layanan</option>@foreach($services as $s)<option {{ request('service') === $s->name ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select>
    <input type="date" name="from" value="{{ request('from') }}" class="px-4 py-2.5 rounded-2xl border">
    <div class="flex gap-2"><input type="date" name="to" value="{{ request('to') }}" class="px-4 py-2.5 rounded-2xl border flex-1"><button class="px-4 py-2.5 rounded-2xl bg-slate-900 text-white font-bold">Filter</button></div>
</form>
<div class="flex justify-between items-center mb-3">
    <p class="text-sm text-slate-500">{{ $orders->total() }} data</p>
    <a href="{{ route('admin.orders.create') }}" class="px-4 py-2.5 rounded-2xl bg-rose-500 text-white text-sm font-bold"><i class="bi bi-plus-lg"></i> Order Baru</a>
</div>
<div class="bg-white rounded-3xl border shadow-sm overflow-x-auto">
    <table class="w-full text-sm min-w-[900px]">
        <thead><tr class="text-left text-xs uppercase text-slate-400 border-b"><th class="p-4">Tanggal / Kode</th><th>Pelanggan</th><th>Layanan</th><th class="text-right">Total</th><th>Status</th><th class="text-right p-4">Aksi</th></tr></thead>
        <tbody>
            @foreach($orders as $o)
            <tr class="border-b last:border-0 hover:bg-slate-50">
                <td class="p-4"><div class="font-mono text-xs">{{ $o->order_code }}</div><div class="text-xs text-slate-400">{{ $o->order_date->format('d M Y') }} • {{ $o->payment_method }}</div></td>
                <td><div class="font-semibold">{{ $o->customer_name }}</div><div class="text-xs text-slate-400">{{ $o->customer_whatsapp }}</div></td>
                <td><div>{{ $o->service_name }}</div><div class="text-xs text-slate-400">{{ $o->talent_name }} • {{ $o->quantity }}x {{ rupiah($o->price) }}</div></td>
                <td class="text-right font-black">{{ rupiah($o->total) }}</td>
                <td><span class="px-2 py-1 rounded-full text-[11px] font-bold {{ $o->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($o->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600') }}">{{ strtoupper($o->status) }}</span></td>
                <td class="p-4 text-right whitespace-nowrap"><a href="{{ route('admin.orders.edit', $o) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-amber-400">Edit</a>
                <form method="POST" action="{{ route('admin.orders.destroy', $o) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700">Hapus</button></form></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
