<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * Send OTP to user email
     * POST /api/forgot-password
     */
    public function sendOtp(Request $request)
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

            // Generate 6 digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = Str::random(60);

            // Save OTP to database
            PasswordReset::where('email', $request->email)->delete();

            PasswordReset::create([
                'email' => $request->email,
                'token' => $token,
                'otp' => $otp,
                'created_at' => now()
            ]);

            // Log OTP untuk development
            Log::info('OTP untuk reset password ' . $request->email . ': ' . $otp);

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda',
                'data' => [
                    'token' => $token,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Send OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim kode OTP'
            ], 500);
        }
    }

    /**
     * Verify OTP
     * POST /api/verify-otp
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string|size:6'
            ]);

            $reset = PasswordReset::where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$reset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak valid'
                ], 400);
            }

            // Check if OTP expired (15 minutes)
            $createdAt = $reset->created_at;
            if ($createdAt && now()->diffInMinutes($createdAt) > 15) {
                $reset->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP sudah kadaluarsa'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP valid',
                'data' => [
                    'reset_token' => $reset->token
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Verify OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal verifikasi OTP'
            ], 500);
        }
    }

    /**
     * Reset password
     * POST /api/reset-password
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'reset_token' => 'required|string',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|same:password'
            ]);

            $reset = PasswordReset::where('email', $request->email)
                ->where('token', $request->reset_token)
                ->first();

            if (!$reset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token reset tidak valid'
                ], 400);
            }

            // Check if token expired (15 minutes)
            $createdAt = $reset->created_at;
            if ($createdAt && now()->diffInMinutes($createdAt) > 15) {
                $reset->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Token reset sudah kadaluarsa'
                ], 400);
            }

            // Update password
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete reset record
            $reset->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ]);

        } catch (\Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password: ' . $e->getMessage()
            ], 500);
        }
    }
}
