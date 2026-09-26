<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Online Chat', 'default_price' => 50000, 'komisi_talent' => 20000, 'komisi_tipe' => 'nominal'],
            ['name' => 'Video Call', 'default_price' => 100000, 'komisi_talent' => 40, 'komisi_tipe' => 'persen'],
            ['name' => 'Call', 'default_price' => 75000, 'komisi_talent' => 30000, 'komisi_tipe' => 'nominal'],
            ['name' => 'Offline Date', 'default_price' => 500000, 'komisi_talent' => 50, 'komisi_tipe' => 'persen'],
            ['name' => 'Buzzer', 'default_price' => 150000, 'komisi_talent' => 60000, 'komisi_tipe' => 'nominal'],
            ['name' => 'Paket Exclusive', 'default_price' => 1000000, 'komisi_talent' => 50, 'komisi_tipe' => 'persen'],
            ['name' => 'Akun Premium', 'default_price' => 80000, 'komisi_talent' => 0, 'komisi_tipe' => 'nominal'],
        ];
        foreach ($services as $s) {
            Service::updateOrCreate(['name' => $s['name']], $s + ['is_active' => true]);
        }
    }
}
