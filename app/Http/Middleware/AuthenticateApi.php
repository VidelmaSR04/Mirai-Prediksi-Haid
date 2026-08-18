<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Mobile\User;
use App\Support\ApiTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthenticateApi
{
    /**
     * Verify token HMAC-signed. Tidak lagi log token mentah (token adalah
     * kredensial rahasia, jangan ditulis ke storage/logs/laravel.log).
     */
    private function verifyToken(string $token): ?int
    {
        $decoded = ApiTokenService::verify($token);

        if (!$decoded) {
            Log::warning('Rejected API token: invalid, expired, or bad signature');
            return null;
        }

        return $decoded['id_user'];
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
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
        
        // Cari user dengan id_user (bukan _id!)
        $user = User::where('id_user', $userId)->first();
        
        if (!$user) {
            Log::error('User not found with id_user: ' . $userId);
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 401);
        }
        
        // Attach user ke request
        $request->merge(['user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        
        return $next($request);
    }
}