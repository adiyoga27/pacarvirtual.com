<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Judul & deskripsi UTAMA (General) — dipakai homepage & preview share WA
        // firstOrCreate agar tidak menimpa bila sudah ada
        \App\Models\Setting::firstOrCreate(
            ['key' => 'meta_title'],
            ['value' => 'PacarVirtual', 'group' => 'general', 'type' => 'text', 'label' => 'Meta Title Utama (judul preview WA/link)']
        );
        \App\Models\Setting::firstOrCreate(
            ['key' => 'meta_description'],
            ['value' => 'Specialis Rental Pacar menyediakan pengalaman berkencan untuk klien dengan pilihan secara Online & Offline', 'group' => 'general', 'type' => 'textarea', 'label' => 'Meta Description Utama (deskripsi preview WA/link)']
        );
    }

    public function down(): void
    {
        \App\Models\Setting::whereIn('key', ['meta_title', 'meta_description'])->delete();
    }
};
