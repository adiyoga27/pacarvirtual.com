<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('service_name', 120);
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('invoice_no', 30)->unique()->nullable()->after('order_code');
        });

        // Backfill: 1 item per order lama + nomor invoice berurutan per bulan order
        $seqPerMonth = [];
        foreach (\App\Models\Order::orderBy('id')->get() as $o) {
            \App\Models\OrderItem::create([
                'order_id' => $o->id,
                'service_id' => $o->service_id,
                'service_name' => $o->service_name ?: 'Layanan',
                'quantity' => $o->quantity ?: 1,
                'price' => $o->price,
                'discount' => $o->discount,
                'commission' => $o->commission,
            ]);
            $key = $o->order_date ? $o->order_date->format('Y/m') : now()->format('Y/m');
            $seqPerMonth[$key] = ($seqPerMonth[$key] ?? 0) + 1;
            $o->update(['invoice_no' => 'INV/' . $key . '/' . str_pad($seqPerMonth[$key], 4, '0', STR_PAD_LEFT)]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('invoice_no');
        });
    }
};
