<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $services = Service::when($q, fn ($query) => $query->where(fn ($w) => $w
                ->where('name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")))
            ->orderBy('name')
            ->get();
        $stats = [
            'total' => Service::count(),
            'aktif' => Service::where('is_active', true)->count(),
            'nonaktif' => Service::where('is_active', false)->count(),
        ];
        return view('admin.services.index', compact('services', 'stats', 'q'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Service::create($data + ['is_active' => true]);
        return back()->with('success', 'Layanan "' . $data['name'] . '" ditambah.');
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request, $service->id);
        $data['is_active'] = $request->boolean('is_active');
        $service->update($data);
        return back()->with('success', 'Layanan "' . $service->name . '" diperbarui.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Layanan dihapus.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => 'required|max:120|unique:services,name,' . ($ignoreId ?? 'NULL'),
            'default_price' => 'required|numeric|min:0|max:9999999999',
            'komisi_talent' => 'nullable|numeric|min:0|max:9999999999',
            'komisi_tipe' => 'required|in:nominal,persen',
            'description' => 'nullable|max:500',
        ], [
            'komisi_tipe.in' => 'Tipe komisi harus nominal atau persen.',
        ]);

        // Validasi khusus persen: maksimal 100
        if (($data['komisi_tipe'] ?? 'nominal') === 'persen' && (float) ($data['komisi_talent'] ?? 0) > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'komisi_talent' => 'Komisi persen maksimal 100%.',
            ]);
        }

        $data['komisi_talent'] = $data['komisi_talent'] ?? 0;

        return $data;
    }
}
