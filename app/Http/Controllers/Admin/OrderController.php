<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::with(['creator', 'talent', 'service', 'items.service', 'items.talent']);
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(fn ($w) => $w->where('invoice_no', 'like', "%$s%")->orWhere('order_code', 'like', "%$s%")->orWhere('customer_name', 'like', "%$s%")->orWhere('customer_whatsapp', 'like', "%$s%"));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        if ($request->filled('service')) {
            $sid = $request->service;
            $q->where(fn ($w) => $w->whereHas('items', fn ($i) => $i->where('service_id', $sid))->orWhere('service_id', $sid));
        }
        if ($request->filled('payment')) {
            $q->where('payment_method', $request->payment);
        }
        if ($request->filled('talent')) {
            $tid = $request->talent;
            $q->where(fn ($w) => $w->where('talent_id', $tid)->orWhereHas('items', fn ($i) => $i->where('talent_id', $tid)));
        }
        if ($request->filled('admin')) {
            $q->where('created_by', $request->admin);
        }
        if ($request->filled('from')) {
            $q->whereDate('order_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('order_date', '<=', $request->to);
        }
        $orders = (clone $q)->orderByDesc('order_date')->orderByDesc('id')->paginate(15)->withQueryString();

        // Ringkasan sesuai filter (di luar paginasi)
        $sumTotal = (float) (clone $q)->sum('total');
        $sumKomisi = (float) (clone $q)->sum('commission');
        $sumCair = (float) (clone $q)->sum('disbursed');
        $summary = [
            'harga' => $sumTotal,
            'komisi' => $sumKomisi,
            'untung' => max(0, $sumTotal - $sumKomisi),
            'cair' => $sumCair,
            'trx' => (clone $q)->count(),
        ];

        $services = Service::orderBy('name')->get();
        $talents = Talent::orderBy('name')->get();
        $admins = User::orderBy('name')->get();
        $paymentMethods = PaymentMethod::activeNames();

        return view('admin.orders.index', compact('orders', 'services', 'talents', 'admins', 'paymentMethods', 'summary'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $talents = Talent::with('paymentMethod')->where('is_active', true)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::activeNames();
        $admins = User::orderBy('name')->get();
        $servicesJson = $this->servicesJson($services);
        $itemsDefault = [['service_id' => '', 'talent_id' => '', 'quantity' => 1, 'price' => 0, 'discount' => 0, 'commission' => 0, 'commission_date' => null]];
        return view('admin.orders.form', [
            'order' => new Order([
                'order_date' => now()->toDateString(),
                'status' => 'paid', 'payment_method' => $paymentMethods[0] ?? 'QRIS',
                'created_by' => auth()->id(),
            ]),
            'services' => $services, 'talents' => $talents,
            'paymentMethods' => $paymentMethods, 'admins' => $admins,
            'servicesJson' => $servicesJson, 'itemsDefault' => $itemsDefault,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $order = DB::transaction(function () use ($data) {
            $order = Order::create($data['order']);
            foreach ($data['items'] as $item) {
                $order->items()->create($item);
            }
            // total & komisi final = agregat items
            $order->total = $order->items()->selectRaw('COALESCE(SUM(quantity * price - discount), 0) as t')->value('t');
            $order->commission = $order->items()->sum('commission');
            $order->save();
            return $order;
        });
        return redirect()->route('admin.orders.index')->with('success', 'Order ' . $order->displayNo() . ' berhasil dibuat. Nomor invoice otomatis: ' . $order->displayNo());
    }

    public function edit(Order $order)
    {
        $order->load('items');
        $services = Service::where('is_active', true)->orderBy('name')->get();
        foreach ($order->items as $it) {
            if ($it->service_id && ! $services->contains('id', $it->service_id) && $it->service) {
                $services->push($it->service);
            }
        }
        $talents = Talent::with('paymentMethod')->where('is_active', true)->orderBy('name')->get();
        if ($order->talent_id && ! $talents->contains('id', $order->talent_id) && $order->talent) {
            $talents->push($order->talent);
        }
        $paymentMethods = PaymentMethod::activeNames();
        if ($order->payment_method && ! in_array($order->payment_method, $paymentMethods)) {
            $paymentMethods[] = $order->payment_method;
        }
        $admins = User::orderBy('name')->get();
        $servicesJson = $this->servicesJson($services);
        $itemsDefault = $order->items->map(fn ($it) => [
            'service_id' => (string) $it->service_id,
            'talent_id' => (string) ($it->talent_id ?? ''),
            'quantity' => (int) $it->quantity,
            'price' => (float) $it->price,
            'discount' => (float) $it->discount,
            'commission' => (float) $it->commission,
            'commission_date' => $it->commission_date?->format('Y-m-d'),
        ])->values()->all();
        if ($itemsDefault === []) {
            $itemsDefault = [['service_id' => (string) ($order->service_id ?? ''), 'talent_id' => (string) ($order->talent_id ?? ''), 'quantity' => 1, 'price' => 0, 'discount' => 0, 'commission' => 0, 'commission_date' => $order->commission_date?->format('Y-m-d')]];
        }
        return view('admin.orders.form', compact('order', 'services', 'talents', 'paymentMethods', 'admins', 'servicesJson', 'itemsDefault'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($order, $data) {
            $order->items()->delete();
            foreach ($data['items'] as $item) {
                $order->items()->create($item);
            }
            $order->update($data['order']); // hook hitung ulang total & komisi dari items
        });
        return back()->with('success', 'Order ' . $order->displayNo() . ' diperbarui. Total & untung dihitung ulang otomatis.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Order dihapus.');
    }

    /** Katalog jasa untuk auto-harga & komisi di form (sudah jadi array biasa). */
    protected function servicesJson($services): array
    {
        $out = [];
        foreach ($services as $s) {
            if (! $s) {
                continue;
            }
            $out[(string) $s->id] = [
                'price' => (float) $s->default_price,
                'komisi_label' => $s->komisiLabel(),
                'komisi_tipe' => $s->komisi_tipe,
                'komisi' => (float) $s->komisi_talent,
            ];
        }
        return $out;
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'admin_id' => 'required|exists:users,id',
            'customer_name' => 'required|max:120',
            'customer_whatsapp' => 'nullable|max:30',
            'payment_method' => 'required|max:50',
            'status' => 'nullable|in:pending,paid,cancelled,refunded',
            'order_date' => 'required|date',
            'notes' => 'nullable|max:1000',
            'items' => 'required|array|min:1|max:20',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.talent_id' => 'nullable|exists:talents,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.commission' => 'nullable|numeric|min:0',
            'items.*.commission_date' => 'nullable|date',
        ]);

        $activePayments = PaymentMethod::where('is_active', true)->pluck('name')->all();
        if ($activePayments !== [] && ! in_array($data['payment_method'], $activePayments)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'payment_method' => 'Pilih metode pembayaran yang terdaftar di master.',
            ]);
        }

        $items = [];
        $total = 0;
        $komisi = 0;
        foreach ($data['items'] as $row) {
            $svc = Service::find($row['service_id']);
            $talent = ! empty($row['talent_id']) ? Talent::find($row['talent_id']) : null;
            $qty = (int) $row['quantity'];
            $price = (float) $row['price'];
            $disc = (float) ($row['discount'] ?? 0);
            $cms = (float) ($row['commission'] ?? 0);
            $items[] = [
                'service_id' => $svc->id,
                'service_name' => $svc->name,
                'talent_id' => $talent?->id,
                'quantity' => $qty,
                'price' => $price,
                'discount' => $disc,
                'commission' => $cms,
                'commission_date' => $row['commission_date'] ?? null,
            ];
            $total += max(0, $qty * $price - $disc);
            $komisi += max(0, $cms);
        }

        $first = $items[0];
        // Arsip level order: talent & tgl komisi baris pertama yang ada isinya
        $firstTalent = collect($items)->first(fn ($it) => ! empty($it['talent_id']));
        $firstDate = collect($items)->map(fn ($it) => $it['commission_date'])->filter()->sort()->first();
        $firstTalentName = $firstTalent ? Talent::find($firstTalent['talent_id'])?->name : null;

        return [
            'order' => [
                'created_by' => $data['admin_id'],
                'service_id' => $first['service_id'],
                'service_name' => $first['service_name'],
                'talent_id' => $firstTalent['talent_id'] ?? null,
                'talent_name' => $firstTalentName,
                'customer_name' => $data['customer_name'],
                'customer_whatsapp' => $data['customer_whatsapp'] ?? null,
                'quantity' => $first['quantity'],
                'price' => $first['price'],
                'discount' => $first['discount'],
                'commission' => $komisi,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'status' => $data['status'] ?? 'paid',
                'order_date' => $data['order_date'],
                'commission_date' => $firstDate,
                'notes' => $data['notes'] ?? null,
            ],
            'items' => $items,
        ];
    }
}
