<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
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

        // Per layanan (dari items — akurat untuk order multi-jasa; komisi = aktual per baris)
        $itemBase = OrderItem::query()->whereHas('order', function ($oq) use ($from, $to, $status, $payment) {
            $oq->whereBetween('order_date', [$from, $to]);
            if ($status) {
                $oq->where('status', $status);
            }
            if ($payment) {
                $oq->where('payment_method', $payment);
            }
        })->when($service, fn ($q) => $q->where('service_name', $service));

        $perService = (clone $itemBase)->select(
                'service_name',
                DB::raw('SUM(quantity * price - discount) as omzet'),
                DB::raw('COUNT(DISTINCT order_id) as trx'),
                DB::raw('SUM(quantity) as qty'),
                DB::raw('SUM(commission) as komisi')
            )
            ->groupBy('service_name')->orderByDesc('omzet')->get();

        $estKomisiTotal = 0;
        foreach ($perService as $row) {
            $row->est_komisi = (float) $row->komisi;
            $estKomisiTotal += $row->est_komisi;
        }

        // Per pembayaran
        $perPayment = (clone $base)->select('payment_method', DB::raw('SUM(total) as omzet'), DB::raw('COUNT(*) as trx'))
            ->groupBy('payment_method')->orderByDesc('omzet')->get();

        $rows = (clone $base)->with(['items.talent'])->orderBy('order_date')->orderBy('id')->limit(500)->get();

        $services = Service::orderBy('name')->get();

        // Export CSV
        if ($request->input('export') === 'csv') {
            $filename = "laporan-omzet-{$from}_{$to}.csv";
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\""];
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Tanggal', 'Invoice', 'Kode', 'Pelanggan', 'WA', 'Jasa (isi order)', 'Talent', 'Komisi', 'Total', 'Pembayaran', 'Status']);
                foreach ($rows as $r) {
                    $itemsStr = $r->items->map(fn ($it) => $it->service_name . ' ' . $it->quantity . 'x' . $it->price . ' [' . ($it->talent?->name ?? '-') . ($it->commission_date ? ', ' . $it->commission_date->format('Y-m-d') : '') . ']')->implode('; ');
                    fputcsv($out, [$r->order_date->format('Y-m-d'), $r->displayNo(), $r->order_code, $r->customer_name, $r->customer_whatsapp, $itemsStr ?: $r->service_name, $r->talentDisplay(), $r->commission, $r->total, $r->payment_method, $r->status]);
                }
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        $paymentMethods = PaymentMethod::activeNames();

        return view('admin.reports.index', compact(
            'from', 'to', 'service', 'payment', 'status',
            'summary', 'labels', 'omzetData', 'trxData',
            'perService', 'perPayment', 'rows', 'services', 'estKomisiTotal', 'paymentMethods'
        ));
    }
}
