<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (Order::count() > 0) {
            return;
        }
        $admin = User::where('email', 'admin@pacarvirtual.com')->first();
        $services = [
            ['Online Chat', 50000], ['Video Call', 100000], ['Call', 75000],
            ['Offline Date', 500000], ['Buzzer', 150000], ['Paket Exclusive', 1000000],
        ];
        $payments = ['QRIS', 'Transfer Bank', 'E-Wallet', 'Cash'];
        $names = ['Andi', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hendra', 'Intan', 'Joko', 'Kirana', 'Lukman', 'Maya', 'Nadia', 'Putri', 'Rizky', 'Salsa', 'Tania', 'Umar', 'Vina'];
        $talents = ['Talent A', 'Talent B', 'Talent C', 'Talent D', null];

        for ($i = 0; $i < 80; $i++) {
            $daysAgo = rand(0, 89);
            $date = now()->subDays($daysAgo);
            [$svc, $price] = $services[array_rand($services)];
            $qty = rand(1, 3);
            $discount = rand(0, 1) ? rand(0, 20000) : 0;
            $status = collect(['paid', 'paid', 'paid', 'paid', 'pending', 'cancelled'])->random();
            Order::create([
                'order_code' => 'PV-' . $date->format('Ymd') . '-' . strtoupper(substr(uniqid($i), -5)) . $i,
                'customer_name' => $names[array_rand($names)] . ' ' . rand(1, 99),
                'customer_whatsapp' => '62812' . rand(10000000, 99999999),
                'service_name' => $svc,
                'talent_name' => $talents[array_rand($talents)],
                'quantity' => $qty,
                'price' => $price,
                'discount' => $discount,
                'payment_method' => $payments[array_rand($payments)],
                'status' => $status,
                'order_date' => $date->toDateString(),
                'notes' => null,
                'created_by' => $admin?->id,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
