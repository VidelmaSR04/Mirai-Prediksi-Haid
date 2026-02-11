<?php

/**
 * =====================================================
 * WEB ROUTES - MIRAI LANDING PAGE
 * =====================================================
 * 
 * File ini berisi routing untuk landing page Mirai
 * Letakkan file ini di: routes/web.php
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

// =====================================================
// HALAMAN UTAMA
// =====================================================

/**
 * Route untuk halaman utama/landing page
 * Menampilkan welcome.blade.php yang berisi semua sections
 */
Route::get('/', function () {
    return view('welcome');
})->name('home');


// =====================================================
// CONTACT FORM
// =====================================================

/**
 * Route untuk menangani submit form kontak
 * Method: POST
 * Controller: ContactController@submit
 */
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');


// =====================================================
// HALAMAN TAMBAHAN (OPSIONAL)
// =====================================================

/**
 * Route untuk halaman privacy policy
 * Buat view di: resources/views/privacy.blade.php
 */
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

/**
 * Route untuk halaman terms & conditions
 * Buat view di: resources/views/terms.blade.php
 */
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


// =====================================================
// DOWNLOAD ROUTES (UNTUK TOMBOL DOWNLOAD)
// =====================================================

/**
 * Route untuk redirect ke App Store
 */
Route::get('/download/ios', function () {
    // Ganti dengan link App Store yang sebenarnya
    return redirect('https://apps.apple.com/');
})->name('download.ios');

/**
 * Route untuk redirect ke Play Store
 */
Route::get('/download/android', function () {
    // Ganti dengan link Play Store yang sebenarnya
    return redirect('https://play.google.com/store');
})->name('download.android');