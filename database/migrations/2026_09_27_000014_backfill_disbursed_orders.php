<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // DANA CAIR = UNTUNG = total − komisi untuk order lama
        DB::table('orders')->update([
            'disbursed' => DB::raw('GREATEST(total - commission, 0)'),
        ]);
    }

    public function down(): void
    {
        // tidak perlu dikembalikan
    }
};
