# PacarVirtual — Laravel CMS Dinamis

Migrasi situs PHP native (`index.php`, `pricelist.php`, `kandidat.php`, dll) menjadi **project Laravel modern**.
Semua **content, background, icon, SEO, script (GTM/GTag)** tersimpan di database MySQL **`pacarvirtual`** (user `root`, password kosong) dan bisa diubah dari admin panel tanpa coding.

## 1. Syarat & Instalasi

```powershell
cd C:\Users\ASUS\Documents\Development\pcr\pacarvirtual
composer install
copy .env.example .env   # lalu sesuaikan DB di bawah (sudah terisi default)
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8099
```

`.env` yang dipakai:
```
APP_NAME=PacarVirtual
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pacarvirtual
DB_USERNAME=root
DB_PASSWORD=
FILESYSTEM_DISK=public
```

## 2. Akun Admin

- URL: `http://127.0.0.1:8099/admin/login`
- Email: `admin@pacarvirtual.com`
- Password: `password`

## 3. Struktur Dinamis (Database)

| Tabel | Fungsi |
|---|---|
| `settings` | Key-value global: `site_name`, `logo`, `favicon`, `background_image/color/gradient/type`, `font_family`, `primary_color`, `text_color`, `button_radius`, `meta_*`, `og_image_default`, `gtm_id`, `gtag_id`, `custom_head/body`, `footer_text` |
| `pages` | Pengganti tiap file PHP lama: `home`, `pricelist`, `kandidat`, `testimoni`, `article`, `review`, `admin-contact`, `owner-contact`, `acara-tv`, `lapor-pak-trans7`, `toc`. Kolom: header, subtitle, logo khusus, background khusus / pakai global, SEO per halaman, footer, aktif, urutan |
| `link_buttons` | Tombol per halaman: judul, URL (internal `/slug` atau eksternal), icon upload, lebar icon, tab baru, aktif, urutan, `click_count` otomatis |
| `click_logs` | Log tiap klik tombol (IP, user agent) untuk dashboard |
| `services` | Master layanan (dropdown order & laporan) |
| `orders` | Transaksi: kode, pelanggan, WA, layanan, talent, qty, harga, diskon, **total otomatis**, pembayaran, status, tanggal, catatan |
| `users` | Admin (`role=admin`) |

Asset lama disalin ke `public/assets-legacy/` sehingga seed awal tetap menampilkan logo/background/icon lama. Semua bisa diganti via upload di admin (tersimpan di `storage/app/public/`).

## 4. Frontend (Publik)

- `/` → halaman `home`
- `/{slug}` → halaman dinamis lain (cth: `/pricelist`, `/kandidat`, `/testimoni`, `/article`, `/review`, `/admin-contact`, `/owner-contact`, `/acara-tv`, `/lapor-pak-trans7`, `/toc`)
- `/link/{id}/click` → redirect tombol + catat klik otomatis
- Layout `resources/views/frontend/layout.blade.php` membaca SEO/background/logo/GTM dari DB. Tidak ada lagi hardcode di file PHP.

## 5. Admin Panel (Modern, Tailwind, Tanpa Filament)

Custom layout `resources/views/admin/layout.blade.php` — sidebar gelap, kartu statistik, Chart.js, Alpine.js. Menu:

- **Dashboard** (`/admin`): omzet hari ini / bulan ini / total, pending, grafik 30 hari, omzet per layanan, order terbaru, tombol terlaris
- **Halaman & Tombol** (`/admin/pages`): CRUD halaman + SEO per halaman + background/logo khusus; kelola tombol per halaman (icon upload, urutan, aktif)
- **Tampilan & SEO** (`/admin/settings/{general,appearance,seo,script}`): semua key-value + upload gambar
- **Data Order** (`/admin/orders`): CRUD + filter + total otomatis
- **Laporan Omzet** (`/admin/reports`): filter periode/layanan/pembayaran/status, ringkasan omzet/trx/rata-rata/diskon, grafik harian, tabel per layanan & pembayaran, **export CSV**
- **Layanan** (`/admin/services`), **Admin** (`/admin/users`)

## 6. Laporan Omzet — Cara Pakai

1. Buka `/admin/reports`
2. Pilih `dari–sampai`, layanan, pembayaran, status (default `paid` agar omzet valid)
3. Klik **Tampilkan** untuk grafik + tabel, klik ikon **download** untuk export CSV (`laporan-omzet-{from}_{to}.csv`)

Rumus total per order: `(quantity × price) − discount` (dihitung otomatis di Model `Order::saving`).

## 7. Deploy ke Hosting (cPanel)

1. Buat DB `pacarvirtual`, import via `php artisan migrate --seed` atau export SQL lokal
2. Upload isi project (kecuali `vendor/`, `node_modules/`), jalankan `composer install --no-dev -o`, `php artisan storage:link`, `php artisan config:cache`
3. Arahkan document root ke `public/`, atau jika shared hosting tanpa akses root, pindahkan isi `public/` ke `public_html/` dan sesuaikan `index.php`
4. `.user.ini` / PHP 8.3 sudah tersedia di folder lama — pastikan versi PHP hosting ≥ 8.3
