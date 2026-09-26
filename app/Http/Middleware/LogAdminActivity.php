<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat aksi tulis di /admin/*, dan hanya yang sukses (<400)
        if (! $request->is('admin/*')) {
            return $response;
        }
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }
        if ($response->getStatusCode() >= 400) {
            return $response;
        }
        // Login/logout sudah dicatat manual di AuthController (biar pesannya rapi)
        if ($request->is('admin/login') || $request->is('admin/logout')) {
            return $response;
        }
        if (! auth()->check()) {
            return $response;
        }

        $action = match ($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'update',
        };

        $path = $request->path(); // cth: admin/pages/3/buttons
        $module = 'lainnya';
        if (str_contains($path, 'admin/pages') && str_contains($path, 'buttons')) {
            $module = 'buttons';
        } elseif (str_contains($path, 'admin/pages')) {
            $module = 'pages';
        } elseif (str_contains($path, 'admin/orders')) {
            $module = 'orders';
        } elseif (str_contains($path, 'admin/users')) {
            $module = 'users';
        } elseif (str_contains($path, 'admin/settings')) {
            $module = 'settings';
        } elseif (str_contains($path, 'admin/services')) {
            $module = 'services';
        } elseif (str_contains($path, 'admin/payment-methods')) {
            $module = 'payment-methods';
        } elseif (str_contains($path, 'admin/talents')) {
            $module = 'talents';
        } elseif (str_contains($path, 'admin/reports')) {
            $module = 'reports';
        }

        // Users sudah dicatat manual dengan pesan lebih jelas — jangan dobel
        if ($module === 'users') {
            return $response;
        }

        $label = match ($module) {
            'pages' => 'halaman',
            'buttons' => 'tombol link',
            'orders' => 'order',
            'settings' => 'pengaturan',
            'services' => 'layanan',
            'payment-methods' => 'metode pembayaran',
            'talents' => 'talent',
            'reports' => 'laporan',
            default => $module,
        };
        $verb = match ($action) {
            'create' => 'Tambah',
            'update' => 'Ubah',
            'delete' => 'Hapus',
            default => $action,
        };

        ActivityLog::record($action, $module, "{$verb} {$label} ({$request->method()} {$path})");

        return $response;
    }
}
