<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

            // Ambil data users untuk nama
            $userMap = [];
            foreach ($db->selectCollection('users')->find([]) as $doc) {
                $doc = (array) $doc;
                $userMap[(int)$doc['id_user']] = $doc['nama_lengkap'] ?? 'User #' . $doc['id_user'];
            }

            // Ambil semua prediksi
            $prediksiRaw = iterator_to_array($db->selectCollection('predictions')->find([], [
                'sort' => ['created_at' => -1]
            ]));

            $prediksi = [];
            foreach ($prediksiRaw as $p) {
                $p = (array) $p;
                $uid = (int)($p['id_user'] ?? 0);

                $prediksi[] = [
                    'id_user'                => $uid,
                    'nama'                   => $userMap[$uid] ?? '-',
                    'usia'                   => '-', // nanti bisa diambil dari users
                    'panjang_siklus'         => $p['predicted_cycle_length'] ?? $p['cycle_length_used'] ?? '-',
                    'pattern'                => $p['current_phase'] ?? '-',
                    'ovulasi'                => $p['predicted_ovulation'] ?? '-',
                    'tanggal_mulai_haid'     => $p['predicted_next_period'] ?? '-',
                    'fertile_start'          => $p['fertile_start'] ?? '-',
                    'fertile_end'            => $p['fertile_end'] ?? '-',
                    'confidence_score'       => (int)($p['confidence_score'] ?? 0),
                    'mae_error'              => round($p['mae_error'] ?? 0, 2),
                    'cycle_status'           => $p['cycle_status'] ?? '-',
                ];
            }

            $totalPrediksi = count($prediksi);

            // Distribusi fase
            $faseCount = ['folikel' => 0, 'ovulasi' => 0, 'luteal' => 0, 'menstruasi' => 0];
            foreach ($prediksi as $p) {
                $f = strtolower($p['pattern'] ?? '');
                if (isset($faseCount[$f])) {
                    $faseCount[$f]++;
                }
            }

            // Stats
            $avgConf = $totalPrediksi > 0
                ? round(array_sum(array_column($prediksi, 'confidence_score')) / $totalPrediksi, 1)
                : 0;

            $sedangSubur = $faseCount['ovulasi'] ?? 0;

            return view('admin.prediksi.index', compact(
                'prediksi',
                'faseCount',
                'totalPrediksi',
                'avgConf',
                'sedangSubur'
            ));

        } catch (\Exception $e) {
            Log::error('PrediksiController@index: ' . $e->getMessage());
            return view('admin.prediksi.index', [
                'prediksi'      => [],
                'faseCount'     => ['folikel'=>0,'ovulasi'=>0,'luteal'=>0,'menstruasi'=>0],
                'totalPrediksi' => 0,
                'avgConf'       => 0,
                'sedangSubur'   => 0,
                'error'         => 'Gagal memuat data prediksi: ' . $e->getMessage()
            ]);
        }
    }
}
