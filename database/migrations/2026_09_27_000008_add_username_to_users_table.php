<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->nullable()->after('name');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });

        // Backfill username dari prefix email untuk user lama
        foreach (\App\Models\User::whereNull('username')->get() as $u) {
            $base = strtolower(preg_replace('/[^a-z0-9_]/', '', explode('@', $u->email)[0] ?? 'admin'));
            $base = $base !== '' ? $base : 'admin';
            $candidate = $base;
            $i = 1;
            while (\App\Models\User::where('username', $candidate)->exists()) {
                $candidate = $base . $i;
                $i++;
            }
            $u->update(['username' => $candidate]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'last_login_at']);
        });
    }
};
