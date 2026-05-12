<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Mobile\Prediction;
use App\Models\Mobile\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PredictionMobileController extends Controller
{
    protected string $pythonServiceUrl;

    public function __construct()
    {
        // Port 8001 untuk Python service
        $this->pythonServiceUrl = env('PYTHON_SERVICE_URL', 'http://localhost:8001');
    }

    /**
     * Verify token manual
     */
    private function verifyToken(string $token): ?int
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
     * POST /api/mobile/predict
     * Prediksi siklus dengan AI - User TIDAK input panjang siklus manual!
     */
public function predict(Request $request)
{
    try {
        $user = $request->user;
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $userId = $user->id_user;
            
            // ============================================
            // 1. VALIDASI INPUT DARI MOBILE
            // ============================================
            // User hanya input tanggal, TIDAK input panjang siklus!
            $validated = $request->validate([
                'tanggal_haid_terakhir' => 'required|date',           // WAJIB
                'tanggal_haid_bulan_sebelumnya' => 'nullable|date',   // OPSIONAL
                'pain_level' => 'required|integer|min:0|max:10',
                'stress_score_cycle' => 'required|integer|min:0|max:10',
                'sleep_hours_cycle' => 'required|numeric|min:0|max:24',
                'mood_score' => 'nullable|integer|min:1|max:10',
            ]);

            // ============================================
            // 2. HITUNG prev_cycle_length OTOMATIS (User TIDAK input!)
            // ============================================
            $lastDate = Carbon::parse($validated['tanggal_haid_terakhir']);
            $prevCycleLength = 28; // default jika tidak ada data sebelumnya

            if (!empty($validated['tanggal_haid_bulan_sebelumnya'])) {
                $prevDate = Carbon::parse($validated['tanggal_haid_bulan_sebelumnya']);
                $prevCycleLength = $lastDate->diffInDays($prevDate);
                
                // Validasi logis: siklus normal 20-45 hari
                if ($prevCycleLength < 20 || $prevCycleLength > 45) {
                    $prevCycleLength = 28; // fallback ke default
                    Log::warning('Invalid cycle length detected, using default', [
                        'calculated' => $prevCycleLength,
                        'user_id' => $userId
                    ]);
                }
            }

            Log::info('Calculated prev_cycle_length', [
                'user_id' => $userId,
                'tanggal_haid_terakhir' => $validated['tanggal_haid_terakhir'],
                'tanggal_haid_bulan_sebelumnya' => $validated['tanggal_haid_bulan_sebelumnya'] ?? null,
                'prev_cycle_length' => $prevCycleLength
            ]);

            // ============================================
            // 3. AMBIL DATA USER (usia, BMI)
            // ============================================
            $user = User::where('id_user', $userId)->first();
            $age = $user->age ?? 25;
            $bmi = $user->bmi ?? 22;

            // ============================================
            // 4. Siapkan payload untuk Python Service (PORT 8001)
            // ============================================
            $payload = [
                'start_date' => $validated['tanggal_haid_terakhir'],
                'user_data' => [
                    'prev_cycle_length' => (float) $prevCycleLength,  // ← HASIL HITUNGAN!
                    'pain_level' => (float) $validated['pain_level'],
                    'stress_score_cycle' => (float) $validated['stress_score_cycle'],
                    'sleep_hours_cycle' => (float) $validated['sleep_hours_cycle'],
                    'mood_score' => (float) ($validated['mood_score'] ?? 7),
                    'age' => (float) $age,
                    'bmi' => (float) $bmi,
                    'pcos_diagnosed' => 0,
                    'birth_control_use' => 0,
                ]
            ];

            Log::info('Sending to Python AI', [
                'url' => $this->pythonServiceUrl . '/api/predict',
                'payload' => $payload
            ]);

            // ============================================
            // 5. PANGGIL PYTHON SERVICE (PORT 8001)
            // ============================================
            $response = Http::timeout(10)->post(
                $this->pythonServiceUrl . '/api/predict',
                $payload
            );

            if (!$response->successful()) {
                Log::error('Python service error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan AI sedang sibuk, coba lagi nanti'
                ], 503);
            }

            $aiResult = $response->json();

            // ============================================
            // 6. SIMPAN KE DATABASE
            // ============================================
            $lastPrediction = Prediction::orderBy('id_prediction', 'desc')->first();
            $nextId = $lastPrediction ? $lastPrediction->id_prediction + 1 : 1;
            
            $prediction = Prediction::create([
                'id_prediction' => $nextId,
                'user_id' => $userId,
                'last_cycle_start_date' => $validated['tanggal_haid_terakhir'],
                'previous_cycle_start_date' => $validated['tanggal_haid_bulan_sebelumnya'] ?? null,
                'predicted_cycle_length' => $aiResult['predicted_cycle_length'],
                'predicted_next_date' => $aiResult['next_period_date'],
                'error_margin' => $aiResult['error_margin'],
                'confidence_level' => $aiResult['confidence_level'],
                'input_data' => $payload['user_data'],
            ]);

            // ============================================
            // 7. KEMBALIKAN HASIL KE MOBILE
            // ============================================
            return response()->json([
                'success' => true,
                'data' => [
                    'prediction_id' => $prediction->id_prediction,
                    'tanggal_haid_terakhir' => $validated['tanggal_haid_terakhir'],
                    'predicted_cycle_length' => $aiResult['predicted_cycle_length'],
                    'next_period_date' => $aiResult['next_period_date'],
                    'error_margin' => $aiResult['error_margin'],
                    'confidence_level' => $aiResult['confidence_level'],
                    'message' => $aiResult['message'],
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Prediction error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/mobile/predictions/history
     * Riwayat prediksi user
     */
    public function history(Request $request)
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
                    'message' => 'Token tidak valid'
                ], 401);
            }
            
            $predictions = Prediction::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($pred) {
                    return [
                        'id_prediction' => $pred->id_prediction,
                        'tanggal_haid_terakhir' => $pred->last_cycle_start_date,
                        'tanggal_haid_bulan_sebelumnya' => $pred->previous_cycle_start_date,
                        'predicted_cycle_length' => $pred->predicted_cycle_length,
                        'predicted_next_date' => $pred->predicted_next_date,
                        'error_margin' => $pred->error_margin,
                        'created_at' => $pred->created_at,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $predictions
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get prediction history error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat prediksi'
            ], 500);
        }
    }

    /**
     * GET /api/mobile/predictions/{id}
     * Detail prediksi tertentu
     */
    public function show(Request $request, int $id)
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
                    'message' => 'Token tidak valid'
                ], 401);
            }
            
            $prediction = Prediction::where('user_id', $userId)
                ->where('id_prediction', $id)
                ->first();
            
            if (!$prediction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prediksi tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id_prediction' => $prediction->id_prediction,
                    'tanggal_haid_terakhir' => $prediction->last_cycle_start_date,
                    'tanggal_haid_bulan_sebelumnya' => $prediction->previous_cycle_start_date,
                    'predicted_cycle_length' => $prediction->predicted_cycle_length,
                    'predicted_next_date' => $prediction->predicted_next_date,
                    'error_margin' => $prediction->error_margin,
                    'confidence_level' => $prediction->confidence_level,
                    'created_at' => $prediction->created_at,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Show prediction error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail prediksi'
            ], 500);
        }
    }
}