<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Mobile\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthenticateApi
{
    /**
     * Verify token manual
     */
    private function verifyToken(string $token): ?int
    {
        try {
            // Debug: log token yang diterima
            Log::info('Token received: ' . $token);
            
            $decoded = json_decode(base64_decode($token), true);
            
            // Debug: log hasil decode
            Log::info('Decoded token: ', $decoded);
            
            if (!$decoded) {
                Log::error('Failed to decode token');
                return null;
            }
            
            if (!isset($decoded['exp'])) {
                Log::error('Token has no exp field');
                return null;
            }
            
            $currentTime = time();
            Log::info('Current time: ' . $currentTime . ', Exp time: ' . $decoded['exp']);
            
            if ($decoded['exp'] < $currentTime) {
                Log::error('Token expired');
                return null;
            }
            
            if (!isset($decoded['id_user'])) {
                Log::error('Token has no id_user field');
                return null;
            }
            
            return $decoded['id_user'];
            
        } catch (\Exception $e) {
            Log::error('Token verification error: ' . $e->getMessage());
            return null;
        }
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