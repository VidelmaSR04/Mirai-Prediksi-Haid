<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// =====================================================
// ROUTE PUBLIK (Tanpa Auth)
// =====================================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// =====================================================
// API ROUTES (JIKA DIPERLUKAN)
// =====================================================

/**
 * Route untuk mendapatkan data team (JSON)
 * Bisa digunakan untuk load data team secara dinamis
 */
Route::get('/api/team', function () {
    $team = [
        [
            'name' => 'Videlma',
            'nim' => 'E3124',
            'role' => 'UI/UX Designer & Machine Learning',
            'description' => 'Bertanggung jawab merancang tampilan antarmuka yang ramah pengguna serta berkontribusi dalam pengembangan model Machine Learning pada Mirai.',
            'image' => asset('img/videng.jpeg')
        ],
        [
            'name' => 'Adinda Riski',
            'nim' => 'E3124',
            'role' => 'Front End Web & Mobile',
            'description' => 'Mengembangkan tampilan web dan aplikasi mobile agar responsif, interaktif, dan sesuai dengan desain yang telah ditentukan.',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDzeuiOIThMu0CoZsdziQav8HowQIYr0QXO_cECCfRPRqg0eAjPs8Ip-b82QVT-swi955KZ6C2xN3D44k04kTO4Yftnlv5HE9TRbxT-vXoxfKUkKYHubj2tymye2NS8lXZNoMr9G3mxTM-F7FS1Mp9_QgKAgzC3L4c2LbXCq9m1eEvoU7HIyzgsCbNNfKzGYfKC-6--NRkqa5Zyy5sfhfAJr215u6lVweneKsa5Du7T4gxuZWuGTTfH9oR6asIMw16G_Datnkj_pY1i'
        ],
        [
            'name' => 'Risky Triana',
            'nim' => 'E3124',
            'role' => 'Back End & Machine Learning',
            'description' => 'Mengelola logika server, basis data, serta mendukung implementasi Machine Learning untuk memastikan sistem berjalan optimal.',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBvwX6onLylow1un8iQOmt3XuDryL9mChfJAg59BWzlvf4fR7OdC4K6IqEdllnqrhKzcYDRsTjouu0u32d-4-YTukv1i883ffmhoKi0vUvaLersC6qIyuW-ugN26iDnJrFVLospo2P7a9u7zQE3MCBaAkjT27pTgICHK5l2eqEAYwOeVDTVz9_8EGYIN8rTZqxVub-KgCnuUV46AxuFVxK84q6gwivIRlTNCTTg-Rdi6NQWtveJpPUusi-zSoTnBCTLyLN-sJfDRROC'
        ],
        [
            'name' => 'Revina',
            'nim' => 'E3124',
            'role' => 'Asisten Front End',
            'description' => 'Mendukung pengembangan Front End dengan membantu implementasi komponen UI dan pengujian tampilan aplikasi.',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAOP-z3tM9hvB-BNjBaQLb644NRYvsbtYsAKJ1JNskjVTGcdjB5DPVCmYVHTfe4aa3mr3ec_OnVtBtzUWW3d5YjM6IH5qbfSZf53LEYQH8k9bEM3fdKZo9SyDXdzlkhLebZU_UMjEJjVG9Ra5JrfbvtLtK-ozK9UpTn83N_K8Z-pxhoCdmpjITiUrgzw33RW0H4LpManXifT2_zKX5xEF3H0w6xoPrJE45SJDz_lKDLcbaE2hFYE5D_kXloKT3t53YYSZqB4LiEj-mt'
        ],
    ];

    return response()->json($team);
})->name('api.team');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/pengaturan', function () {
    return view('layouts.admin.pengaturan');
})->name('pengaturan');


Route::get('/test-mongo', function () {
    \App\Models\Cycle::create([
        'user_id' => 1,
        'cycle_length' => 28,
        'prediction_result' => '2026-04-05'
    ]);

    return "Data berhasil masuk MongoDB!";
});

// =====================================================
// DOWNLOAD ROUTES (UNTUK TOMBOL DOWNLOAD)
// =====================================================

/**
 * Route untuk redirect ke App Store
 */

Route::get('/download/ios', function () {
    return redirect('https://apps.apple.com/');
})->name('download.ios');

Route::get('/download/android', function () {
    return redirect('https://play.google.com/store');
})->name('download.android');



// =====================================================
// ROUTE ADMIN (Harus Login)
// =====================================================
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', function () {        // ← Pindah ke sini, hapus ['auth','verified']
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');     // ← Pindah ke sini
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/pengaturan', function () {       // ← Pindah ke sini, sebelumnya tidak terlindungi
        return view('layouts.admin.pengaturan');
    })->name('pengaturan');

    Route::get('/test-mongo', function () {       // ← Pindah ke sini, sebelumnya tidak terlindungi
        \App\Models\Cycle::create([
            'user_id' => 1,
            'cycle_length' => 28,
            'prediction_result' => '2026-04-05'
        ]);
        return "Data berhasil masuk MongoDB!";
    });

    // ← Tambah di sini, hanya admin login yang bisa buat akun admin baru
    Route::get('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
                ->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
});

require __DIR__ . '/auth.php';


