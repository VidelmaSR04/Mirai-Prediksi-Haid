<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiklusController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\CycleMobileController;
use App\Http\Controllers\Api\PredictionMobileController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\CorrectionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ========== ROUTE UNTUK USER MOBILE ==========

// Register & OTP Verification
Route::post('/mobile/register', [UserAuthController::class, 'register']);
Route::post('/mobile/verify-otp', [UserAuthController::class, 'verifyOtp']);
Route::post('/mobile/resend-otp', [UserAuthController::class, 'resendOtp']);

// Login
Route::post('/mobile/login', [UserAuthController::class, 'login']);

// ============================================
// PROTECTED ROUTES (pakai token)
// ============================================

Route::middleware(['auth.api'])->group(function () {
    
    // User Profile
    Route::post('/mobile/logout', [UserAuthController::class, 'logout']);
    Route::get('/mobile/user/profile', [UserAuthController::class, 'profile']);
    Route::put('/mobile/user/profile', [UserAuthController::class, 'updateProfile']);
    
    // Cycle (CRUD)
    Route::get('/mobile/cycles', [CycleMobileController::class, 'index']);
    Route::get('/mobile/cycle/latest', [CycleMobileController::class, 'latest']);
    Route::post('/mobile/cycle', [CycleMobileController::class, 'store']);
    Route::put('/mobile/cycle/{id}', [CycleMobileController::class, 'update']);
    Route::delete('/mobile/cycle/{id}', [CycleMobileController::class, 'destroy']);
    
    // Prediction (AI)
    Route::post('/mobile/predict', [PredictionMobileController::class, 'predict']);
    Route::get('/mobile/predictions/history', [PredictionMobileController::class, 'history']);
    Route::get('/mobile/predictions/{id}', [PredictionMobileController::class, 'show']);
});

  Route::middleware(['auth.api'])->group(function () {
    // ============================================
    // DAILY NOTE
    // ============================================
    Route::post('/mobile/note', [NoteController::class, 'store']);
    Route::get('/mobile/note/{date}', [NoteController::class, 'show']);
    Route::get('/mobile/notes/{year}/{month}', [NoteController::class, 'getByMonth']);
    Route::delete('/mobile/note/{id}', [NoteController::class, 'destroy']);
    
    // ============================================
    // CYCLE CORRECTION (Feedback untuk AI)
    // ============================================
    Route::post('/mobile/correction', [CorrectionController::class, 'store']);
    Route::get('/mobile/corrections', [CorrectionController::class, 'index']);
    Route::get('/mobile/corrections/stats', [CorrectionController::class, 'stats']);
});

// ========== ROUTE UNTUK FORGOT PASSWORD ==========
Route::post('/forgot-password', [App\Http\Controllers\ForgotPasswordController::class, 'sendOtp']);
Route::post('/verify-otp', [App\Http\Controllers\ForgotPasswordController::class, 'verifyOtp']);
Route::post('/reset-password', [App\Http\Controllers\ForgotPasswordController::class, 'resetPassword']);