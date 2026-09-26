<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(fn ($w) => $w->where('order_code', 'like', "%$s%")->orWhere('customer_name', 'like', "%$s%")->orWhere('customer_whatsapp', 'like', "%$s%"));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        if ($request->filled('service')) {
            $q->where('service_name', $request->service);
        }
        if ($request->filled('from')) {
            $q->whereDate('order_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('order_date', '<=', $request->to);
        }
        $orders = $q->orderByDesc('order_date')->orderByDesc('id')->paginate(15)->withQueryString();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('admin.orders.index', compact('orders', 'services'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('admin.orders.form', ['order' => new Order(['order_date' => now()->toDateString(), 'quantity' => 1, 'status' => 'pending', 'payment_method' => 'QRIS']), 'services' => $services]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = auth()->id();
        $order = Order::create($data);
        return redirect()->route('admin.orders.index')->with('success', 'Order ' . $order->order_code . ' berhasil dibuat.');
    }

    public function edit(Order $order)
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        return view('admin.orders.form', compact('order', 'services'));
    }

    public function update(Request $request, Order $order)
    {
        $order->update($this->validated($request));
        return back()->with('success', 'Order berhasil diperbarui. Total otomatis dihitung ulang.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Order dihapus.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'customer_name' => 'required|max:120',
            'customer_whatsapp' => 'nullable|max:30',
            'service_name' => 'required|max:120',
            'talent_name' => 'nullable|max:120',
            'quantity' => 'required|integer|min:1|max:100',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:QRIS,Transfer Bank,E-Wallet,Cash,Lainnya',
            'status' => 'required|in:pending,paid,cancelled,refunded',
            'order_date' => 'required|date',
            'notes' => 'nullable|max:1000',
        ]) + ['discount' => $request->input('discount', 0)];
    }
}
