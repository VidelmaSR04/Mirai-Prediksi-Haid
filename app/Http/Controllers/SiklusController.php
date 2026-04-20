<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

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

    public function index(Request $request)
    {
        try {
            $allCycles = $this->getAllCycles();
            $userMap   = $this->getUserMap();

            // Inject nama dari users ke setiap cycle
            foreach ($allCycles as &$s) {
                $s['nama'] = $userMap[$s['id_user'] ?? 0] ?? 'User #' . ($s['id_user'] ?? '-');
                // Alias field baru ke blade lama agar kompatibel
                $s['panjang_siklus']       = $s['cycle_length_days']   ?? 0;
                $s['tanggal_mulai_haid']   = $s['tanggal_mulai_haid']  ?? '-';
                $s['tanggal_selesai_haid'] = $s['tanggal_selesai_haid'] ?? '-';
                $s['stress_score_cycle']   = $s['stress_score_cycle']  ?? '-';
                $s['sleep_hours_cycle']    = $s['sleep_hours_cycle']   ?? '-';
                $s['pain_level']           = $s['pain_level']          ?? '-';
            }
            unset($s);

            // Search
            $search = trim($request->get('search', ''));
            if ($search) {
                $q         = strtolower($search);
                $allCycles = array_values(array_filter($allCycles,
                    fn($s) => str_contains(strtolower($s['nama'] ?? ''), $q)
                ));
            }

            // Stats
            $total    = count($allCycles);
            $cycleLen = array_filter(
                array_column($allCycles, 'cycle_length_days'),
                fn($v) => is_numeric($v)
            );
            $rataRata    = count($cycleLen) ? round(array_sum($cycleLen) / count($cycleLen), 1) : 0;
            $normalCount = count(array_filter($cycleLen, fn($v) => $v >= 21 && $v <= 35));
            $persenNormal = $total ? round($normalCount / $total * 100, 1) : 0;

            // Distribusi fase — pakai field current_phase dari data baru
            $distribusi = ['Folikel' => 0, 'Ovulasi' => 0, 'Luteal' => 0, 'Menstruasi' => 0];
            foreach ($allCycles as $s) {
                $fase = ucfirst(strtolower($s['current_phase'] ?? ''));
                if (isset($distribusi[$fase])) {
                    $distribusi[$fase]++;
                }
            }

            // Pagination
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
}
