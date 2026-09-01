# Catatan Perbaikan Pentest (2026-08-31)

Perbaikan ini merespons laporan pentest terhadap `mirai.nexidn.my.id`
(skor awal 0/100, 1 temuan KRITIS).

## Sudah diperbaiki lewat patch ini

1. **TrustProxies** (`app/Http/Middleware/TrustProxies.php`)
   Aplikasi di belakang Cloudflare tapi proxy belum dipercaya (`$proxies`
   masih `null`). Akibatnya `Request::secure()` selalu `false`, sehingga
   cookie sesi tidak pernah mendapat atribut `Secure` meski
   `SESSION_SECURE_COOKIE=true`. Ini kemungkinan besar penyebab temuan
   *"Cookies without Secure attribute"* dan mendukung HSTS bekerja benar.

2. **Security headers** (`app/Http/Middleware/SecurityHeaders.php`)
   Menambahkan `Strict-Transport-Security`, `X-Content-Type-Options`,
   `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` di setiap
   response. Menutup temuan *"HSTS not offered"* dan beberapa
   *"HTTP Missing Security Headers"*.

3. **`.htaccess` (public/ dan root)**
   Blokir akses ke semua file/folder berawalan titik (`.git`, `.env`,
   `.config`, dst) sebagai lapisan pertahanan tambahan.

4. **Pin versi Chart.js** (`admin/analitik/index.blade.php`,
   `admin/siklus/index.blade.php`)
   Sebelumnya memuat `chart.js` tanpa versi (selalu "latest"), diganti
   jadi versi tetap `@4.5.1`. Mengurangi risiko dari temuan
   *"Missing Subresource Integrity"* — CDN unpinned bisa berubah isi
   kapan saja tanpa sepengetahuan kita.

## Sengaja BELUM diperbaiki lewat patch ini (butuh keputusan kamu)

- **SRI hash pada Chart.js/Google Fonts/Tailwind CDN** — belum
  ditambahkan. cdnjs.com dan jsdelivr.net menyediakan hash SRI resmi
  di halaman mereka, tapi hash itu harus disalin PERSIS dari sumber
  resmi (bukan ditebak/dihitung ulang) karena satu byte salah bikin
  browser blokir seluruh script. Font Awesome di `admin/layout.blade.php`
  SUDAH punya SRI (dari commit sebelumnya) sebagai contoh bentuknya.
  Google Fonts CSS tidak bisa dikasih SRI sama sekali karena isinya
  beda-beda tergantung browser pengunjung — ini pengecualian yang
  wajar dan diterima secara industri, bukan celah yang perlu ditutup.

- **Tailwind CDN di production** (`admin/layout.blade.php`,
  `layouts/app.blade.php`, `auth/login.blade.php`) — DevTools browser
  sendiri sudah warning: *"cdn.tailwindcss.com should not be used in
  production"*. **Keputusan: TIDAK dimigrasikan ke build asli.**

  Alasan: temuan ini levelnya INFORMASI (paling rendah), bukan celah
  keamanan yang bisa dieksploitasi — cuma warning performa/best-practice
  dari Tailwind sendiri. Setelah dicek, admin panel dan halaman publik
  ternyata pakai DUA tema Tailwind yang bentrok kalau digabung jadi satu
  `tailwind.config.js` build (font-sans, border-radius default, dan
  warna custom-nya beda total antara admin vs halaman publik). CDN bisa
  begini karena tiap halaman load config sendiri-sendiri secara
  independen; build asli cuma bisa satu config global.

  Menutup temuan level informasi ini butuh migrasi arsitektur (pisah
  token/build per tema) yang menyentuh puluhan file blade tanpa bisa
  dites visual dari sini — risiko regresi tampilan tidak sepadan
  dengan manfaat keamanannya. Kalau nanti mau dikerjakan, sebaiknya
  dikerjakan bertahap dengan testing manual di tiap halaman, bukan
  lewat patch otomatis.

## Wajib dicek manual di server (di luar kode aplikasi)

Temuan **KRITIS** — `Exposed .git repository` (`.git/HEAD` terdeteksi,
walau saat ini mengembalikan 403) — statusnya sudah *forbidden*, tapi
`.git` semestinya tidak pernah ada di dalam document root sama sekali.
Ini hampir pasti berarti document root web server saat ini mengarah ke
**root proyek**, bukan ke folder `public/` seperti standar Laravel.
Patch di repo ini tidak bisa mengubah konfigurasi server, jadi tolong
pastikan manual:

- **Document root harus diarahkan ke `public/`**, bukan ke root proyek.
- Jika pakai **Nginx** (bukan Apache, karena `.htaccess` tidak berlaku),
  tambahkan di server block:
  ```nginx
  root /path/ke/project/public;

  location ~ /\.(?!well-known) {
      deny all;
  }
  ```
- Set `SESSION_SECURE_COOKIE=true` di `.env` production (lihat
  `.env.example`).
- Set `APP_DEBUG=false` di production.

Temuan SEDANG/RENDAH terkait TLS (TLS 1.0/1.1 masih ditawarkan, cipher
lemah, grade B) dikontrol oleh Cloudflare (SSL/TLS → Edge Certificates),
bukan oleh kode Laravel — perlu diaktifkan **"Minimum TLS Version: 1.2"**
dan nonaktifkan cipher lama dari dashboard Cloudflare.
