<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
 
// Test route
Route::get('/test', function() {
    return response()->json([
        'message' => 'API is working!',
        'status' => 'success',
        'database' => 'MongoDB'
    ]);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/test-mongodb', function() {
    try {
        // Test ping
        DB::connection('mongodb')->command(['ping' => 1]);
        
        // Test insert
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test' . time() . '@example.com',
            'password' => Hash::make('password123'),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'MongoDB connected successfully!',
            'test_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'MongoDB connection failed: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/test-token', function() {
    try {
        $user = User::first();
        $token = $user->createToken('test')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});