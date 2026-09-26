<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('komisi_talent', 15, 2)->default(0)->after('default_price');
            $table->string('komisi_tipe', 10)->default('nominal')->after('komisi_talent'); // nominal | persen
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['komisi_talent', 'komisi_tipe']);
        });
    }
};
