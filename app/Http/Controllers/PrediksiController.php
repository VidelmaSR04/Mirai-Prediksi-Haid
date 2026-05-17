<?php

namespace App\Http\Controllers;

use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class PrediksiController extends Controller
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

            // mapping user
            $userMap = [];
            foreach ($db->users->find([]) as $doc) {
                $doc = (array) $doc;
                $uid = (string)($doc['id_user'] ?? '');
                if ($uid) {
                    $userMap[$uid] = $doc;
                }
            }

            $prediksi = [];
            $faseCount = [
                'folikel' => 0,
                'ovulasi' => 0,
                'luteal' => 0,
                'menstruasi' => 0
            ];

            foreach ($db->predictions->find([]) as $doc) {
                $doc = (array) $doc;

                $uid = (string)($doc['user_id'] ?? '');
                $namaUser = $userMap[$uid]['nama_lengkap'] ?? ('User ' . $uid);

                $predictionsArray = $doc['predictions'] ?? [];

                foreach ($predictionsArray as $item) {
                    $item = (array)$item;
                    $cycle = (array)($item['current_cycle'] ?? []);

                    $faseRaw = strtolower($cycle['cycle_phase'] ?? '');

                    // translate English → Indonesia
                    $fase = match($faseRaw) {
                        'follicular' => 'folikel',
                        'ovulation' => 'ovulasi',
                        'luteal' => 'luteal',
                        'menstrual', 'menstruation' => 'menstruasi',
                        default => 'folikel'
                    };

                    if (isset($faseCount[$fase])) {
                        $faseCount[$fase]++;
                    }

                    $prediksi[] = [
                        'id_user' => $uid,
                        'nama' => $namaUser,
                        'usia' => $userMap[$uid]['age'] ?? '-',
                        'panjang_siklus' => $cycle['cycle_length_days'] ?? '-',
                        'pattern' => ucfirst($fase),
                        'ovulasi' => $cycle['ovulation_result'] ?? '-',
                        'tanggal_mulai_haid' => $cycle['start_date'] ?? '-',
                        'confidence_score' => round(($cycle['log_consistency_score'] ?? 0) * 100),
                        'mae_error' => '-',
                    ];
                }
            }

            $totalPrediksi = count($prediksi);

            $confValues = array_filter(array_column($prediksi, 'confidence_score'), 'is_numeric');
            $avgConf = count($confValues)
                ? round(array_sum($confValues) / count($confValues), 1)
                : 0;

            $sedangSubur = $faseCount['ovulasi'];

            return view('admin.prediksi.index', compact(
                'prediksi',
                'faseCount',
                'totalPrediksi',
                'avgConf',
                'sedangSubur'
            ));

        } catch (\Exception $e) {
            Log::error('Prediksi error: ' . $e->getMessage());

            return view('admin.prediksi.index', [
                'prediksi' => [],
                'faseCount' => [
                    'folikel' => 0,
                    'ovulasi' => 0,
                    'luteal' => 0,
                    'menstruasi' => 0
                ],
                'totalPrediksi' => 0,
                'avgConf' => 0,
                'sedangSubur' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }
}