<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Online Chat', 'default_price' => 50000],
            ['name' => 'Video Call', 'default_price' => 100000],
            ['name' => 'Call', 'default_price' => 75000],
            ['name' => 'Offline Date', 'default_price' => 500000],
            ['name' => 'Buzzer', 'default_price' => 150000],
            ['name' => 'Paket Exclusive', 'default_price' => 1000000],
            ['name' => 'Akun Premium', 'default_price' => 80000],
        ];
        foreach ($services as $s) {
            Service::updateOrCreate(['name' => $s['name']], $s + ['is_active' => true]);
        }
    }
}
