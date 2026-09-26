<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Talent;
use Illuminate\Http\Request;

class TalentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $talents = Talent::with('paymentMethod')
            ->when($q, fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('account_number', 'like', "%{$q}%")))
            ->orderBy('name')
            ->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $stats = [
            'total' => Talent::count(),
            'aktif' => Talent::where('is_active', true)->count(),
            'nonaktif' => Talent::where('is_active', false)->count(),
        ];
        return view('admin.talents.index', compact('talents', 'paymentMethods', 'stats', 'q'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $talent = Talent::create($data + ['is_active' => true]);
        return back()->with('success', 'Talent "' . $talent->name . '" ditambah.');
    }

    public function update(Request $request, Talent $talent)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $talent->update($data);
        return back()->with('success', 'Talent "' . $talent->name . '" diperbarui.');
    }

    public function destroy(Talent $talent)
    {
        $talent->delete();
        return back()->with('success', 'Talent dihapus. Order lama tetap tersimpan (nama talent tidak hilang).');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|max:120',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'account_number' => 'nullable|max:100',
        ]);
    }
}
