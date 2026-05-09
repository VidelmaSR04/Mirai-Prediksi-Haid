<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;
use App\Models\Cycle;

class SiklusController extends Controller
{
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    /** Semua cycles — 1 doc = 1 data (struktur baru) */
    private function getAllCycles(): array
    {
        $all = [];
        foreach ($this->getDb()->selectCollection('cycles')->find([]) as $doc) {
            $all[] = (array) $doc;
        }
        return $all;
    }

    /** Map id_user → nama_lengkap dari collection users */
    private function getUserMap(): array
    {
        $map = [];
        foreach ($this->getDb()->selectCollection('users')->find([]) as $doc) {
            $doc = (array) $doc;
            $map[$doc['id_user'] ?? 0] = $doc['nama_lengkap'] ?? '-';
        }
        return $map;
    }

    /**
     * Verify token manual
     */
    private function verifyToken($token)
    {
        try {
            Log::info('VerifyToken - Input token: ' . $token);
            
            $decoded = json_decode(base64_decode($token), true);
            
            Log::info('VerifyToken - Decoded payload: ', $decoded ?: ['null']);
            
            if (!$decoded || !isset($decoded['exp']) || $decoded['exp'] < time()) {
                Log::warning('VerifyToken - Token invalid or expired', [
                    'has_decoded' => !$decoded,
                    'has_exp' => isset($decoded['exp']),
                    'exp' => $decoded['exp'] ?? null,
                    'current_time' => time()
                ]);
                return null;
            }
            
            $userId = $decoded['id_user'] ?? null;
            Log::info('VerifyToken - User ID extracted: ' . ($userId ?? 'null'));
            
            return $userId;
        } catch (\Exception $e) {
            Log::error('Verify token error: ' . $e->getMessage());
            return null;
        }
    }

    // ========== METHOD UNTUK WEB ADMIN ==========
    public function index(Request $request)
    {
        try {
            $allCycles = $this->getAllCycles();
            $userMap   = $this->getUserMap();

            foreach ($allCycles as &$s) {
                $s['nama'] = $userMap[$s['id_user'] ?? 0] ?? 'User #' . ($s['id_user'] ?? '-');
                $s['panjang_siklus']       = $s['cycle_length_days']   ?? 0;
                $s['tanggal_mulai_haid']   = $s['tanggal_mulai_haid']  ?? '-';
                $s['tanggal_selesai_haid'] = $s['tanggal_selesai_haid'] ?? '-';
                $s['stress_score_cycle']   = $s['stress_score_cycle']  ?? '-';
                $s['sleep_hours_cycle']    = $s['sleep_hours_cycle']   ?? '-';
                $s['pain_level']           = $s['pain_level']          ?? '-';
            }
            unset($s);

            $search = trim($request->get('search', ''));
            if ($search) {
                $q         = strtolower($search);
                $allCycles = array_values(array_filter($allCycles,
                    fn($s) => str_contains(strtolower($s['nama'] ?? ''), $q)
                ));
            }

            $total    = count($allCycles);
            $cycleLen = array_filter(
                array_column($allCycles, 'cycle_length_days'),
                fn($v) => is_numeric($v)
            );
            $rataRata    = count($cycleLen) ? round(array_sum($cycleLen) / count($cycleLen), 1) : 0;
            $normalCount = count(array_filter($cycleLen, fn($v) => $v >= 21 && $v <= 35));
            $persenNormal = $total ? round($normalCount / $total * 100, 1) : 0;

            $distribusi = ['Folikel' => 0, 'Ovulasi' => 0, 'Luteal' => 0, 'Menstruasi' => 0];
            foreach ($allCycles as $s) {
                $fase = ucfirst(strtolower($s['current_phase'] ?? ''));
                if (isset($distribusi[$fase])) {
                    $distribusi[$fase]++;
                }
            }

            $perPage     = 10;
            $currentPage = max(1, (int) $request->get('page', 1));
            $totalPages  = max(1, (int) ceil($total / $perPage));
            $pageSiklus  = array_slice($allCycles, ($currentPage - 1) * $perPage, $perPage);

            return view('admin.siklus.index', compact(
                'pageSiklus', 'total', 'totalPages', 'currentPage',
                'rataRata', 'persenNormal', 'distribusi', 'search'
            ));

        } catch (\Exception $e) {
            Log::error('SiklusController@index: ' . $e->getMessage());
            return view('admin.siklus.index', [
                'pageSiklus'   => [], 'total' => 0, 'totalPages' => 1, 'currentPage' => 1,
                'rataRata'     => 0,  'persenNormal' => 0,
                'distribusi'   => ['Folikel' => 0, 'Ovulasi' => 0, 'Luteal' => 0, 'Menstruasi' => 0],
                'search'       => '',
                'error'        => 'Gagal memuat data: ' . $e->getMessage(),
            ]);
        }
    }

    // ================================================================
    // ========== API METHODS FOR MOBILE (TOKEN MANUAL) ==============
    // ================================================================

    /**
     * API: Get all cycles for authenticated user (Mobile)
     * GET /api/cycles
     */
    public function apiIndex(Request $request)
    {
        try {
            Log::info('API Index - Request received');
            
            $token = $request->bearerToken();
            
            Log::info('API Index - Bearer token: ' . ($token ? substr($token, 0, 50) . '...' : 'null'));
            
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
            
            $cycles = Cycle::where('id_user', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $cycles
            ]);
        } catch (\Exception $e) {
            Log::error('API Get cycles error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data siklus'
            ], 500);
        }
    }

    /**
     * API: Get latest cycle for authenticated user (Mobile)
     * GET /api/cycle/latest
     */
    public function apiLatest(Request $request)
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
            
            $cycle = Cycle::where('id_user', $userId)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if (!$cycle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada data siklus'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $cycle
            ]);
        } catch (\Exception $e) {
            Log::error('API Get latest cycle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data siklus'
            ], 500);
        }
    }

    /**
 * API: Store a new cycle (Mobile)
 * POST /api/cycle
 */
public function apiStore(Request $request)
{
    try {
        Log::info('API Store cycle - Request received', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan. Silakan login ulang.'
            ], 401);
        }
        
        $userId = $this->verifyToken($token);
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid. Silakan login ulang.'
            ], 401);
        }

        $validated = $request->validate([
            'last_period_date' => 'required|date',
            'previous_period_date' => 'nullable|date',
            'cycle_length_days' => 'nullable|integer|min:15|max:60',
            'period_duration_days' => 'nullable|integer|min:1|max:15',
            'pain_level' => 'nullable|integer|min:0|max:10',
            'stress_score_cycle' => 'nullable|integer|min:1|max:10',
            'sleep_hours_cycle' => 'nullable|numeric|min:1|max:24',
            'mood_score' => 'nullable|integer|min:1|max:10',
        ]);

        // ✅ PASTIKAN PAKAI 'id_user' (sesuai model)
        $cycleData = $validated;
        $cycleData['id_user'] = $userId;  // ← Kunci!
        
        Log::info('API Store cycle - Saving cycle data', ['cycle_data' => $cycleData]);

        $cycle = Cycle::create($cycleData);

        Log::info('API Store cycle - Success', [
            'cycle_id' => $cycle->_id,
            'saved_user_id' => $cycle->id_user  // ← pastikan tidak null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data siklus berhasil disimpan',
            'data' => $cycle,
            '_id' => $cycle->_id
        ], 201);

    } catch (\Exception $e) {
        Log::error('API Store cycle error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan data siklus: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * API: Update a cycle (Mobile)
     * PUT /api/cycle/{id}
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            // ============================================
            // STEP 1: LOG REQUEST YANG DITERIMA
            // ============================================
            Log::info('========================================');
            Log::info('API UPDATE CYCLE - REQUEST RECEIVED');
            Log::info('========================================');
            Log::info('ID Cycle: ' . $id);
            Log::info('Request Method: ' . $request->method());
            Log::info('Request Headers: ', $request->headers->all());
            Log::info('Request Body: ', $request->all());
            
            // ============================================
            // STEP 2: AMBIL DAN LOG TOKEN
            // ============================================
            $token = $request->bearerToken();
            
            Log::info('Bearer Token: ' . ($token ?: 'NULL'));
            
            if (!$token) {
                Log::warning('API Update - Token tidak ditemukan');
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401);
            }
            
            // ============================================
            // STEP 3: VERIFIKASI TOKEN
            // ============================================
            $userId = $this->verifyToken($token);
            
            Log::info('User ID dari token: ' . ($userId ?? 'NULL'));
            
            if (!$userId) {
                Log::warning('API Update - Token tidak valid');
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid'
                ], 401);
            }
            
            // ============================================
            // STEP 4: CARI CYCLE
            // ============================================
            Log::info('Mencari cycle dengan ID: ' . $id);
            
            $cycle = Cycle::find($id);
            
            if (!$cycle) {
                Log::warning('API Update - Cycle tidak ditemukan: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Data siklus tidak ditemukan'
                ], 404);
            }
            
            Log::info('Cycle ditemukan', [
                'cycle_id' => $cycle->_id,
                'cycle_user_id' => $cycle->id_user,
                'requesting_user_id' => $userId
            ]);
            
            // ============================================
            // STEP 5: CEK AUTHORIZATION
            // ============================================
            if ($cycle->id_user != $userId) {
                Log::warning('API Update - Unauthorized: User mismatch', [
                    'cycle_user' => $cycle->id_user,
                    'token_user' => $userId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Anda tidak memiliki akses ke data ini'
                ], 403);
            }
            
            // ============================================
            // STEP 6: VALIDASI INPUT
            // ============================================
            try {
                $validated = $request->validate([
                    'pain_level' => 'nullable|integer|min:0|max:10',
                    'stress_score_cycle' => 'nullable|integer|min:1|max:10',
                    'sleep_hours_cycle' => 'nullable|numeric|min:1|max:24',
                    'mood_score' => 'nullable|integer|min:1|max:10',
                    'weight_kg' => 'nullable|numeric|min:20|max:200',
                    'height_cm' => 'nullable|numeric|min:100|max:250',
                    'symptoms' => 'nullable|array',
                    'notes' => 'nullable|string',
                    'last_period_date' => 'nullable|date',
                    'previous_period_date' => 'nullable|date',
                    'cycle_length_days' => 'nullable|integer|min:15|max:60',
                    'period_duration_days' => 'nullable|integer|min:1|max:15',
                ]);
                
                Log::info('API Update - Validasi berhasil', ['validated' => $validated]);
                
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('API Update - Validasi gagal', ['errors' => $e->errors()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            
            // ============================================
            // STEP 7: UPDATE CYCLE
            // ============================================
            $cycle->update($validated);
            
            Log::info('API Update - Cycle berhasil diupdate', ['cycle_id' => $id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data siklus berhasil diupdate',
                'data' => $cycle
            ]);

        } catch (\Exception $e) {
            Log::error('API Update cycle error: ' . $e->getMessage());
            Log::error('API Update stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal update data siklus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete a cycle (Mobile)
     * DELETE /api/cycle/{id}
     */
    public function apiDestroy(Request $request, $id)
    {
        try {
            Log::info('API Destroy - Request received', ['id' => $id]);
            
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
            
            $cycle = Cycle::find($id);
            
            if (!$cycle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siklus tidak ditemukan'
                ], 404);
            }
            
            if ($cycle->id_user != $userId) {
                Log::warning('API Destroy - Unauthorized: User mismatch');
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $cycle->delete();
            
            Log::info('API Destroy - Cycle berhasil dihapus', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Data siklus berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('API Delete cycle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data siklus'
            ], 500);
        }
    }
}