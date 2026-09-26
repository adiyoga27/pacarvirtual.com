<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share global dynamic settings ke semua view (content, background, icon, SEO, script)
        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::allAsArray();
                View::share('gsettings', $settings);
            }
        } catch (\Throwable $e) {
            View::share('gsettings', []);
        }
    }
}
