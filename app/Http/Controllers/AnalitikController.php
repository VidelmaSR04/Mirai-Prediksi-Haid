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
            $db        = $this->getDb();
            $cyclesCol = $db->selectCollection('cycles');
            $usersCol  = $db->selectCollection('users');

            $allCycles = iterator_to_array($cyclesCol->find([]));
            $allUsers  = iterator_to_array($usersCol->find([]));

            $totalSiklus = count($allCycles);
            $totalUser   = count($allUsers);

            // ── KPI ──────────────────────────────────────────────────────────
            $cycleLengths = array_filter(
                array_column($allCycles, 'cycle_length_days'),
                fn($v) => is_numeric($v) && $v > 0
            );
            $rataRata     = count($cycleLengths)
                ? round(array_sum($cycleLengths) / count($cycleLengths), 1)
                : 0;
            $normalCount  = count(array_filter($cycleLengths, fn($v) => $v >= 21 && $v <= 35));
            $persenNormal = $totalSiklus > 0
                ? round(($normalCount / $totalSiklus) * 100, 1)
                : 0;

            $stats = [
                'total_user'    => $totalUser,
                'total_siklus'  => $totalSiklus,
                'rata_siklus'   => $rataRata,
                'persen_normal' => $persenNormal,
            ];

            // ── 1. HISTOGRAM: Distribusi Panjang Siklus ──────────────────────
            // Bucket per 3 hari: 15-17, 18-20, 21-23, ..., 42-44
            $histBuckets = [];
            foreach ($allCycles as $c) {
                $c   = (array) $c;
                $len = (int)($c['cycle_length_days'] ?? 0);
                if ($len < 10 || $len > 60) continue;
                $bucket = (int)(floor(($len - 10) / 3)) * 3 + 10;
                $label  = "{$bucket}-" . ($bucket + 2);
                $histBuckets[$label] = ($histBuckets[$label] ?? 0) + 1;
            }
            ksort($histBuckets);
            $histogramData = [
                'labels' => array_keys($histBuckets),
                'values' => array_values($histBuckets),
            ];

            // ── 2. BAR: Rata-rata Siklus per Fase ────────────────────────────
            $faseMap = [
                'follicular' => 'Folikel', 'follicle' => 'Folikel', 'folikel' => 'Folikel',
                'ovulation'  => 'Ovulasi', 'ovulasi'  => 'Ovulasi',
                'luteal'     => 'Luteal',
                'menstruation' => 'Menstruasi', 'menstrual' => 'Menstruasi', 'menstruasi' => 'Menstruasi',
            ];
            $faseGroups = ['Folikel' => [], 'Ovulasi' => [], 'Luteal' => [], 'Menstruasi' => []];
            foreach ($allCycles as $c) {
                $c     = (array) $c;
                $raw   = strtolower(trim($c['current_phase'] ?? ''));
                $fase  = $faseMap[$raw] ?? null;
                $len   = (float)($c['cycle_length_days'] ?? 0);
                if ($fase && $len > 0 && isset($faseGroups[$fase])) {
                    $faseGroups[$fase][] = $len;
                }
            }
            $faseBarData = [];
            foreach ($faseGroups as $fase => $vals) {
                $faseBarData[$fase] = count($vals) > 0
                    ? round(array_sum($vals) / count($vals), 1)
                    : 0;
            }

            // ── 3. BAR: Rata-rata Stress per Fase ────────────────────────────
            $faseStress = ['Folikel' => [], 'Ovulasi' => [], 'Luteal' => [], 'Menstruasi' => []];
            $faseSleep  = ['Folikel' => [], 'Ovulasi' => [], 'Luteal' => [], 'Menstruasi' => []];
            foreach ($allCycles as $c) {
                $c      = (array) $c;
                $raw    = strtolower(trim($c['current_phase'] ?? ''));
                $fase   = $faseMap[$raw] ?? null;
                $stress = (float)($c['stress_score_cycle'] ?? 0);
                $sleep  = (float)($c['sleep_hours_cycle']  ?? 0);
                if ($fase && isset($faseStress[$fase])) {
                    if ($stress > 0) $faseStress[$fase][] = $stress;
                    if ($sleep  > 0) $faseSleep[$fase][]  = $sleep;
                }
            }
            $stressPerFase = [];
            $sleepPerFase  = [];
            foreach (['Folikel','Ovulasi','Luteal','Menstruasi'] as $f) {
                $stressPerFase[$f] = count($faseStress[$f]) > 0
                    ? round(array_sum($faseStress[$f]) / count($faseStress[$f]), 2)
                    : 0;
                $sleepPerFase[$f]  = count($faseSleep[$f]) > 0
                    ? round(array_sum($faseSleep[$f]) / count($faseSleep[$f]), 2)
                    : 0;
            }

            // ── 4. LINE: Tren Rata-rata Siklus per Bulan ─────────────────────
            $trenBulan = [];
            foreach ($allCycles as $c) {
                $c   = (array) $c;
                $tgl = $c['tanggal_mulai_haid'] ?? null;
                $len = (float)($c['cycle_length_days'] ?? 0);
                if (!$tgl || $len <= 0) continue;
                try {
                    $dt    = new \DateTime(is_string($tgl) ? $tgl : (string)$tgl);
                    $key   = $dt->format('Y-m');
                    if (!isset($trenBulan[$key])) $trenBulan[$key] = [];
                    $trenBulan[$key][] = $len;
                } catch (\Exception $e) { /* skip */ }
            }
            ksort($trenBulan);
            // Ambil 12 bulan terakhir
            $trenBulan = array_slice($trenBulan, -12, 12, true);
            $trenData  = [
                'labels' => array_map(fn($k) => date('M Y', strtotime($k . '-01')), array_keys($trenBulan)),
                'values' => array_map(fn($v) => round(array_sum($v) / count($v), 1), array_values($trenBulan)),
            ];

            // ── 5. BAR HORIZONTAL: Top 10 Pain Level Distribution ────────────
            $painCount = [];
            foreach ($allCycles as $c) {
                $c    = (array) $c;
                $pain = (int)($c['pain_level'] ?? 0);
                if ($pain >= 1 && $pain <= 10) {
                    $painCount[$pain] = ($painCount[$pain] ?? 0) + 1;
                }
            }
            ksort($painCount);
            $painData = [
                'labels' => array_map(fn($k) => "Level $k", array_keys($painCount)),
                'values' => array_values($painCount),
            ];

            // ── 6. DONUT: Status Siklus (Normal vs Tidak Normal) ─────────────
            $statusData = [
                'Normal'       => $normalCount,
                'Tidak Normal' => $totalSiklus - $normalCount,
            ];

            return view('admin.analitik.index', compact(
                'stats',
                'histogramData',
                'faseBarData',
                'stressPerFase',
                'sleepPerFase',
                'trenData',
                'painData',
                'statusData'
            ));

        } catch (\Exception $e) {
            Log::error('AnalitikController@index: ' . $e->getMessage());
            return view('admin.analitik.index', [
                'stats'         => ['total_user' => 0, 'total_siklus' => 0, 'rata_siklus' => 0, 'persen_normal' => 0],
                'histogramData' => ['labels' => [], 'values' => []],
                'faseBarData'   => [],
                'stressPerFase' => [],
                'sleepPerFase'  => [],
                'trenData'      => ['labels' => [], 'values' => []],
                'painData'      => ['labels' => [], 'values' => []],
                'statusData'    => [],
                'error'         => 'Gagal memuat data analitik: ' . $e->getMessage(),
            ]);
        }
    }
}
