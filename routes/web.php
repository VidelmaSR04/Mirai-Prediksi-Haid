<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataadminController;
use App\Http\Controllers\SiklusController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\AnalitikController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

// =====================================================
// ROUTE PUBLIK (Landing Page)
// =====================================================
Route::get('/', fn() => view('welcome'))->name('home');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/privacy', fn() => view('privacy'))->name('privacy');
Route::get('/terms', fn() => view('terms'))->name('terms');
Route::get('/api/team', fn() => response()->json([]))->name('api.team');
Route::get('/download/ios', fn() => redirect('https://apps.apple.com/'))->name('download.ios');
Route::get('/download/android', fn() => redirect('https://play.google.com/store'))->name('download.android');

// =====================================================
// ADMIN ROUTES
// =====================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // Login
    // throttle:5,1 -> maksimal 5 percobaan per menit per kombinasi IP+field yang dicoba,
    // mencegah brute-force password admin.
    Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.submit');

    Route::middleware('admin')->group(function () {

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // ── Halaman Utama ──────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifikasi/data', [NotifikasiController::class, 'getData'])->name('notifikasi.data');
        Route::get('/siklus', [SiklusController::class, 'index'])->name('siklus');
        Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi');
        Route::get('/analitik', [AnalitikController::class, 'index'])->name('analitik');

        // Register Admin
        Route::get('/register',  [AdminAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AdminAuthController::class, 'register'])->name('register.submit');

        // Data Pengguna
        Route::prefix('pengguna')->name('pengguna.')->group(function () {
            Route::get('/',              [UserController::class, 'index'])->name('index');
            Route::get('/export',        [UserController::class, 'exportCsv'])->name('export');
            Route::get('/{id}',          [UserController::class, 'show'])->name('show');
            Route::patch('/{id}/status', [UserController::class, 'updateStatus'])->name('status');
        });

        // Data Admin
        Route::prefix('data-admin')->name('data-admin.')->group(function () {
            Route::get('/',     [DataadminController::class, 'index'])->name('index');
            Route::get('/{id}', [DataadminController::class, 'show'])->name('show');
        });

        // Laporan & Pengaturan
        Route::prefix('laporan')->name('laporan')->group(function () {
            Route::get('/',          [LaporanController::class, 'index'])->name('');
            Route::post('/generate', [LaporanController::class, 'generate'])->name('.generate');
            Route::delete('/{id}',   [LaporanController::class, 'destroy'])->name('.destroy');
        });

        Route::prefix('pengaturan')->name('pengaturan')->group(function () {
            Route::get('/',           [PengaturanController::class, 'index'])->name('');
            Route::patch('/profil',   [PengaturanController::class, 'update'])->name('.update');
            Route::patch('/password', [PengaturanController::class, 'updatePassword'])->name('.password');
        });
    });
});

// =====================================================
// CATATAN: Rute Breeze bawaan (/login, /register, /profile, guard 'web')
// SENGAJA DINONAKTIFKAN.
//
// Sistem ini cuma punya 2 jenis login: admin (web, guard 'admin') dan
// user mobile (API, token HMAC sendiri). Guard 'web' + App\Models\User
// bawaan Breeze tidak pernah dipakai untuk fitur nyata apa pun, jadi
// dibiarkan aktif cuma jadi jalur otentikasi kedua yang tidak terpantau
// dan berpotensi crash (App\Models\User tidak pernah diisi data asli).
//
// Kalau nanti butuh portal web untuk user biasa (bukan admin, bukan
// mobile), aktifkan lagi baris di bawah dan pastikan App\Models\User
// benar-benar terhubung ke data user yang sesuai.
// =====================================================
// Route::middleware('auth')->group(function () {
//     Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });
// require __DIR__ . '/auth.php';