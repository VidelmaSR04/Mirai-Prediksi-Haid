<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class AnalitikController extends Controller
{
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    /** Ambil semua cycles sebagai array flat, sudah pakai field baru */
    private function getAllCycles(): array
    {
        $all = [];
        foreach ($this->getDb()->selectCollection('cycles')->find([]) as $doc) {
            $all[] = (array) $doc;
        }
        return $all;
    }

    /** Ambil semua users sebagai array flat */
    private function getAllUsers(): array
    {
        $all = [];
        foreach ($this->getDb()->selectCollection('users')->find([]) as $doc) {
            $all[] = (array) $doc;
        }
        return $all;
    }

    public function index()
    {
        try {
            $allCycles = $this->getAllCycles();
            $allUsers  = $this->getAllUsers();
            $total     = count($allCycles);

            // ── KPI ───────────────────────────────────────────────────────────
            // Field baru: cycle_length_days (bukan panjang_siklus)
            $cycleLen = array_filter(
                array_column($allCycles, 'cycle_length_days'),
                fn($v) => is_numeric($v)
            );
            $rataRata    = count($cycleLen) ? round(array_sum($cycleLen) / count($cycleLen), 1) : 0;
            $normalCount = count(array_filter($cycleLen, fn($v) => $v >= 21 && $v <= 35));
            $persenNorm  = $total ? round($normalCount / $total * 100, 1) : 0;

            $stats = [
                'total_user'    => count($allUsers),
                'total_siklus'  => $total,
                'rata_siklus'   => $rataRata,
                'persen_normal' => $persenNorm,
            ];

            // ── EDA: Stress vs Cycle ──────────────────────────────────────────
            // Field baru: stress_score_cycle & cycle_length_days
            $stressData = [];
            foreach ($allCycles as $s) {
                if (isset($s['stress_score_cycle'], $s['cycle_length_days'])) {
                    $stressData[] = [
                        'x' => round((float) $s['stress_score_cycle'], 2),
                        'y' => (float) $s['cycle_length_days'],
                    ];
                }
            }

            // ── EDA: Sleep vs Cycle ───────────────────────────────────────────
            // Field baru: sleep_hours_cycle
            $sleepData = [];
            foreach ($allCycles as $s) {
                if (isset($s['sleep_hours_cycle'], $s['cycle_length_days'])) {
                    $sleepData[] = [
                        'x' => round((float) $s['sleep_hours_cycle'], 1),
                        'y' => (float) $s['cycle_length_days'],
                    ];
                }
            }

            // ── EDA: BMI vs Cycle ─────────────────────────────────────────────
            // BMI ada di collection users, join by id_user
            $userBmiMap = [];
            foreach ($allUsers as $u) {
                $userBmiMap[$u['id_user'] ?? 0] = $u['bmi'] ?? null;
            }
            $bmiData = [];
            foreach ($allCycles as $s) {
                $bmi = $userBmiMap[$s['id_user'] ?? 0] ?? null;
                if ($bmi !== null && isset($s['cycle_length_days'])) {
                    $bmiData[] = [
                        'x' => round((float) $bmi, 1),
                        'y' => (float) $s['cycle_length_days'],
                    ];
                }
            }

            // ── EDA: Prev Cycle vs Current ────────────────────────────────────
            // Field baru: prev_cycle_length
            $prevCycleData = [];
            foreach ($allCycles as $s) {
                if (isset($s['prev_cycle_length'], $s['cycle_length_days'])) {
                    $prevCycleData[] = [
                        'x' => (float) $s['prev_cycle_length'],
                        'y' => (float) $s['cycle_length_days'],
                    ];
                }
            }

            // ── Distribusi Panjang Siklus ─────────────────────────────────────
            $distribusi = [];
            foreach ($cycleLen as $p) {
                $p = (int) $p;
                $distribusi[$p] = ($distribusi[$p] ?? 0) + 1;
            }
            ksort($distribusi);

            // ── Demografi Usia (field baru: age, bukan usia) ──────────────────
            $usiaBucket = ['18-22' => 0, '23-27' => 0, '28-32' => 0, '33-37' => 0, '38+' => 0];
            foreach ($allUsers as $u) {
                $this->bucketUsia($usiaBucket, (int) ($u['age'] ?? 0));
            }

            return view('admin.analitik.index', compact(
                'stats', 'stressData', 'sleepData', 'bmiData',
                'prevCycleData', 'distribusi', 'usiaBucket'
            ));

        } catch (\Exception $e) {
            Log::error('AnalitikController@index: ' . $e->getMessage());
            return view('admin.analitik.index', [
                'stats'         => ['total_user' => 0, 'total_siklus' => 0, 'rata_siklus' => 0, 'persen_normal' => 0],
                'stressData'    => [], 'sleepData'     => [],
                'bmiData'       => [], 'prevCycleData' => [],
                'distribusi'    => [], 'usiaBucket'    => [],
                'error'         => $e->getMessage(),
            ]);
        }
    }

    private function bucketUsia(array &$bucket, int $usia): void
    {
        if ($usia >= 18 && $usia <= 22)      $bucket['18-22']++;
        elseif ($usia >= 23 && $usia <= 27)  $bucket['23-27']++;
        elseif ($usia >= 28 && $usia <= 32)  $bucket['28-32']++;
        elseif ($usia >= 33 && $usia <= 37)  $bucket['33-37']++;
        elseif ($usia > 37)                  $bucket['38+']++;
    }
}
