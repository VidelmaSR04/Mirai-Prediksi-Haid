<?php

namespace App\Http\Controllers;

use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class AnalitikController extends Controller
{
    private function getDb()
    {
        $client = new MongoClient(
            env('MONGODB_URI', 'mongodb://127.0.0.1:27017')
        );

        return $client->selectDatabase(
            env('MONGODB_DATABASE', 'mirai')
        );
    }

    public function index()
    {
        try {

            $db = $this->getDb();

            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            $users = iterator_to_array(
                $db->users->find([])
            );

            $totalUsers = count($users);

            /*
            |--------------------------------------------------------------------------
            | PREDICTION MAP
            |--------------------------------------------------------------------------
            */

            $predictionMap = [];

            foreach ($db->predictions->find([]) as $pred)
            {
                $pred = (array)$pred;

                $userId = $pred['user_id'] ?? '';

                if (!$userId) continue;

                $predictionMap[$userId] = [
                    'phase' => $pred['cycle_phase'] ?? '',
                    'start_date' => $pred['start_date'] ?? ''
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | VARIABLE
            |--------------------------------------------------------------------------
            */

            $cycleLengths = [];
            $painLevels = [];

            $normal = 0;
            $tidakNormal = 0;

            $faseStats = [
                'Folikel' => [],
                'Ovulasi' => [],
                'Luteal' => [],
                'Menstruasi' => []
            ];

            /*
            |--------------------------------------------------------------------------
            | TREN BULANAN
            |--------------------------------------------------------------------------
            */

            $monthlyData = [];

            foreach ($db->predictions->find([]) as $pred)
            {
                $pred = (array)$pred;

                if (
                    empty($pred['start_date']) ||
                    empty($pred['cycle_length_days'])
                ) {
                    continue;
                }

                $month = date(
                    'M Y',
                    strtotime($pred['start_date'])
                );

                $monthlyData[$month][] =
                    (int)$pred['cycle_length_days'];
            }

            /*
            |--------------------------------------------------------------------------
            | CYCLES
            |--------------------------------------------------------------------------
            */

            foreach ($db->cycles->find([]) as $cycle)
            {
                $cycle = (array)$cycle;

                $userId = $cycle['user_id'] ?? '';

                $length =
                    (int)($cycle['cycle_length_days'] ?? 0);

                $pain =
                    (int)($cycle['pain_level'] ?? 0);

                $stress =
                    (float)($cycle['stress_score_cycle'] ?? 0);

                $sleep =
                    (float)($cycle['sleep_hours_cycle'] ?? 0);

                if ($length <= 0)
                {
                    continue;
                }

                $cycleLengths[] = $length;

                if ($pain > 0)
                {
                    $painLevels[] = $pain;
                }

                if ($length >= 21 && $length <= 35)
                {
                    $normal++;
                }
                else
                {
                    $tidakNormal++;
                }

                $phaseRaw = strtolower(
                    $predictionMap[$userId]['phase'] ?? ''
                );

                $fase = match($phaseRaw)
                {
                    'follicular' => 'Folikel',
                    'ovulation' => 'Ovulasi',
                    'luteal' => 'Luteal',
                    'menstrual' => 'Menstruasi',
                    'menstruation' => 'Menstruasi',
                    default => null
                };

                if ($fase)
                {
                    $faseStats[$fase][] = [
                        'cycle' => $length,
                        'stress' => $stress,
                        'sleep' => $sleep
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | KPI
            |--------------------------------------------------------------------------
            */

            $totalSiklus = count($cycleLengths);

            $rataSiklus =
                $totalSiklus
                ? round(array_sum($cycleLengths) / $totalSiklus, 1)
                : 0;

            $persenNormal =
                $totalSiklus
                ? round(($normal / $totalSiklus) * 100, 1)
                : 0;

            $stats = [
                'total_user' => $totalUsers,
                'total_siklus' => $totalSiklus,
                'rata_siklus' => $rataSiklus,
                'persen_normal' => $persenNormal
            ];

            /*
            |--------------------------------------------------------------------------
            | HISTOGRAM
            |--------------------------------------------------------------------------
            */

            $bins = [
                '21-25' => 0,
                '26-30' => 0,
                '31-35' => 0,
                '36-40' => 0,
                '>40' => 0
            ];

            foreach ($cycleLengths as $len)
            {
                if ($len <= 25)
                {
                    $bins['21-25']++;
                }
                elseif ($len <= 30)
                {
                    $bins['26-30']++;
                }
                elseif ($len <= 35)
                {
                    $bins['31-35']++;
                }
                elseif ($len <= 40)
                {
                    $bins['36-40']++;
                }
                else
                {
                    $bins['>40']++;
                }
            }

            $histogramData = [
                'labels' => array_keys($bins),
                'values' => array_values($bins)
            ];

            /*
            |--------------------------------------------------------------------------
            | TREN
            |--------------------------------------------------------------------------
            */

            $trenLabels = [];
            $trenValues = [];

            foreach ($monthlyData as $month => $values)
            {
                $trenLabels[] = $month;

                $trenValues[] =
                    round(
                        array_sum($values) /
                        count($values),
                        1
                    );
            }

            $trenData = [
                'labels' => $trenLabels,
                'values' => $trenValues
            ];

            /*
            |--------------------------------------------------------------------------
            | PAIN
            |--------------------------------------------------------------------------
            */

            $painCount = [];

            for ($i = 1; $i <= 10; $i++)
            {
                $painCount[$i] = 0;
            }

            foreach ($painLevels as $pain)
            {
                if (isset($painCount[$pain]))
                {
                    $painCount[$pain]++;
                }
            }

            $painData = [
                'labels' => array_keys($painCount),
                'values' => array_values($painCount)
            ];

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $statusData = [
                'Normal' => $normal,
                'Tidak Normal' => $tidakNormal
            ];

            /*
            |--------------------------------------------------------------------------
            | FASE
            |--------------------------------------------------------------------------
            */

            $faseBarData = [];
            $stressPerFase = [];
            $sleepPerFase = [];

            foreach ($faseStats as $fase => $items)
            {
                if (count($items) == 0)
                {
                    $faseBarData[$fase] = 0;
                    $stressPerFase[$fase] = 0;
                    $sleepPerFase[$fase] = 0;
                    continue;
                }

                $faseBarData[$fase] =
                    round(
                        array_sum(array_column($items, 'cycle'))
                        / count($items),
                        1
                    );

                $stressPerFase[$fase] =
                    round(
                        array_sum(array_column($items, 'stress'))
                        / count($items),
                        1
                    );

                $sleepPerFase[$fase] =
                    round(
                        array_sum(array_column($items, 'sleep'))
                        / count($items),
                        1
                    );
            }

            return view('admin.analitik.index', compact(
                'stats',
                'histogramData',
                'trenData',
                'painData',
                'statusData',
                'faseBarData',
                'stressPerFase',
                'sleepPerFase'
            ));

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}