@extends('admin.layout')
@section('title', 'Data Order')
@section('header', 'Data Order / Penjualan')
@section('subheader', 'Admin, metode, talent & jasa diambil dari database')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .select2-container--default .select2-selection--single { height: 42px; border-radius: 1rem; border: 1px solid #e2e8f0; display: flex; align-items: center; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { padding-left: 1rem; font-size: .875rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
</style>
@endpush

@section('content')
{{-- Ringkasan sesuai filter --}}
<div class="grid grid-cols-2 xl:grid-cols-5 gap-3 mb-4">
    <div class="rounded-3xl p-4 bg-gradient-to-br from-rose-500 to-pink-500 text-white shadow"><div class="text-[11px] opacity-80 uppercase font-semibold">Harga ({{ $summary['trx'] }} trx)</div><div class="text-xl font-black">{{ rupiah($summary['harga']) }}</div></div>
    <div class="bg-white rounded-3xl p-4 border"><div class="text-[11px] text-slate-400 uppercase font-semibold">Komisi</div><div class="text-xl font-black text-indigo-600">{{ rupiah($summary['komisi']) }}</div></div>
    <div class="bg-white rounded-3xl p-4 border"><div class="text-[11px] text-slate-400 uppercase font-semibold">Untung</div><div class="text-xl font-black text-emerald-600">{{ rupiah($summary['untung']) }}</div></div>
    <div class="bg-white rounded-3xl p-4 border"><div class="text-[11px] text-slate-400 uppercase font-semibold">Dana Cair</div><div class="text-xl font-black text-sky-600">{{ rupiah($summary['cair']) }}</div></div>
    <div class="col-span-2 xl:col-span-1 flex items-end"><a href="{{ route('admin.orders.create') }}" class="w-full text-center px-4 py-3 rounded-2xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-700"><i class="bi bi-plus-lg"></i> Order Baru</a></div>
</div>

<form method="GET" class="bg-white rounded-3xl border p-4 grid md:grid-cols-4 gap-3 mb-4 text-sm">
    <input name="search" value="{{ request('search') }}" placeholder="Cari kode / client / WA" class="px-4 py-2.5 rounded-2xl border">
    <select name="status" class="px-4 py-2.5 rounded-2xl border bg-white"><option value="">Semua status</option>@foreach(['pending','paid','cancelled','refunded'] as $st)<option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>@endforeach</select>
    <select name="service" class="select2" data-placeholder="Semua jasa"><option value="">Semua jasa</option>@foreach($services as $s)<option value="{{ $s->id }}" {{ (string) request('service') === (string) $s->id || request('service') === $s->name ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select>
    <select name="payment" class="select2" data-placeholder="Semua metode"><option value="">Semua metode</option>@foreach($paymentMethods as $pm)<option {{ request('payment') === $pm ? 'selected' : '' }}>{{ $pm }}</option>@endforeach</select>
    <select name="talent" class="select2" data-placeholder="Semua talent"><option value="">Semua talent</option>@foreach($talents as $t)<option value="{{ $t->id }}" {{ (string) request('talent') === (string) $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select>
    <select name="admin" class="select2" data-placeholder="Semua admin"><option value="">Semua admin</option>@foreach($admins as $a)<option value="{{ $a->id }}" {{ (string) request('admin') === (string) $a->id ? 'selected' : '' }}>{{ $a->name }} ({{ '@' . $a->username }})</option>@endforeach</select>
    <input type="date" name="from" value="{{ request('from') }}" class="px-4 py-2.5 rounded-2xl border">
    <div class="flex gap-2"><input type="date" name="to" value="{{ request('to') }}" class="px-4 py-2.5 rounded-2xl border flex-1"><button class="px-4 py-2.5 rounded-2xl bg-slate-900 text-white font-bold">Filter</button>@if(request()->query())<a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 font-bold">Reset</a>@endif</div>
</form>

<div class="bg-white rounded-3xl border shadow-sm overflow-x-auto">
    <table class="w-full text-sm min-w-[1500px]">
        <thead><tr class="text-left text-[11px] uppercase text-slate-400 border-b whitespace-nowrap">
            <th class="p-3">No</th><th>Tanggal Orderan</th><th>Tanggal Komisian</th><th>Admin</th><th>Metode Pembayaran</th><th>Talent</th><th>Jasa</th><th>Client</th><th class="text-right">Harga</th><th class="text-right">Komisi</th><th class="text-right">Untung</th><th class="text-right">Dana Cair</th><th class="text-right p-3">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($orders as $i => $o)
            <tr class="border-b last:border-0 hover:bg-slate-50 align-top">
                <td class="p-3 text-slate-400">{{ ($orders->currentPage() - 1) * $orders->perPage() + $i + 1 }}</td>
                <td class="p-3 whitespace-nowrap"><div class="font-semibold">{{ $o->order_date->format('d M Y') }}</div><div class="font-mono text-[11px] font-bold text-slate-600">{{ $o->displayNo() }}</div><span class="mt-1 inline-block px-2 py-0.5 rounded-full text-[10px] font-bold {{ $o->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($o->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600') }}">{{ strtoupper($o->status) }}</span></td>
                <td class="p-3">@if($o->items->count())<div class="space-y-1.5">@foreach($o->items as $it)<div><div class="whitespace-nowrap text-slate-500 text-xs">{{ $it->service?->name ?? $it->service_name }}</div><div class="whitespace-nowrap font-semibold">@if($it->commission_date){{ $it->commission_date->format('d M Y') }}@else<span class="text-slate-300">-</span>@endif</div></div>@endforeach</div>@else<div class="whitespace-nowrap font-semibold">@if($o->commission_date){{ $o->commission_date->format('d M Y') }}@else<span class="text-slate-300">-</span>@endif</div>@endif</td>
                <td class="p-3"><div class="font-semibold whitespace-nowrap">{{ $o->creator?->name ?? '-' }}</div>@if($o->creator?->username)<div class="text-[11px] text-slate-400 font-mono">{{ '@' . $o->creator->username }}</div>@endif</td>
                <td class="p-3 whitespace-nowrap"><span class="text-xs font-bold px-2 py-1 rounded-lg bg-slate-100">{{ $o->payment_method }}</span></td>
                <td class="p-3">@if($o->items->count())<div class="space-y-1.5">@foreach($o->items as $it)<div><div class="whitespace-nowrap text-slate-500 text-xs">{{ $it->service?->name ?? $it->service_name }}</div><div class="whitespace-nowrap font-semibold">{{ $it->talent?->name ?? '-' }}</div></div>@endforeach</div>@else<div class="font-semibold whitespace-nowrap">{{ $o->talentDisplay() }}</div>@endif</td>
                <td class="p-3">@if($o->items->count())<div class="space-y-1">@foreach($o->items as $it)<div class="whitespace-nowrap">{{ $it->service?->name ?? $it->service_name }}</div><div class="text-[11px] text-slate-400">{{ $it->quantity }}x {{ rupiah($it->price) }}</div>@endforeach</div>@else<div class="whitespace-nowrap">{{ $o->serviceDisplay() }}</div><div class="text-[11px] text-slate-400">{{ $o->quantity }}x {{ rupiah($o->price) }}</div>@endif</td>
                <td class="p-3"><div class="font-semibold whitespace-nowrap">{{ $o->customer_name }}</div><div class="text-[11px] text-slate-400">{{ $o->customer_whatsapp }}</div></td>
                <td class="p-3 text-right font-bold whitespace-nowrap">{{ rupiah($o->total) }}</td>
                <td class="p-3 text-right text-indigo-600 font-semibold whitespace-nowrap">{{ rupiah($o->commission) }}</td>
                <td class="p-3 text-right text-emerald-600 font-black whitespace-nowrap">{{ rupiah($o->profit) }}</td>
                <td class="p-3 text-right text-sky-600 font-semibold whitespace-nowrap">{{ rupiah($o->disbursed) }}</td>
                <td class="p-3 text-right whitespace-nowrap"><a href="{{ route('admin.orders.edit', $o) }}" class="px-3 py-2 text-xs font-bold rounded-xl bg-amber-400">Edit</a>
                <form method="POST" action="{{ route('admin.orders.destroy', $o) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="px-3 py-2 text-xs font-bold rounded-xl bg-rose-100 text-rose-700">Hapus</button></form></td>
            </tr>
            @empty
            <tr><td colspan="13" class="p-8 text-center text-slate-400">Belum ada order. Klik Order Baru.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () { $('.select2').each(function () { $(this).select2({ width: '100%', placeholder: $(this).data('placeholder') || '', allowClear: true }); }); });
</script>
@endpush
