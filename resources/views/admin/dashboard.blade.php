@extends('admin.layout')
@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Ringkasan omzet, order & performa link')

@section('content')
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="bg-white rounded-3xl p-5 border shadow-sm">
        <div class="flex items-center justify-between"><span class="text-xs font-semibold text-slate-500 uppercase">Omzet Hari Ini</span><span class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="bi bi-cash-stack"></i></span></div>
        <div class="text-2xl font-black mt-2">{{ rupiah($omzetToday) }}</div>
        <div class="text-xs text-slate-500 mt-1">Transaksi paid hari ini</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border shadow-sm">
        <div class="flex items-center justify-between"><span class="text-xs font-semibold text-slate-500 uppercase">Omzet Bulan Ini</span><span class="w-9 h-9 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center"><i class="bi bi-graph-up-arrow"></i></span></div>
        <div class="text-2xl font-black mt-2">{{ rupiah($omzetMonth) }}</div>
        <div class="text-xs text-slate-500 mt-1">{{ $orderMonth }} transaksi bulan ini</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border shadow-sm">
        <div class="flex items-center justify-between"><span class="text-xs font-semibold text-slate-500 uppercase">Total Omzet</span><span class="w-9 h-9 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="bi bi-wallet2"></i></span></div>
        <div class="text-2xl font-black mt-2">{{ rupiah($omzetTotal) }}</div>
        <div class="text-xs text-slate-500 mt-1">Akumulasi paid semua waktu</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border shadow-sm">
        <div class="flex items-center justify-between"><span class="text-xs font-semibold text-slate-500 uppercase">Pending / Klik</span><span class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="bi bi-clock-history"></i></span></div>
        <div class="text-2xl font-black mt-2">{{ $orderPending }} <span class="text-sm font-semibold text-slate-400">pending</span></div>
        <div class="text-xs text-slate-500 mt-1">{{ number_format($totalClicks) }} total klik tombol • {{ $totalPages }} halaman</div>
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-4 mt-4">
    <div class="xl:col-span-2 bg-white rounded-3xl p-6 border shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div><h3 class="font-bold">Grafik Omzet 30 Hari</h3><p class="text-xs text-slate-500">Berdasarkan order berstatus paid</p></div>
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-rose-600 hover:underline">Laporan lengkap →</a>
        </div>
        <div class="h-72"><canvas id="omzetChart"></canvas></div>
    </div>
    <div class="bg-white rounded-3xl p-6 border shadow-sm">
        <h3 class="font-bold mb-1">Omzet per Layanan</h3>
        <p class="text-xs text-slate-500 mb-4">Bulan berjalan</p>
        <div class="space-y-3">
            @forelse($perService as $s)
                @php $pct = $omzetMonth > 0 ? round($s->omzet / $omzetMonth * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm"><span class="font-semibold">{{ $s->service_name }}</span><span class="text-slate-500">{{ rupiah($s->omzet) }}</span></div>
                    <div class="h-2 bg-slate-100 rounded-full mt-1"><div class="h-2 rounded-full bg-gradient-to-r from-rose-500 to-pink-400" style="width: {{ $pct }}%"></div></div>
                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $s->trx }} trx • {{ $pct }}%</div>
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada data bulan ini.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-4 mt-4">
    <div class="xl:col-span-2 bg-white rounded-3xl p-6 border shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold">Order Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-rose-600 hover:underline">Kelola order →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-slate-400 uppercase"><th class="py-2">Kode</th><th>Pelanggan</th><th>Layanan</th><th class="text-right">Total</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($latestOrders as $o)
                    <tr class="border-t">
                        <td class="py-2 font-mono text-xs">{{ $o->order_code }}</td>
                        <td>{{ $o->customer_name }}</td>
                        <td class="text-slate-500">{{ $o->service_name }}</td>
                        <td class="text-right font-semibold">{{ rupiah($o->total) }}</td>
                        <td><span class="px-2 py-1 rounded-full text-[11px] font-bold {{ $o->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($o->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-600') }}">{{ strtoupper($o->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-3xl p-6 border shadow-sm">
        <h3 class="font-bold mb-1">Tombol Terlaris (klik)</h3>
        <p class="text-xs text-slate-500 mb-4">Tracking klik frontend otomatis</p>
        <div class="space-y-2 text-sm">
            @foreach($topButtons as $b)
                <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-slate-50">
                    @if($b->icon_path)<img src="{{ pv_asset($b->icon_path) }}" class="w-8 h-8 rounded-lg object-cover" alt="">@endif
                    <div class="flex-1 min-w-0"><div class="font-semibold truncate">{{ $b->title }}</div><div class="text-xs text-slate-400">{{ $b->page->name ?? '-' }}</div></div>
                    <span class="text-xs font-black bg-slate-900 text-white px-2 py-1 rounded-full">{{ number_format($b->click_count) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('omzetChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($labels),
        datasets: [
            { label: 'Omzet (Rp)', data: @json($omzetData), backgroundColor: '#ff4d6d', borderRadius: 8, yAxisID: 'y' },
            { label: 'Transaksi', data: @json($trxData), type: 'line', borderColor: '#0f172a', tension: 0.4, yAxisID: 'y1' },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        scales: {
            y: { ticks: { callback: v => (v >= 1000000 ? (v/1000000)+'jt' : (v/1000)+'rb') } },
            y1: { position: 'right', grid: { display: false }, ticks: { precision: 0 } }
        }
    }
});
</script>
@endpush
