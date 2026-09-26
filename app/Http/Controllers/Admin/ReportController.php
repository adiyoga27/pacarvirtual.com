<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $service = $request->input('service', '');
        $payment = $request->input('payment', '');
        $status = $request->input('status', 'paid');

        $base = Order::query()
            ->whereBetween('order_date', [$from, $to])
            ->when($service, fn ($q) => $q->where('service_name', $service))
            ->when($payment, fn ($q) => $q->where('payment_method', $payment))
            ->when($status, fn ($q) => $q->where('status', $status));

        $summary = [
            'omzet' => (float) (clone $base)->sum('total'),
            'trx' => (clone $base)->count(),
            'avg' => (float) (clone $base)->avg('total'),
            'discount' => (float) (clone $base)->sum('discount'),
        ];

        // Harian
        $daily = (clone $base)->select(DB::raw('DATE(order_date) as d'), DB::raw('SUM(total) as omzet'), DB::raw('COUNT(*) as trx'))
            ->groupBy('d')->orderBy('d')->get();
        $labels = $daily->pluck('d')->map(fn ($d) => date('d M', strtotime($d)));
        $omzetData = $daily->pluck('omzet')->map(fn ($v) => (float) $v);
        $trxData = $daily->pluck('trx');

        // Per layanan
        $perService = (clone $base)->select('service_name', DB::raw('SUM(total) as omzet'), DB::raw('COUNT(*) as trx'))
            ->groupBy('service_name')->orderByDesc('omzet')->get();

        // Per pembayaran
        $perPayment = (clone $base)->select('payment_method', DB::raw('SUM(total) as omzet'), DB::raw('COUNT(*) as trx'))
            ->groupBy('payment_method')->orderByDesc('omzet')->get();

        $rows = (clone $base)->orderBy('order_date')->orderBy('id')->limit(500)->get();

        $services = Service::orderBy('name')->get();

        // Export CSV
        if ($request->input('export') === 'csv') {
            $filename = "laporan-omzet-{$from}_{$to}.csv";
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Tanggal', 'Kode', 'Pelanggan', 'WA', 'Layanan', 'Talent', 'Qty', 'Harga', 'Diskon', 'Total', 'Pembayaran', 'Status']);
                foreach ($rows as $r) {
                    fputcsv($out, [$r->order_date->format('Y-m-d'), $r->order_code, $r->customer_name, $r->customer_whatsapp, $r->service_name, $r->talent_name, $r->quantity, $r->price, $r->discount, $r->total, $r->payment_method, $r->status]);
                }
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        return view('admin.reports.index', compact(
            'from', 'to', 'service', 'payment', 'status',
            'summary', 'labels', 'omzetData', 'trxData',
            'perService', 'perPayment', 'rows', 'services'
        ));
    }
}
