<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->get();
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:120|unique:services,name',
            'default_price' => 'required|numeric|min:0',
            'description' => 'nullable|max:500',
        ]);
        Service::create($data + ['is_active' => true]);
        return back()->with('success', 'Layanan ditambah.');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => 'required|max:120|unique:services,name,' . $service->id,
            'default_price' => 'required|numeric|min:0',
            'description' => 'nullable|max:500',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $service->update($data);
        return back()->with('success', 'Layanan diperbarui.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Layanan dihapus.');
    }
}
