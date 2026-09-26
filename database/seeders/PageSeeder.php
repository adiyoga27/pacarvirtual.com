<?php

namespace Database\Seeders;

use App\Models\LinkButton;
use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'home', 'name' => 'Beranda', 'header_title' => 'Sewa Pacar',
                'subtitle' => null, 'show_logo' => true, 'show_back_button' => false,
                'meta_title' => 'PacarVirtual',
                'meta_description' => 'Specialis Rental Pacar menyediakan pengalaman berkencan untuk klien dengan pilihan secara Online & Offline',
                'meta_keywords' => 'sewa pacar, rental pacar, pacar virtual',
                'is_active' => true, 'sort_order' => 1,
                'buttons' => [
                    ['title' => 'Info order di WA', 'url' => '/admin-contact', 'icon_path' => 'assets-legacy/images/whatsapp.png', 'icon_width' => 30, 'open_new_tab' => false],
                    ['title' => 'Acara TV', 'url' => '/acara-tv', 'icon_path' => 'assets-legacy/images/television.png', 'icon_width' => 30, 'open_new_tab' => false],
                    ['title' => 'Artikel Berita', 'url' => '/article', 'icon_path' => 'assets-legacy/images/blog.webp', 'icon_width' => 40, 'open_new_tab' => false],
                    ['title' => 'Pricelist', 'url' => 'https://drive.google.com/drive/folders/1dlT3yEHp36nIZo1ElBk1fCVfkcE9iSZ1', 'icon_path' => 'assets-legacy/images/tag.png', 'icon_width' => 40],
                    ['title' => 'Review', 'url' => 'https://vt.tiktok.com/ZS2VxsmRF/', 'icon_path' => 'assets-legacy/images/rating.png', 'icon_width' => 40],
                    ['title' => 'Testimoni', 'url' => '/testimoni', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 40, 'open_new_tab' => false],
                    ['title' => 'Foto Kandidat', 'url' => '/kandidat', 'icon_path' => 'assets-legacy/images/rules.png', 'icon_width' => 40, 'open_new_tab' => false],
                ],
            ],
            [
                'slug' => 'admin-contact', 'name' => 'Admin Contact', 'header_title' => 'Sewa Pacar',
                'subtitle' => null, 'show_back_button' => true,
                'meta_title' => 'Sewa Pacar - Admin Contact',
                'meta_description' => 'Specialis Rental Pacar menyediakan pengalaman berkencan untuk klien dengan pilihan secara Online & Offline',
                'buttons' => [
                    ['title' => 'Admin 1 : 082137672977', 'url' => 'https://api.whatsapp.com/send/?phone=6282137672977&text&type=phone_number&app_absent=0', 'icon_path' => 'assets-legacy/images/whatsapp.png', 'icon_width' => 30],
                    ['title' => 'Admin 2 : 087818509230', 'url' => 'https://api.whatsapp.com/send/?phone=6287818509230&text&type=phone_number&app_absent=0', 'icon_path' => 'assets-legacy/images/whatsapp.png', 'icon_width' => 30],
                ],
            ],
            [
                'slug' => 'owner-contact', 'name' => 'Owner Contact', 'header_title' => 'Sewa Pacar',
                'show_back_button' => true, 'meta_title' => 'Sewa Pacar - Owner',
                'meta_description' => 'Hubungi owner PacarVirtual',
                'buttons' => [
                    ['title' => 'Hubungi Owner', 'url' => 'https://wa.me/6287873764785', 'icon_path' => 'assets-legacy/images/whatsapp.png', 'icon_width' => 30],
                ],
            ],
            [
                'slug' => 'pricelist', 'name' => 'Pricelist', 'header_title' => 'Sewa Pacar',
                'show_back_button' => true,
                'meta_title' => 'PacarVirtual - Pricelist',
                'meta_description' => 'Berikut pricelist pacarvirtual, yuk di order kakak <3',
                'buttons' => [
                    ['title' => 'Pricelist 1', 'url' => 'https://www.instagram.com/p/DIYJF2NTRvj/?igsh=bnN5cGJyYmNmZjZv', 'icon_path' => 'assets-legacy/images/price.png', 'icon_width' => 40],
                    ['title' => 'Pricelist 2', 'url' => 'https://www.instagram.com/p/DIYJMHkTN2h/?igsh=MWZiNnpmYTgwMWd5OQ==', 'icon_path' => 'assets-legacy/images/price.png', 'icon_width' => 40],
                ],
            ],
            [
                'slug' => 'kandidat', 'name' => 'Foto Kandidat', 'header_title' => 'Sewa Pacar',
                'show_back_button' => true, 'meta_title' => 'Sewa Pacar - Kandidat',
                'meta_description' => 'Specialis Rental Pacar menyediakan pengalaman berkencan untuk klien dengan pilihan secara Online & Offline',
                'meta_keywords' => 'kandidat, talent',
                'buttons' => [
                    ['title' => 'Kandidat Perempuan', 'url' => 'https://drive.google.com/drive/folders/12aEltmv-MSVHPfWhNILqoqKSWUIkpAJy', 'icon_path' => 'assets-legacy/images/rules.png', 'icon_width' => 50],
                    ['title' => 'Kandidat Laki-Laki', 'url' => 'https://drive.google.com/drive/folders/1dGfk0bmCsGnDTDgyBiAHay5sfdVM8sqE', 'icon_path' => 'assets-legacy/images/rules.png', 'icon_width' => 50],
                ],
            ],
            [
                'slug' => 'testimoni', 'name' => 'Testimoni', 'header_title' => 'Sewa Pacar',
                'show_back_button' => true, 'meta_title' => 'Sewa Pacar - Testimoni',
                'meta_description' => 'Specialis Rental Pacar Dijamin talent-talentnya bikin hari harimu indah kak',
                'buttons' => [
                    ['title' => 'Testimoni Offline Date', 'url' => 'https://vt.tiktok.com/ZS2Vxbpfx/', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'Testimoni Buzzer', 'url' => 'https://vt.tiktok.com/ZS2VxXUeU/', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'Testimoni Videocall', 'url' => 'https://vt.tiktok.com/ZS2VQ65VF/', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 40],
                    ['title' => 'Testimoni Call', 'url' => 'https://vt.tiktok.com/ZS2VQwLup/', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 40],
                ],
            ],
            [
                'slug' => 'article', 'name' => 'Artikel Berita', 'header_title' => 'Sewa Pacar',
                'subtitle' => 'Kumpulan Berita @pacarvirtual.co_', 'show_back_button' => true,
                'meta_title' => 'Sewa Pacar - Artikel',
                'meta_description' => 'Specialis Rental Pacar Dijamin talent-talentnya bikin hari harimu indah kak',
                'buttons' => [
                    ['title' => 'kabarbaru.co', 'url' => 'https://kabarbaru.co/mengenal-jasa-rental-pacar-banyak-yang-kesepian-di-indonesia-jasa-sewa-pacar-pun-semakin-diminati/', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'dailynusantara.com', 'url' => 'https://www.dailynusantara.com/jasa-rental-pacar-chat-hingga-teman-kencan/', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'kabartren.com', 'url' => 'https://www.kabartren.com/jasa-rental-pacar-antara-kebutuhan-sosial-dan-peluang-inovatif/', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'kuasakata.com', 'url' => 'https://kuasakata.com/read/berita/103806-mengenal-jasa-rental-pacar-tidak-hanya-untuk-mahasiswa-pria-30-an-pun-tertarik', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'nalarrakyat.com', 'url' => 'https://www.nalarrakyat.com/2025/02/jasa-sewa-pacar-mulai-populer-di.html', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'rakyatsipil.com', 'url' => 'https://www.rakyatsipil.com/jasa-sewa-pacar-mulai-populer-di-indonesia-dengan-budget-sangat-murah/', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'sabdaguru.com', 'url' => 'https://www.sabdaguru.com/2025/02/perkembangan-bisnis-online-yaitu.html', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'portaldemokrasi.com', 'url' => 'https://www.portaldemokrasi.com/2025/02/sewa-pacar-online-offline-tren-bisnis.html', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'jogjapekan.com', 'url' => 'https://www.jogjapekan.com/2025/02/sewa-pacar-strategi-tantangan-dan.html', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                    ['title' => 'kompasiana.com', 'url' => 'https://www.kompasiana.com/feraagustina21/67b71be834777c277c280a72/jasa-sewa-pacar-mulai-populer-bahkan-banyak-peminat-di-negara-indonesia', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                ],
            ],
            [
                'slug' => 'review', 'name' => 'Review', 'header_title' => 'Sewa Pacar',
                'show_back_button' => true, 'meta_title' => 'Sewa Pacar - Review',
                'meta_description' => 'Specialis Rental Pacar Dijamin talent-talentnya bikin hari harimu indah kak',
                'buttons' => [
                    ['title' => 'CEK KOLOM KOMENTAR', 'url' => 'https://www.instagram.com/p/C8FELcxhXlh/?igsh=a2oyY3ZlZm10YW5h', 'icon_path' => 'assets-legacy/images/reviewer.png', 'icon_width' => 30],
                ],
            ],
            [
                'slug' => 'acara-tv', 'name' => 'Acara TV', 'header_title' => 'Sewa Pacar',
                'show_back_button' => true, 'meta_title' => 'Sewa Pacar - Acara TV',
                'meta_description' => 'Specialis Rental Pacar menyediakan pengalaman berkencan untuk klien dengan pilihan secara Online & Offline',
                'buttons' => [
                    ['title' => 'On The Spot Trans7', 'url' => 'https://youtu.be/04b8ZzFuHIA?si=oL9SMI-thELTNuD1', 'icon_path' => 'assets-legacy/images/trans7.png', 'icon_width' => 50],
                    ['title' => 'Lapor Pak Trans7', 'url' => '/lapor-pak-trans7', 'icon_path' => 'assets-legacy/images/trans7.png', 'icon_width' => 50, 'open_new_tab' => false],
                ],
            ],
            [
                'slug' => 'lapor-pak-trans7', 'name' => 'Lapor Pak Trans7', 'header_title' => 'Sewa Pacar',
                'show_back_button' => true, 'meta_title' => 'Sewa Pacar - Lapor Pak',
                'meta_description' => 'Specialis Rental Pacar menyediakan pengalaman berkencan untuk klien dengan pilihan secara Online & Offline',
                'buttons' => [
                    ['title' => 'Part 1', 'url' => 'https://youtu.be/NTePUmOY4aI?si=_rLaf7nn-YxLcAQL', 'icon_path' => 'assets-legacy/images/trans7.png', 'icon_width' => 50],
                    ['title' => 'Part 2', 'url' => 'https://youtu.be/I-hd8O3A8Z0?si=vaDjwdFpMIHDr6vY', 'icon_path' => 'assets-legacy/images/trans7.png', 'icon_width' => 50],
                ],
            ],
            [
                'slug' => 'toc', 'name' => 'Syarat & Ketentuan', 'header_title' => 'Sewa Pacar',
                'show_back_button' => true, 'meta_title' => 'Sewa Pacar - Rules',
                'meta_description' => 'Berikut pricelist pacarvirtual, yuk di order kakak <3',
                'buttons' => [
                    ['title' => 'RULES ONLINE DATE', 'url' => 'https://www.instagram.com/p/C8FEiHshxqN/?igsh=bml4c2UyeW9pMWkx', 'icon_path' => 'assets-legacy/images/price.png', 'icon_width' => 40],
                    ['title' => 'RULES OFFLINE DATE', 'url' => 'https://www.instagram.com/p/C8FEmDHh0IP/?igsh=MWxxaXlxeDMxMGZiNQ==', 'icon_path' => 'assets-legacy/images/price.png', 'icon_width' => 40],
                ],
            ],
        ];

        foreach ($pages as $i => $p) {
            $buttons = $p['buttons'] ?? [];
            unset($p['buttons']);
            $p['meta_author'] = $p['meta_author'] ?? 'https://pacarvirtual.com/';
            $p['is_active'] = $p['is_active'] ?? true;
            $p['sort_order'] = $i + 1;
            $page = Page::updateOrCreate(['slug' => $p['slug']], $p);
            // reset buttons agar seeder idempotent
            $page->buttons()->delete();
            foreach (array_values($buttons) as $j => $b) {
                LinkButton::create([
                    'page_id' => $page->id,
                    'title' => $b['title'],
                    'url' => $b['url'],
                    'icon_path' => $b['icon_path'] ?? null,
                    'icon_width' => $b['icon_width'] ?? 40,
                    'open_new_tab' => $b['open_new_tab'] ?? true,
                    'is_active' => true,
                    'sort_order' => $j + 1,
                ]);
            }
        }
    }
}
