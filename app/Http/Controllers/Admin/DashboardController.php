<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClickLog;
use App\Models\LinkButton;
use App\Models\Order;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $omzetToday = Order::where('status', 'paid')->whereDate('order_date', $today)->sum('total');
        $omzetMonth = Order::where('status', 'paid')->whereBetween('order_date', [$monthStart, $today])->sum('total');
        $omzetTotal = Order::where('status', 'paid')->sum('total');
        $orderPending = Order::where('status', 'pending')->count();
        $orderMonth = Order::whereBetween('order_date', [$monthStart, $today])->count();
        $totalPages = Page::count();
        $totalClicks = LinkButton::sum('click_count');

        // Grafik omzet 30 hari terakhir (paid only)
        $start = now()->subDays(29)->toDateString();
        $daily = Order::select(DB::raw('DATE(order_date) as d'), DB::raw('SUM(total) as omzet'), DB::raw('COUNT(*) as trx'))
            ->where('status', 'paid')
            ->whereBetween('order_date', [$start, $today])
            ->groupBy('d')
            ->pluck('omzet', 'd');

        $dailyCount = Order::select(DB::raw('DATE(order_date) as d'), DB::raw('COUNT(*) as trx'))
            ->whereBetween('order_date', [$start, $today])
            ->groupBy('d')
            ->pluck('trx', 'd');

        $labels = [];
        $omzetData = [];
        $trxData = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $omzetData[] = (float) ($daily[$d] ?? 0);
            $trxData[] = (int) ($dailyCount[$d] ?? 0);
        }

        // Omzet per layanan bulan ini
        $perService = Order::select('service_name', DB::raw('SUM(total) as omzet'), DB::raw('COUNT(*) as trx'))
            ->where('status', 'paid')
            ->whereBetween('order_date', [$monthStart, $today])
            ->groupBy('service_name')
            ->orderByDesc('omzet')
            ->limit(7)
            ->get();

        $latestOrders = Order::orderByDesc('order_date')->orderByDesc('id')->limit(8)->get();
        $topButtons = LinkButton::with('page')->orderByDesc('click_count')->limit(6)->get();

        return view('admin.dashboard', compact(
            'omzetToday', 'omzetMonth', 'omzetTotal', 'orderPending', 'orderMonth',
            'totalPages', 'totalClicks', 'labels', 'omzetData', 'trxData',
            'perService', 'latestOrders', 'topButtons'
        ));
    }
}
