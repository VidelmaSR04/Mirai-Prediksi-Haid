<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class SiklusController extends Controller
{
    // ----------------------------------------------------------------
    // Koneksi MongoDB
    // ----------------------------------------------------------------
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    // ----------------------------------------------------------------
    // Ambil semua siklus — hanya field yang ADA di collection cycles
    //
    // Field aktual di MongoDB cycles:
    //   user_id, cycle_length_days, prev_cycle_length,
    //   pain_level, stress_score_cycle, sleep_hours_cycle, mood_score
    // ----------------------------------------------------------------
    private function getAllCycles(): array
    {
        $all = [];
        foreach ($this->getDb()->selectCollection('cycles')->find([]) as $doc) {
            $doc = (array) $doc;

            $all[] = [
                'user_id'            => isset($doc['user_id']) ? (string) $doc['user_id'] : null,
                'cycle_length_days'  => isset($doc['cycle_length_days'])  ? (int) $doc['cycle_length_days']  : null,
                'prev_cycle_length'  => isset($doc['prev_cycle_length'])  ? (int) $doc['prev_cycle_length']  : null,
                'pain_level'         => isset($doc['pain_level'])         ? (int) $doc['pain_level']         : null,
                'stress_score_cycle' => $doc['stress_score_cycle'] ?? null,
                'sleep_hours_cycle'  => $doc['sleep_hours_cycle']  ?? null,
                'mood_score'         => isset($doc['mood_score'])         ? (int) $doc['mood_score']         : null,
            ];
        }
        return $all;
    }

    // ----------------------------------------------------------------
    // Map user_id → nama_lengkap dari collection users
    // Mendukung berbagai kemungkinan nama field ID di collection users
    // ----------------------------------------------------------------
    private function getUserMap(): array
    {
        $map = [];


        foreach ($this->getDb()->selectCollection('users')->find([]) as $doc) {
            $doc = (array) $doc;

            // Coba semua kemungkinan nama field ID pengguna
            $uid = $doc['user_id']
                ?? $doc['id_user']
                ?? $doc['userId']
                ?? $doc['id']
                ?? null;

            if ($uid) {
                // Cast ke string agar cocok saat matching dengan user_id dari cycles
                $uid = (string) $uid;

                $map[$uid] = $doc['nama_lengkap']
                    ?? $doc['name']
                    ?? $doc['nama']
                    ?? '-';
            }
        }
        return $map;
    }

    // ----------------------------------------------------------------
    // INDEX — daftar siklus dengan filter & pagination
    // ----------------------------------------------------------------
    public function index(Request $request)
    {
        try {
            $allCycles = $this->getAllCycles();
            $userMap   = $this->getUserMap();

            // Gabungkan nama user ke setiap record
            // user_id sudah di-cast ke string di getAllCycles(), map key juga string
            foreach ($allCycles as &$s) {
                $uid      = $s['user_id'] ?? '';
                $s['nama'] = $userMap[$uid] ?? ('User #' . ($uid ?: '-'));
            }
            unset($s);

            // Search by nama atau user_id
            $search = trim($request->get('search', ''));
            if ($search !== '') {
                $q         = strtolower($search);
                $allCycles = array_values(array_filter(
                    $allCycles,
                    fn($s) =>
                        str_contains(strtolower($s['nama']),    $q) ||
                        str_contains(strtolower($s['user_id'] ?? ''), $q)
                ));
            }

            // Filter pain level (ringan/sedang/berat) — opsional
            $filterPain = $request->get('pain');
            if ($filterPain !== null && $filterPain !== '') {
                $allCycles = array_values(array_filter($allCycles, function ($s) use ($filterPain) {
                    $p = $s['pain_level'] ?? 0;
                    return match ($filterPain) {
                        'ringan' => $p >= 1 && $p <= 3,
                        'sedang' => $p >= 4 && $p <= 6,
                        'berat'  => $p >= 7 && $p <= 10,
                        default  => true,
                    };
                }));
            }

            $total = count($allCycles);

            // --------------------------------------------------------
            // Stats ringkasan
            // --------------------------------------------------------
            $lengths     = array_filter(array_column($allCycles, 'cycle_length_days'));
            $rataRata    = count($lengths) ? round(array_sum($lengths) / count($lengths), 1) : 0;

            $normalCount  = count(array_filter($lengths, fn($v) => $v >= 21 && $v <= 35));
            $persenNormal = $total > 0 ? round(($normalCount / $total) * 100, 1) : 0;

            // Distribusi panjang siklus: pendek / normal / panjang
            $distribusiPanjang = [
                'Pendek (<21)'   => count(array_filter($lengths, fn($v) => $v < 21)),
                'Normal (21–35)' => $normalCount,
                'Panjang (>35)'  => count(array_filter($lengths, fn($v) => $v > 35)),
            ];

            // Rata-rata pain level
            $painValues   = array_filter(array_column($allCycles, 'pain_level'));
            $rataRataPain = count($painValues) ? round(array_sum($painValues) / count($painValues), 1) : 0;

            // --------------------------------------------------------
            // Pagination
            // --------------------------------------------------------
            $perPage     = 10;
            $currentPage = max(1, (int) $request->get('page', 1));
            $totalPages  = max(1, (int) ceil($total / $perPage));
            $pageSiklus  = array_slice($allCycles, ($currentPage - 1) * $perPage, $perPage);

            return view('admin.siklus.index', compact(
                'pageSiklus',
                'total',
                'totalPages',
                'currentPage',
                'rataRata',
                'persenNormal',
                'distribusiPanjang',
                'rataRataPain',
                'search',
                'filterPain'
            ));

        } catch (\Exception $e) {
            Log::error('SiklusController@index: ' . $e->getMessage());

            return view('admin.siklus.index', [
                'pageSiklus'        => [],
                'total'             => 0,
                'totalPages'        => 1,
                'currentPage'       => 1,
                'rataRata'          => 0,
                'persenNormal'      => 0,
                'distribusiPanjang' => [],
                'rataRataPain'      => 0,
                'search'            => '',
                'filterPain'        => '',
                'error'             => 'Gagal memuat data siklus.',
            ]);
        }
    }
}
