<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mobile\User;
use App\Models\Mobile\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    /**
     * Generate OTP 6 digit
     */
    private function generateOtp()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate token unik untuk verifikasi
     */
    private function generateVerificationToken()
    {
        return Str::random(60);
    }

    /**
     * Generate token manual untuk login
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
     * Register user dari mobile (dengan OTP)
     * POST /api/mobile/register
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

            // Generate OTP dan token verifikasi
            $otp = $this->generateOtp();
            $verificationToken = $this->generateVerificationToken();

            // Simpan user dengan status belum terverifikasi
            $user = User::create([
                'id_user' => $nextId,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'Menunggu Verifikasi',
                'email_verified' => false,
                'verification_token' => $verificationToken,
                'created_at' => now(),
                'nama_lengkap' => $request->name,
            ]);

            // Simpan OTP ke collection email_verifications
            EmailVerification::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => $otp,
                    'token' => $verificationToken,
                    'expires_at' => now()->addMinutes(15),
                ]
            );

            // Log OTP untuk development (hapus di production)
            Log::info('OTP untuk ' . $request->email . ': ' . $otp);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil. Silakan verifikasi email Anda.',
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'name' => $user->name,
                        'email' => $user->email,
                        'status' => $user->status,
                    ],
                    'verification_token' => $verificationToken,
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
     * Resend OTP
     * POST /api/mobile/resend-otp
     */
    public function resendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak terdaftar'
                ], 404);
            }

            if ($user->email_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terverifikasi'
                ], 400);
            }

            // Generate OTP baru
            $otp = $this->generateOtp();
            $verificationToken = $this->generateVerificationToken();

            // Update OTP di database
            EmailVerification::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => $otp,
                    'token' => $verificationToken,
                    'expires_at' => now()->addMinutes(15),
                ]
            );

            // Update user verification token
            $user->verification_token = $verificationToken;
            $user->save();

            // Log OTP untuk development
            Log::info('Resend OTP untuk ' . $request->email . ': ' . $otp);

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ulang',
                'data' => [
                    'verification_token' => $verificationToken,
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Resend OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang OTP'
            ], 500);
        }
    }

    /**
     * Verify OTP
     * POST /api/mobile/verify-otp
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string|size:6',
            ]);

            $verification = EmailVerification::where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak valid'
                ], 400);
            }

            // Cek expired (15 menit)
            if (now()->isAfter($verification->expires_at)) {
                $verification->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP sudah kadaluarsa'
                ], 400);
            }

            // Update user menjadi verified
            $user = User::where('email', $request->email)->first();
            $user->email_verified = true;
            $user->status = 'Aktif';
            $user->save();

            // Generate token untuk login (manual)
            $token = $this->generateToken($user);

            // Hapus verification data
            $verification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi',
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
            Log::error('Verify OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal verifikasi OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user dari mobile
     * POST /api/mobile/login
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

            // Cek apakah email sudah diverifikasi
            if (!$user->email_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email belum diverifikasi. Silakan cek email Anda untuk kode OTP.'
                ], 403);
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
     * POST /api/mobile/logout
     */
    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

 /**
 * Get profile user
 * GET /api/mobile/user/profile
 */
public function profile(Request $request)
{
    try {
        // User sudah di-attach oleh middleware
        $user = $request->user;
        
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
                    'email_verified' => $user->email_verified,
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
        Log::error('Get profile error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data profile'
        ], 500);
    }
}

/**
 * Update profile user
 * PUT /api/mobile/user/profile
 */
public function updateProfile(Request $request)
{
    try {
        $user = $request->user;
        
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
        
        // Hitung BMI
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
}}