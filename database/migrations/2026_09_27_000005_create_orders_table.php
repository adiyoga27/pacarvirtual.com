<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('customer_name');
            $table->string('customer_whatsapp')->nullable();
            $table->string('service_name');
            $table->string('talent_name')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('payment_method')->default('QRIS');
            $table->string('status')->default('pending'); // pending, paid, cancelled, refunded
            $table->date('order_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['order_date', 'status']);
            $table->index('service_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
