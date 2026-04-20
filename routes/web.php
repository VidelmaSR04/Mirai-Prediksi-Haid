<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
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

    // Login (tanpa auth)
    Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    // Semua route admin butuh login
    Route::middleware('admin')->group(function () {

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // ── Halaman Utama ──────────────────────────────
       Route::get('/dashboard', fn() => view('admin.dashboard.index'))->name('dashboard');
        Route::get('/siklus',    fn() => view('admin.siklus.index'))->name('siklus');
        Route::get('/prediksi',  fn() => view('admin.prediksi.index'))->name('prediksi');
        Route::get('/analitik',  fn() => view('admin.analitik.index'))->name('analitik');


        // ── Register Admin Baru (hanya bisa dibuat oleh admin) ──
        Route::get('/register',  [AdminAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AdminAuthController::class, 'register'])->name('register.submit');

        // ── Data Pengguna ──────────────────────────────
        Route::prefix('pengguna')->name('pengguna')->group(function () {
            Route::get('/',              [UserController::class, 'index'])->name('');
            Route::get('/export',        [UserController::class, 'exportCsv'])->name('.export');
            Route::get('/{id}',          [UserController::class, 'show'])->name('.show');
            Route::patch('/{id}/status', [UserController::class, 'updateStatus'])->name('.status');
        });

        // ── Laporan ────────────────────────────────────
        Route::prefix('laporan')->name('laporan')->group(function () {
            Route::get('/',          [LaporanController::class, 'index'])->name('');
            Route::post('/generate', [LaporanController::class, 'generate'])->name('.generate');
            Route::delete('/{id}',   [LaporanController::class, 'destroy'])->name('.destroy');
        });

        // ── Pengaturan ─────────────────────────────────
        Route::prefix('pengaturan')->name('pengaturan')->group(function () {
            Route::get('/',           [PengaturanController::class, 'index'])->name('');
            Route::patch('/profil',   [PengaturanController::class, 'update'])->name('.update');
            Route::patch('/password', [PengaturanController::class, 'updatePassword'])->name('.password');
        });
    });
});

// =====================================================
// USER PROFILE (Breeze - untuk mobile app user)
// =====================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
