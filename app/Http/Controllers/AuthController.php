<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Helpers\TokenHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:admins',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = TokenHelper::generateToken((string)$admin->id);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'user' => [
                    'id' => (string) $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                ],
                'token' => $token
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $admin = Admin::where('email', $request->email)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah'
                ], 401);
            }

            $token = TokenHelper::generateToken((string)$admin->id);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user' => [
                    'id' => (string) $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                ],
                'token' => $token
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function user(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ], 401);
        }

        $parts = explode('|', base64_decode($token));
        if (count($parts) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }

        $adminId = $parts[0];
        $admin = Admin::find($adminId);
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => (string) $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ]
        ]);
    }
}
