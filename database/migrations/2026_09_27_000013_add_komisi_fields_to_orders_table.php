<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('service_name')->constrained('services')->nullOnDelete();
            $table->foreignId('talent_id')->nullable()->after('talent_name')->constrained('talents')->nullOnDelete();
            $table->date('commission_date')->nullable()->after('order_date');
            $table->decimal('commission', 15, 2)->default(0)->after('discount');
            $table->decimal('disbursed', 15, 2)->default(0)->after('commission');
        });

        // Backfill relasi dari nama string yang sudah ada
        foreach (\App\Models\Service::all() as $s) {
            \App\Models\Order::where('service_name', $s->name)->whereNull('service_id')->update(['service_id' => $s->id]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
            $table->dropConstrainedForeignId('talent_id');
            $table->dropColumn(['commission_date', 'commission', 'disbursed']);
        });
    }
};
