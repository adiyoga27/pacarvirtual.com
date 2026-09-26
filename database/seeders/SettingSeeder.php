<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // General
            ['key' => 'site_name', 'value' => 'PacarVirtual', 'group' => 'general', 'type' => 'text', 'label' => 'Nama Situs'],
            ['key' => 'tagline', 'value' => 'Sewa Pacar', 'group' => 'general', 'type' => 'text', 'label' => 'Tagline / Judul Header'],
            ['key' => 'footer_text', 'value' => 'Pacarvirtual.co_', 'group' => 'general', 'type' => 'text', 'label' => 'Teks Footer'],
            // Appearance (semua dynamic)
            ['key' => 'logo', 'value' => 'assets-legacy/logo.png', 'group' => 'appearance', 'type' => 'image', 'label' => 'Logo Utama'],
            ['key' => 'favicon', 'value' => 'assets-legacy/images/icon.png', 'group' => 'appearance', 'type' => 'image', 'label' => 'Favicon / Icon'],
            ['key' => 'background_image', 'value' => 'assets-legacy/background.png', 'group' => 'appearance', 'type' => 'image', 'label' => 'Background Utama'],
            ['key' => 'background_color', 'value' => '#1a0b2e', 'group' => 'appearance', 'type' => 'color', 'label' => 'Warna Background (fallback)'],
            ['key' => 'background_type', 'value' => 'image', 'group' => 'appearance', 'type' => 'text', 'label' => 'Tipe Background (image/color/gradient)'],
            ['key' => 'background_gradient', 'value' => 'linear-gradient(135deg,#2b1055,#7597de)', 'group' => 'appearance', 'type' => 'text', 'label' => 'Gradient Background'],
            ['key' => 'font_family', 'value' => '"Nunito", sans-serif', 'group' => 'appearance', 'type' => 'text', 'label' => 'Font Family'],
            ['key' => 'primary_color', 'value' => '#c76e91', 'group' => 'appearance', 'type' => 'color', 'label' => 'Warna Primer (tombol)'],
            ['key' => 'text_color', 'value' => '#ffffff', 'group' => 'appearance', 'type' => 'color', 'label' => 'Warna Teks'],
            ['key' => 'button_radius', 'value' => '30', 'group' => 'appearance', 'type' => 'text', 'label' => 'Radius Tombol (px)'],
            // Teks animasi berjalan (typed.js) di bawah judul — tampil bila halaman tidak punya subtitle sendiri
            ['key' => 'typed_enabled', 'value' => '0', 'group' => 'appearance', 'type' => 'text', 'label' => 'Animasi Teks Aktif (1 = ya, 0 = tidak)'],
            ['key' => 'typed_prefix', 'value' => 'Hidup ', 'group' => 'appearance', 'type' => 'text', 'label' => 'Awalan Teks Animasi'],
            ['key' => 'typed_strings', 'value' => "Bahagia 💫\nPenuh Warna\nHilangi Ke Galauan 🔥", 'group' => 'appearance', 'type' => 'textarea', 'label' => 'Teks Animasi (satu baris = satu teks)'],
            // SEO global
            ['key' => 'meta_author', 'value' => 'https://pacarvirtual.com/', 'group' => 'seo', 'type' => 'text', 'label' => 'Meta Author'],
            ['key' => 'meta_keywords_default', 'value' => 'sewa pacar, rental pacar, pacar virtual, teman kencan', 'group' => 'seo', 'type' => 'text', 'label' => 'Meta Keywords Default'],
            ['key' => 'og_image_default', 'value' => 'assets-legacy/logo.png', 'group' => 'seo', 'type' => 'image', 'label' => 'OG Image Default'],
            // Script / tracking
            ['key' => 'gtm_id', 'value' => 'GTM-NNH5QJ7C', 'group' => 'script', 'type' => 'text', 'label' => 'Google Tag Manager ID'],
            ['key' => 'gtag_id', 'value' => 'G-FQ62YTTLFM', 'group' => 'script', 'type' => 'text', 'label' => 'Google Analytics / GTag ID'],
            ['key' => 'custom_head', 'value' => '', 'group' => 'script', 'type' => 'textarea', 'label' => 'Custom <head> (opsional)'],
            ['key' => 'custom_body', 'value' => '', 'group' => 'script', 'type' => 'textarea', 'label' => 'Custom script body (opsional)'],
        ];

        foreach ($data as $row) {
            Setting::updateOrCreate(['key' => $row['key']], $row);
        }
    }
}
