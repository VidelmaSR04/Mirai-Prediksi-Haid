<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mobile\Cycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CycleMobileController extends Controller
{
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
 * GET /api/mobile/cycles
 */
public function index(Request $request)
{
    try {
        $user = $request->user;
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $cycles = Cycle::where('user_id', $user->id_user)
            ->orderBy('last_period_date', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $cycles
        ]);
        
    } catch (\Exception $e) {
        Log::error('Get cycles error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data siklus'
        ], 500);
    }
}

    /**
     * GET /api/mobile/cycle/latest
     * Ambil siklus terakhir
     */
    public function latest(Request $request)
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
            
            $cycle = Cycle::where('user_id', $userId)
                ->orderBy('last_period_date', 'desc')
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => $cycle
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get latest cycle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data siklus terakhir'
            ], 500);
        }
    }

    /**
     * POST /api/mobile/cycle
     * Simpan siklus baru
     */
    public function store(Request $request)
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
            
            $request->validate([
                'last_period_date' => 'required|date',
                'cycle_length_days' => 'required|integer|min:20|max:45',
                'pain_level' => 'required|integer|min:0|max:10',
                'stress_score_cycle' => 'required|integer|min:0|max:10',
                'sleep_hours_cycle' => 'required|numeric|min:0|max:24',
                'mood_score' => 'nullable|integer|min:1|max:10',
            ]);
            
            // Generate id_cycle incremental
            $lastCycle = Cycle::orderBy('id_cycle', 'desc')->first();
            $nextId = $lastCycle ? $lastCycle->id_cycle + 1 : 1;
            
            $cycle = Cycle::create([
                'id_cycle' => $nextId,
                'user_id' => $userId,
                'last_period_date' => $request->last_period_date,
                'cycle_length_days' => $request->cycle_length_days,
                'pain_level' => $request->pain_level,
                'stress_score_cycle' => $request->stress_score_cycle,
                'sleep_hours_cycle' => $request->sleep_hours_cycle,
                'mood_score' => $request->mood_score ?? null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data siklus berhasil disimpan',
                'data' => $cycle
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Store cycle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data siklus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/mobile/cycle/{id}
     * Update siklus
     */
 /**
 * PUT /api/mobile/cycle/{id}
 * Update siklus
 */
public function update(Request $request, $id)
{
    try {
        $user = $request->user;
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $cycle = Cycle::where('user_id', $user->id_user)
            ->where('_id', $id)
            ->first();
        
        if (!$cycle) {
            return response()->json([
                'success' => false,
                'message' => 'Data siklus tidak ditemukan'
            ], 404);
        }
        
        $validated = $request->validate([
            'last_period_date' => 'sometimes|date',
            'cycle_length_days' => 'sometimes|integer|min:20|max:45',
            'pain_level' => 'sometimes|integer|min:0|max:10',
            'stress_score_cycle' => 'sometimes|integer|min:0|max:10',
            'sleep_hours_cycle' => 'sometimes|numeric|min:0|max:24',
            'mood_score' => 'nullable|integer|min:1|max:10',
        ]);
        
        $cycle->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Data siklus berhasil diupdate',
            'data' => $cycle
        ]);
        
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Update cycle error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate data siklus'
        ], 500);
    }
}

    /**
     * DELETE /api/mobile/cycle/{id}
     * Hapus siklus
     */
    public function destroy(Request $request, $id)
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
            
            $cycle = Cycle::where('user_id', $userId)
                ->where('id_cycle', $id)
                ->first();
            
            if (!$cycle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siklus tidak ditemukan'
                ], 404);
            }
            
            $cycle->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Data siklus berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete cycle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data siklus'
            ], 500);
        }
    }
}