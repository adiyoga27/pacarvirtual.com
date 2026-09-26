<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $methods = PaymentMethod::when($q, fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('account_number', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $stats = [
            'total' => PaymentMethod::count(),
            'aktif' => PaymentMethod::where('is_active', true)->count(),
            'nonaktif' => PaymentMethod::where('is_active', false)->count(),
        ];
        return view('admin.payment-methods.index', compact('methods', 'stats', 'q'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        PaymentMethod::create($data + ['is_active' => true]);
        return back()->with('success', 'Metode pembayaran "' . $data['name'] . '" ditambah.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $this->validated($request, $paymentMethod->id);
        $data['is_active'] = $request->boolean('is_active');
        $paymentMethod->update($data);
        return back()->with('success', 'Metode pembayaran "' . $paymentMethod->name . '" diperbarui.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return back()->with('success', 'Metode pembayaran dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|max:50|unique:payment_methods,name,' . ($ignoreId ?? 'NULL'),
            'account_number' => 'nullable|max:100',
            'account_name' => 'nullable|max:100',
            'description' => 'nullable|max:500',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]) + ['sort_order' => (int) $request->input('sort_order', 0)];
    }
}
