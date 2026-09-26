<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('talent_id')->nullable()->after('service_name')->constrained('talents')->nullOnDelete();
            $table->date('commission_date')->nullable()->after('commission');
        });

        // Backfill dari level order ke tiap barisnya
        foreach (\App\Models\Order::with('items')->get() as $o) {
            foreach ($o->items as $it) {
                $it->update([
                    'talent_id' => $o->talent_id,
                    'commission_date' => $o->commission_date,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('talent_id');
            $table->dropColumn('commission_date');
        });
    }
};
