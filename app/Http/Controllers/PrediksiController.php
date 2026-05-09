<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;
use App\Models\Prediction;
use App\Models\Cycle;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    // ============================================
    // METHOD UNTUK WEB ADMIN (YANG SUDAH ADA)
    // ============================================
    
    public function index()
    {
        // ... KODE ANDA YANG SUDAH ADA, TIDAK PERUBAH ...
        // (saya tidak tulis ulang biar tidak panjang)
    }

    // ============================================
    // METHOD BARU UNTUK API (DIPANGGIL FLUTTER)
    // ============================================

    /**
     * POST /api/predictions
     * Menerima data dari Flutter, memanggil Python AI, mengembalikan hasil prediksi
     */
    public function predict(Request $request)
    {
        try {
            // 1. Validasi input dari Flutter
            $validated = $request->validate([
                'last_cycle_start_date' => 'required|date',
                'previous_cycle_start_date' => 'nullable|date',
                'pain_level' => 'required|integer|min:0|max:10',
                'stress_score_cycle' => 'required|integer|min:0|max:10',
                'sleep_hours_cycle' => 'required|numeric|min:0|max:24',
                'mood_score' => 'nullable|integer|min:1|max:10',
                'age' => 'nullable|integer|min:12|max:60',
                'weight_kg' => 'nullable|numeric|min:30|max:200',
                'height_cm' => 'nullable|numeric|min:100|max:250',
                'pcos_diagnosed' => 'nullable|boolean',
                'birth_control_use' => 'nullable|boolean',
            ]);

            Log::info('Prediksi request dari Flutter', $validated);

            // 2. HITUNG prev_cycle_length (BUKAN INPUT USER!)
            $lastDate = Carbon::parse($validated['last_cycle_start_date']);
            $prevCycleLength = 28; // default

            if (!empty($validated['previous_cycle_start_date'])) {
                $prevDate = Carbon::parse($validated['previous_cycle_start_date']);
                $prevCycleLength = $lastDate->diffInDays($prevDate);
                
                if ($prevCycleLength < 20 || $prevCycleLength > 45) {
                    $prevCycleLength = 28;
                }
            }

            // 3. Hitung BMI jika ada data berat & tinggi
            $bmi = 22.0;
            if (!empty($validated['weight_kg']) && !empty($validated['height_cm'])) {
                $heightM = $validated['height_cm'] / 100;
                $bmi = round($validated['weight_kg'] / ($heightM * $heightM), 1);
            }

            // 4. Siapkan payload ke Python Service
            $pythonUrl = env('PYTHON_SERVICE_URL', 'http://localhost:8001');
            
            $payload = [
                'start_date' => $validated['last_cycle_start_date'],
                'user_data' => [
                    'prev_cycle_length' => (float) $prevCycleLength,
                    'pain_level' => (float) $validated['pain_level'],
                    'stress_score_cycle' => (float) $validated['stress_score_cycle'],
                    'sleep_hours_cycle' => (float) $validated['sleep_hours_cycle'],
                    'mood_score' => (float) ($validated['mood_score'] ?? 7),
                    'age' => (float) ($validated['age'] ?? 25),
                    'bmi' => (float) $bmi,
                    'pcos_diagnosed' => (int) ($validated['pcos_diagnosed'] ?? 0),
                    'birth_control_use' => (int) ($validated['birth_control_use'] ?? 0),
                ]
            ];

            Log::info('Mengirim ke Python service', ['url' => $pythonUrl, 'payload' => $payload]);

            // 5. Panggil Python Service
            $response = Http::timeout(30)->post($pythonUrl . '/api/predict', $payload);

            if (!$response->successful()) {
                Log::error('Python service error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Service AI sedang sibuk, coba lagi nanti'
                ], 503);
            }

            $result = $response->json();

            // 6. Simpan ke database (collection: predictions)
            $db = $this->getDb();
            $userId = auth()->id() ?? $request->input('user_id'); // fallback
            
            $predictionDoc = [
                'id_user' => $userId,
                'last_cycle_start_date' => $validated['last_cycle_start_date'],
                'previous_cycle_start_date' => $validated['previous_cycle_start_date'] ?? null,
                'predicted_cycle_length' => $result['predicted_cycle_length'],
                'predicted_next_period' => $result['next_period_date'],
                'error_margin' => $result['error_margin'],
                'confidence_score' => $result['confidence_level'] == 'Tinggi' ? 85 : ($result['confidence_level'] == 'Sedang' ? 70 : 50),
                'mae_error' => $result['error_margin'],
                'input_data' => $payload['user_data'],
                'created_at' => now()->toISOString(),
            ];
            
            $db->selectCollection('predictions')->insertOne($predictionDoc);

            // 7. Juga simpan ke collection cycles
            $cycleDoc = [
                'id_user' => $userId,
                'last_period_date' => $validated['last_cycle_start_date'],
                'cycle_length_days' => $result['predicted_cycle_length'],
                'pain_level' => $validated['pain_level'],
                'stress_score_cycle' => $validated['stress_score_cycle'],
                'sleep_hours_cycle' => $validated['sleep_hours_cycle'],
                'mood_score' => $validated['mood_score'] ?? null,
                'created_at' => now()->toISOString(),
            ];
            
            $db->selectCollection('cycles')->insertOne($cycleDoc);

            // 8. Kembalikan hasil ke Flutter
            return response()->json([
                'success' => true,
                'data' => [
                    'predicted_cycle_length' => $result['predicted_cycle_length'],
                    'next_period_date' => $result['next_period_date'],
                    'error_margin' => $result['error_margin'],
                    'confidence_level' => $result['confidence_level'],
                    'message' => $result['message'],
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Prediksi error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/predictions (history)
     * Mengambil riwayat prediksi user untuk ditampilkan di Flutter
     */
    public function history(Request $request)
    {
        try {
            $db = $this->getDb();
            $userId = auth()->id() ?? $request->input('user_id');
            
            $predictions = $db->selectCollection('predictions')
                ->find(['id_user' => $userId], ['sort' => ['created_at' => -1], 'limit' => 10]);
            
            $result = [];
            foreach ($predictions as $pred) {
                $pred = (array) $pred;
                $result[] = [
                    'id' => (string) $pred['_id'],
                    'predicted_cycle_length' => $pred['predicted_cycle_length'] ?? null,
                    'predicted_next_period' => $pred['predicted_next_period'] ?? null,
                    'error_margin' => $pred['error_margin'] ?? null,
                    'confidence_score' => $pred['confidence_score'] ?? null,
                    'created_at' => $pred['created_at'] ?? null,
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/predictions/health
     * Cek status Python AI service
     */
    public function health()
    {
        try {
            $pythonUrl = env('PYTHON_SERVICE_URL', 'http://localhost:8001');
            $response = Http::timeout(5)->get($pythonUrl . '/api/health');
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            Log::warning('Python service health check failed: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'unavailable',
            'message' => 'AI prediction service is not responding'
        ], 503);
    }
}