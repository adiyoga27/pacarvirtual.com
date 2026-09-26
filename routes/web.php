<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LinkButtonController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PageViewController;
use Illuminate\Support\Facades\Route;

// ===== Frontend dinamis (link-in-bio) =====
Route::get('/', [PageViewController::class, 'index'])->name('home');
Route::get('/link/{id}/click', [PageViewController::class, 'click'])->name('link.click');

// ===== Admin Auth =====
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ===== Admin Panel (modern, Tailwind, custom) =====
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Halaman & tombol link
    Route::resource('pages', PageController::class)->except(['show']);
    Route::get('pages/{page}/buttons', [LinkButtonController::class, 'index'])->name('pages.buttons.index');
    Route::get('pages/{page}/buttons/create', [LinkButtonController::class, 'create'])->name('pages.buttons.create');
    Route::post('pages/{page}/buttons', [LinkButtonController::class, 'store'])->name('pages.buttons.store');
    Route::get('pages/{page}/buttons/{button}/edit', [LinkButtonController::class, 'edit'])->name('pages.buttons.edit');
    Route::put('pages/{page}/buttons/{button}', [LinkButtonController::class, 'update'])->name('pages.buttons.update');
    Route::delete('pages/{page}/buttons/{button}', [LinkButtonController::class, 'destroy'])->name('pages.buttons.destroy');

    // Pengaturan dinamis: content, background, icon, SEO, script
    Route::get('settings/{group?}', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings/{group?}', [SettingController::class, 'update'])->name('settings.update');

    // Penjualan & laporan omzet
    Route::resource('orders', OrderController::class)->except(['show']);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Master layanan & admin
    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// ===== Halaman dinamis per slug (paling bawah agar tidak menabrak /admin) =====
Route::get('/{slug}', [PageViewController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('page.show');
