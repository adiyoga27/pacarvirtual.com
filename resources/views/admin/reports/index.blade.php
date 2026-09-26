@extends('admin.layout')
@section('title', 'Laporan Omzet')
@section('header', 'Laporan Omzet Penjualan')
@section('subheader', 'Filter periode, layanan, pembayaran & status — export CSV sekali klik')

@section('content')
<form method="GET" class="bg-white rounded-3xl border p-4 grid md:grid-cols-6 gap-3 mb-4 text-sm">
    <div><label class="text-[11px] font-bold text-slate-400">DARI</label><input type="date" name="from" value="{{ $from }}" class="w-full px-4 py-2.5 rounded-2xl border"></div>
    <div><label class="text-[11px] font-bold text-slate-400">SAMPAI</label><input type="date" name="to" value="{{ $to }}" class="w-full px-4 py-2.5 rounded-2xl border"></div>
    <div><label class="text-[11px] font-bold text-slate-400">LAYANAN</label><select name="service" class="w-full px-4 py-2.5 rounded-2xl border"><option value="">Semua</option>@foreach($services as $s)<option value="{{ $s->name }}" {{ $service === $s->name ? 'selected' : '' }}>{{ $s->name }}</option>@endforeach</select></div>
    <div><label class="text-[11px] font-bold text-slate-400">PEMBAYARAN</label><select name="payment" class="w-full px-4 py-2.5 rounded-2xl border"><option value="">Semua</option>@foreach(['QRIS','Transfer Bank','E-Wallet','Cash','Lainnya'] as $pm)<option {{ $payment === $pm ? 'selected' : '' }}>{{ $pm }}</option>@endforeach</select></div>
    <div><label class="text-[11px] font-bold text-slate-400">STATUS</label><select name="status" class="w-full px-4 py-2.5 rounded-2xl border"><option value="">Semua</option>@foreach(['paid','pending','cancelled','refunded'] as $st)<option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>@endforeach</select></div>
    <div class="flex items-end gap-2"><button class="flex-1 py-2.5 rounded-2xl bg-slate-900 text-white font-bold">Tampilkan</button><button name="export" value="csv" class="py-2.5 px-4 rounded-2xl bg-emerald-500 text-white font-bold" title="Export CSV"><i class="bi bi-download"></i></button></div>
</form>

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
    <div class="rounded-3xl p-5 bg-gradient-to-br from-rose-500 to-pink-500 text-white shadow"><div class="text-xs opacity-80 uppercase font-semibold">Omzet</div><div class="text-2xl font-black">{{ rupiah($summary['omzet']) }}</div><div class="text-xs opacity-80">{{ $summary['trx'] }} transaksi</div></div>
    <div class="bg-white rounded-3xl p-5 border"><div class="text-xs text-slate-400 uppercase font-semibold">Rata-rata / trx</div><div class="text-2xl font-black">{{ rupiah($summary['avg'] ?? 0) }}</div><div class="text-xs text-slate-400">nilai order rata-rata</div></div>
    <div class="bg-white rounded-3xl p-5 border"><div class="text-xs text-slate-400 uppercase font-semibold">Total Diskon</div><div class="text-2xl font-black">{{ rupiah($summary['discount']) }}</div><div class="text-xs text-slate-400">potongan periode ini</div></div>
    <div class="bg-white rounded-3xl p-5 border"><div class="text-xs text-slate-400 uppercase font-semibold">Periode</div><div class="font-black">{{ date('d M', strtotime($from)) }} – {{ date('d M Y', strtotime($to)) }}</div><div class="text-xs text-slate-400">{{ $rows->count() }} baris ditampilkan</div></div>
</div>

<div class="grid xl:grid-cols-3 gap-4">
    <div class="xl:col-span-2 bg-white rounded-3xl border p-6"><h3 class="font-bold mb-3">Omzet Harian</h3><div class="h-64"><canvas id="dailyChart"></canvas></div></div>
    <div class="bg-white rounded-3xl border p-6">
        <h3 class="font-bold mb-3">Per Layanan</h3>
        <div class="space-y-2 text-sm max-h-64 overflow-auto">
            @forelse($perService as $s)<div class="flex justify-between border-b py-1.5"><span>{{ $s->service_name }} <span class="text-xs text-slate-400">({{ $s->trx }}x)</span></span><b>{{ rupiah($s->omzet) }}</b></div>@empty<p class="text-slate-400 text-sm">Tidak ada data.</p>@endforelse
        </div>
        <h3 class="font-bold mt-5 mb-3">Per Pembayaran</h3>
        <div class="space-y-2 text-sm">
            @forelse($perPayment as $p)<div class="flex justify-between border-b py-1.5"><span>{{ $p->payment_method }} <span class="text-xs text-slate-400">({{ $p->trx }}x)</span></span><b>{{ rupiah($p->omzet) }}</b></div>@empty<p class="text-slate-400 text-sm">Tidak ada data.</p>@endforelse
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl border mt-4 overflow-x-auto">
    <table class="w-full text-sm min-w-[900px]">
        <thead><tr class="text-left text-xs uppercase text-slate-400 border-b"><th class="p-4">Tanggal</th><th>Kode</th><th>Pelanggan</th><th>Layanan</th><th class="text-right">Total</th><th>Status</th></tr></thead>
        <tbody>@foreach($rows as $r)<tr class="border-b last:border-0"><td class="p-4">{{ $r->order_date->format('d M Y') }}</td><td class="font-mono text-xs">{{ $r->order_code }}</td><td>{{ $r->customer_name }}</td><td class="text-slate-500">{{ $r->service_name }}</td><td class="text-right font-bold">{{ rupiah($r->total) }}</td><td><span class="text-[11px] font-bold px-2 py-1 rounded-full bg-slate-100">{{ strtoupper($r->status) }}</span></td></tr>@endforeach</tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: { labels: @json($labels), datasets: [{ label: 'Omzet', data: @json($omzetData), borderColor: '#ff4d6d', backgroundColor: '#ff4d6d33', fill: true, tension: 0.4 }] },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>
@endpush
