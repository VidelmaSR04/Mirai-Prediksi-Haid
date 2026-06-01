<p align="center">
  <img src="docs/logo.png" alt="MIRAI Logo" width="180">
</p>

<h1 align="center">MIRAI ADMIN PANEL</h1>

<p align="center">
  Dashboard Administrasi Sistem Prediksi Siklus Menstruasi MIRAI
</p>

---

## 👨‍💻 Tim Pengembang

Proyek MIRAI dikembangkan oleh tim mahasiswa Program Studi Manajemen Informatika:

- **Videlma Sufi Romadhani**
- **Adinda Riski Maulida**
- **Rizky Triana Putri**
- **Revina Eka Maharani Enysky**

---


## 📖 Tentang Proyek

MIRAI Admin Panel merupakan aplikasi berbasis web yang digunakan untuk mengelola seluruh data pada sistem MIRAI. Dashboard ini membantu administrator dalam memantau aktivitas pengguna, mengelola data siklus menstruasi, mengawasi hasil prediksi, serta mengelola informasi yang tersedia dalam sistem.

Aplikasi ini dibangun menggunakan Laravel sebagai backend dan terintegrasi dengan layanan machine learning yang digunakan untuk membantu proses prediksi siklus menstruasi.

---

## 🎯 Tujuan Sistem

Sistem admin MIRAI dikembangkan untuk:

- Mengelola data pengguna aplikasi.
- Mengelola data riwayat siklus menstruasi.
- Memantau hasil prediksi yang dihasilkan sistem.
- Mengelola data chatbot dan konsultasi.
- Menyediakan dashboard monitoring sistem.
- Membantu proses pengambilan keputusan berdasarkan data yang tersedia.

---

## ✨ Fitur Admin

### 👥 Manajemen Pengguna
Mengelola data pengguna yang terdaftar pada aplikasi MIRAI.

### 📅 Manajemen Data Siklus
Melihat dan mengelola data riwayat siklus menstruasi yang tersimpan dalam sistem.

### 📊 Monitoring Prediksi
Menampilkan hasil prediksi siklus menstruasi yang dihasilkan sistem.

### 🤖 Monitoring Chatbot
Memantau aktivitas chatbot dan interaksi pengguna.

### 📈 Dashboard Statistik
Menyajikan ringkasan data dan statistik penggunaan aplikasi dalam bentuk dashboard.

### 🔐 Manajemen Hak Akses
Mengelola autentikasi dan akses administrator sistem.

---

## 🧠 Metode Prediksi

Sistem MIRAI menggunakan metode:

### Multiple Linear Regression (MLR)

Metode ini digunakan untuk membantu memperkirakan panjang siklus menstruasi berdasarkan berbagai faktor yang memengaruhi siklus pengguna.

Model dibangun menggunakan Python dan Scikit-Learn, kemudian diintegrasikan dengan sistem berbasis Laravel melalui layanan API.

---

## 💻 Teknologi yang Digunakan

### Backend
- Laravel 12
- PHP 8+

### Frontend
- Blade Template
- Tailwind CSS
- JavaScript

### Database
- MySQL

### Machine Learning Service
- Python
- Scikit-Learn
- Pandas
- NumPy
- FastAPI

---

## 📂 Struktur Modul Admin

```text
Dashboard
│
├── Data Pengguna
├── Data Siklus Menstruasi
├── Monitoring Prediksi
├── Monitoring Chatbot
├── Statistik Sistem
└── Pengaturan Admin
```

---

## 🚀 Instalasi

```bash
git clone https://github.com/username/mirai-admin.git

cd mirai-admin

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan serve
```

---

## 👨‍💻 Tim Pengembang

Proyek ini dikembangkan sebagai bagian dari penelitian dan pengembangan Sistem Prediksi Siklus Menstruasi MIRAI.

---

## 📄 Lisensi

Digunakan untuk tujuan penelitian, pembelajaran, dan pengembangan sistem informasi.
