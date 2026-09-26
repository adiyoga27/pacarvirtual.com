<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'QRIS', 'description' => 'Scan QR semua e-wallet & m-banking'],
            ['name' => 'Transfer Bank', 'description' => 'Transfer antar bank'],
            ['name' => 'E-Wallet', 'description' => 'DANA / OVO / GoPay / ShopeePay'],
            ['name' => 'Cash', 'description' => 'Tunai saat offline date'],
            ['name' => 'Lainnya', 'description' => 'Metode lain'],
        ];
        foreach ($methods as $i => $m) {
            PaymentMethod::updateOrCreate(['name' => $m['name']], $m + ['is_active' => true, 'sort_order' => $i + 1]);
        }
    }
}
