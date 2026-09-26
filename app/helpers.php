<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('pv_asset')) {
    /**
     * Resolve path gambar dinamis dari DB:
     * - URL penuh (http...) -> kembalikan apa adanya
     * - path storage (uploads/...) -> Storage::url
     * - path assets-legacy/... -> asset()
     */
    function pv_asset(?string $path, string $fallback = ''): string
    {
        if (empty($path)) {
            return $fallback ? asset($fallback) : '';
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }
        // file di storage/app/public/uploads/...
        if (str_starts_with($path, 'uploads/') || str_starts_with($path, 'settings/') || str_starts_with($path, 'pages/') || str_starts_with($path, 'icons/')) {
            return Storage::disk('public')->url($path);
        }
        return asset($path);
    }
}

if (! function_exists('pv_setting')) {
    function pv_setting(string $key, $default = null)
    {
        try {
            return \App\Models\Setting::get($key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (! function_exists('rupiah')) {
    function rupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }
}
