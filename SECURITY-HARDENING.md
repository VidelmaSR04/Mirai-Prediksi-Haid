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
