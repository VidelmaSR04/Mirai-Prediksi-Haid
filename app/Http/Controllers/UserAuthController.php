<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    /**
     * Generate token manual sederhana
     */
    private function generateToken($user)
    {
        $payload = [
            'id_user' => $user->id_user,
            'email' => $user->email,
            'exp' => time() + (86400 * 7), // 7 hari
        ];
        
        return base64_encode(json_encode($payload));
    }

    /**
     * Verify token manual
     */
    private function verifyToken($token)
    {
        try {
            $decoded = json_decode(base64_decode($token), true);
            
            if (!$decoded || !isset($decoded['exp']) || $decoded['exp'] < time()) {
                return null;
            }
            
            return $decoded['id_user'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Register user dari mobile
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            // Generate id_user incremental
            $lastUser = User::orderBy('id_user', 'desc')->first();
            $nextId = $lastUser ? $lastUser->id_user + 1 : 1;

            $user = User::create([
                'id_user' => $nextId,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'Aktif',
                'created_at' => now(),
                'nama_lengkap' => $request->name,
            ]);

            // Generate token manual (tanpa Sanctum)
            $token = $this->generateToken($user);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'name' => $user->name,
                        'email' => $user->email,
                        'status' => $user->status,
                    ],
                    'token' => $token
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('User registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user dari mobile
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak terdaftar'
                ], 401);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah'
                ], 401);
            }

            // Cek status akun
            if ($user->status !== 'Aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda tidak aktif. Silakan hubungi admin.'
                ], 403);
            }

            // Generate token manual
            $token = $this->generateToken($user);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'name' => $user->name,
                        'email' => $user->email,
                        'status' => $user->status,
                        'nama_lengkap' => $user->nama_lengkap,
                        'no_telepon' => $user->no_telepon,
                        'age' => $user->age,
                    ],
                    'token' => $token
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('User login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Dengan token manual, logout cukup dari sisi client
        // Server hanya return success
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Get profile user
     */
    public function profile(Request $request)
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401);
            }
            
            $userId = $this->verifyToken($token);
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau sudah kadaluarsa'
                ], 401);
            }
            
            $user = User::where('id_user', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'name' => $user->name,
                        'email' => $user->email,
                        'status' => $user->status,
                        'nama_lengkap' => $user->nama_lengkap,
                        'no_telepon' => $user->no_telepon,
                        'age' => $user->age,
                        'weight_kg' => $user->weight_kg,
                        'height_cm' => $user->height_cm,
                        'bmi' => $user->bmi,
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data profile'
            ], 500);
        }
    }

    /**
     * Update profile user (untuk onboarding)
     */
    public function updateProfile(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $userId = $this->verifyToken($token);
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid'
                ], 401);
            }
            
            $user = User::where('id_user', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            $request->validate([
                'nama_lengkap' => 'nullable|string|max:255',
                'no_telepon' => 'nullable|string|max:15',
                'age' => 'nullable|integer|min:10|max:100',
                'weight_kg' => 'nullable|numeric|min:20|max:300',
                'height_cm' => 'nullable|numeric|min:50|max:250',
            ]);
            
            // Update field
            if ($request->has('nama_lengkap')) {
                $user->nama_lengkap = $request->nama_lengkap;
            }
            if ($request->has('no_telepon')) {
                $user->no_telepon = $request->no_telepon;
            }
            if ($request->has('age')) {
                $user->age = $request->age;
            }
            if ($request->has('weight_kg')) {
                $user->weight_kg = $request->weight_kg;
            }
            if ($request->has('height_cm')) {
                $user->height_cm = $request->height_cm;
            }
            
            // Hitung BMI jika weight dan height tersedia
            if ($user->weight_kg && $user->height_cm) {
                $heightM = $user->height_cm / 100;
                $user->bmi = round($user->weight_kg / ($heightM * $heightM), 1);
            }
            
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diupdate',
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'nama_lengkap' => $user->nama_lengkap,
                        'no_telepon' => $user->no_telepon,
                        'age' => $user->age,
                        'weight_kg' => $user->weight_kg,
                        'height_cm' => $user->height_cm,
                        'bmi' => $user->bmi,
                    ]
                ]
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal update profile: ' . $e->getMessage()
            ], 500);
        }
    }
}