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

    public function index(Request $request)
    {
        try {
            $db = $this->getDb();
            $cyclesCol = $db->selectCollection('cycles');
            $usersCol  = $db->selectCollection('users');

            $allCycles = iterator_to_array($cyclesCol->find([]));

            // Map nama user
            $userMap = [];
            foreach ($usersCol->find([]) as $u) {
                $u = (array) $u;
                $userMap[(int)$u['id_user']] = $u['nama_lengkap'] ?? 'User #' . $u['id_user'];
            }

            // Proses data
            $processed = [];
            foreach ($allCycles as $doc) {
                $s = (array) $doc;

                $s['nama'] = $userMap[(int)($s['id_user'] ?? 0)] ?? '-';

                $s['tanggal_mulai_haid']   = $s['tanggal_mulai_haid']   ?? '-';
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
            $decoded = json_decode(base64_decode($token), true);
            
            if (!$decoded || !isset($decoded['exp']) || $decoded['exp'] < time()) {
                return null;
            }
            
            return $decoded['id_user'] ?? null;
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
                $s['panjang_siklus']       = (int)($s['cycle_length_days'] ?? 0);
                $s['pain_level']           = (int)($s['pain_level'] ?? 0);
                $s['stress_score_cycle']   = $s['stress_score_cycle']   ?? '-';
                $s['sleep_hours_cycle']    = $s['sleep_hours_cycle']    ?? '-';
                $s['current_phase']        = ucfirst(strtolower($s['current_phase'] ?? 'Lainnya'));

                $processed[] = $s;
            }

            // Filter Fase
            $filterPhase = $request->get('fase');
            if ($filterPhase) {
                $processed = array_values(array_filter($processed,
                    fn($s) => strtolower($s['current_phase']) === strtolower($filterPhase)
                ));
            }

            $search = trim($request->get('search', ''));
            if ($search) {
                $q = strtolower($search);
                $processed = array_values(array_filter($processed,
                    fn($s) => str_contains(strtolower($s['nama'] ?? ''), $q)
                ));
            }

            $total = count($processed);

            // Stats
            $lengths = array_filter(array_column($processed, 'panjang_siklus'));
            $rataRata = count($lengths) ? round(array_sum($lengths) / count($lengths), 1) : 0;

            $normalCount = count(array_filter($lengths, fn($v) => $v >= 21 && $v <= 35));
            $persenNormal = $total > 0 ? round(($normalCount / $total) * 100, 1) : 0;

            // Distribusi Fase (untuk card)
            $distribusi = ['Folikel' => 0, 'Ovulasi' => 0, 'Luteal' => 0, 'Menstruasi' => 0, 'Lainnya' => 0];
            foreach ($allCycles as $doc) {
                $s = (array) $doc;
                $fase = ucfirst(strtolower($s['current_phase'] ?? 'Lainnya'));
                $key = in_array($fase, ['Folikel','Ovulasi','Luteal','Menstruasi']) ? $fase : 'Lainnya';
                $distribusi[$key]++;
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
            $currentPage = max(1, (int)$request->get('page', 1));
            $totalPages  = max(1, (int)ceil($total / $perPage));
            $pageSiklus  = array_slice($processed, ($currentPage - 1) * $perPage, $perPage);

            return view('admin.siklus.index', compact(
                'pageSiklus', 'total', 'totalPages', 'currentPage',
                'rataRata', 'persenNormal', 'distribusi', 'search', 'filterPhase'
            ));

        } catch (\Exception $e) {
            Log::error('SiklusController: ' . $e->getMessage());
            return view('admin.siklus.index', ['error' => 'Gagal memuat data siklus.']);
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
            // Log request
            Log::info('API Store cycle - Request received', [
                'headers' => $request->headers->all(),
                'body' => $request->all()
            ]);

            $token = $request->bearerToken();
            
            Log::info('API Store cycle - Token: ' . ($token ? substr($token, 0, 50) . '...' : 'null'));
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan. Silakan login ulang.'
                ], 401);
            }
            
            $userId = $this->verifyToken($token);
            
            Log::info('API Store cycle - User ID from token: ' . ($userId ?? 'null'));
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid. Silakan login ulang.'
                ], 401);
            }

            $validated = $request->validate([
                'id_user' => 'required|integer',
                'last_period_date' => 'required|date',
                'previous_period_date' => 'nullable|date',
                'cycle_length_days' => 'nullable|integer|min:15|max:60',
                'period_duration_days' => 'nullable|integer|min:1|max:15',
                'stress_score_cycle' => 'nullable|integer|min:1|max:10',
                'sleep_hours_cycle' => 'nullable|numeric|min:1|max:24',
                'symptoms' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            // Verify user matches token
            if ($validated['id_user'] != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: ID user tidak cocok'
                ], 403);
            }

            $cycle = Cycle::create($validated);

            Log::info('API Store cycle - Success', ['cycle_id' => $cycle->_id]);

            return response()->json([
                'success' => true,
                'message' => 'Data siklus berhasil disimpan',
                'data' => $cycle,
                '_id' => $cycle->_id
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('API Store cycle - Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validated = $request->validate([
                'last_period_date' => 'nullable|date',
                'previous_period_date' => 'nullable|date',
                'cycle_length_days' => 'nullable|integer|min:15|max:60',
                'period_duration_days' => 'nullable|integer|min:1|max:15',
                'stress_score_cycle' => 'nullable|integer|min:1|max:10',
                'sleep_hours_cycle' => 'nullable|numeric|min:1|max:24',
                'symptoms' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            $cycle->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data siklus berhasil diupdate',
                'data' => $cycle
            ]);

        } catch (\Exception $e) {
            Log::error('API Update cycle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal update data siklus'
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
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $cycle->delete();

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