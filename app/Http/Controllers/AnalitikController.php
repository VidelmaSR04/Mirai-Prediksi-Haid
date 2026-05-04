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

    public function index()
    {
        try {
            $db = $this->getDb();
            $cyclesCol = $db->selectCollection('cycles');
            $usersCol  = $db->selectCollection('users');

            $allCycles = iterator_to_array($cyclesCol->find([]));
            $allUsers  = iterator_to_array($usersCol->find([]));

            $totalSiklus = count($allCycles);
            $totalUser   = count($allUsers);

            // KPI
            $cycleLengths = array_filter(array_column($allCycles, 'cycle_length_days'), fn($v) => is_numeric($v) && $v > 0);
            $rataRata     = count($cycleLengths) ? round(array_sum($cycleLengths) / count($cycleLengths), 1) : 0;
            $normalCount  = count(array_filter($cycleLengths, fn($v) => $v >= 21 && $v <= 35));
            $persenNormal = $totalSiklus > 0 ? round(($normalCount / $totalSiklus) * 100, 1) : 0;

            $stats = [
                'total_user'    => $totalUser,
                'total_siklus'  => $totalSiklus,
                'rata_siklus'   => $rataRata,
                'persen_normal' => $persenNormal,
            ];

            // Stress vs Siklus
            $stressData = [];
            foreach ($allCycles as $c) {
                $c = (array) $c;
                if (isset($c['stress_score_cycle'], $c['cycle_length_days'])) {
                    $stressData[] = [
                        'x' => (float)$c['stress_score_cycle'],
                        'y' => (float)$c['cycle_length_days']
                    ];
                }
            }

            // Tidur vs Siklus
            $sleepData = [];
            foreach ($allCycles as $c) {
                $c = (array) $c;
                if (isset($c['sleep_hours_cycle'], $c['cycle_length_days'])) {
                    $sleepData[] = [
                        'x' => (float)$c['sleep_hours_cycle'],
                        'y' => (float)$c['cycle_length_days']
                    ];
                }
            }

            // BMI vs Siklus
            $userBmi = [];
            foreach ($allUsers as $u) {
                $u = (array) $u;
                $userBmi[$u['id_user'] ?? 0] = $u['bmi'] ?? null;
            }
            $bmiData = [];
            foreach ($allCycles as $c) {
                $c = (array) $c;
                $bmi = $userBmi[$c['id_user'] ?? 0] ?? null;
                if ($bmi !== null && isset($c['cycle_length_days'])) {
                    $bmiData[] = [
                        'x' => (float)$bmi,
                        'y' => (float)$c['cycle_length_days']
                    ];
                }
            }

            // Prev vs Current Cycle
            $prevCycleData = [];
            foreach ($allCycles as $c) {
                $c = (array) $c;
                if (isset($c['prev_cycle_length'], $c['cycle_length_days'])) {
                    $prevCycleData[] = [
                        'x' => (float)$c['prev_cycle_length'],
                        'y' => (float)$c['cycle_length_days']
                    ];
                }
            }

            return view('admin.analitik.index', compact(
                'stats',
                'stressData',
                'sleepData',
                'bmiData',
                'prevCycleData'
            ));

        } catch (\Exception $e) {
            Log::error('AnalitikController@index: ' . $e->getMessage());
            return view('admin.analitik.index', [
                'stats'         => ['total_user' => 0, 'total_siklus' => 0, 'rata_siklus' => 0, 'persen_normal' => 0],
                'stressData'    => [],
                'sleepData'     => [],
                'bmiData'       => [],
                'prevCycleData' => [],
                'error'         => 'Gagal memuat data analitik: ' . $e->getMessage()
            ]);
        }
    }
}
