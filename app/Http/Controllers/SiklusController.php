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

            // Search
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
            }

            // Pagination
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
}
