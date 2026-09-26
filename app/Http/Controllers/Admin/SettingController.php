<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(string $group = 'general')
    {
        $allowed = ['general', 'appearance', 'seo', 'script'];
        if (! in_array($group, $allowed)) {
            $group = 'general';
        }
        $settings = Setting::where('group', $group)->orderBy('id')->get();
        return view('admin.settings.index', compact('settings', 'group', 'allowed'));
    }

    public function update(Request $request, string $group = 'general')
    {
        $settings = Setting::where('group', $group)->get();
        foreach ($settings as $setting) {
            $key = $setting->key;
            if ($setting->type === 'image') {
                $fileKey = 'file_' . $key;
                if ($request->hasFile($fileKey)) {
                    $request->validate([$fileKey => 'image|mimes:jpg,jpeg,png,webp,ico,svg|max:4096']);
                    if ($setting->value && ! str_starts_with($setting->value, 'assets-legacy')) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    $setting->value = $request->file($fileKey)->store('settings', 'public');
                    $setting->save();
                }
                // URL manual alternatif
                if ($request->filled('url_' . $key)) {
                    $setting->value = $request->input('url_' . $key);
                    $setting->save();
                }
            } else {
                $setting->value = $request->input($key, $setting->value);
                $setting->save();
            }
        }
        return back()->with('success', 'Pengaturan ' . ucfirst($group) . ' berhasil disimpan. Semua halaman frontend otomatis ikut berubah.');
    }
}
