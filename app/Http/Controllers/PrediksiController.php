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

            $userMap = [];
            foreach ($db->selectCollection('users')->find([]) as $doc) {
                $doc = (array) $doc;
                $uid = (string) ($doc['id_user'] ?? '');
                if ($uid !== '') $userMap[$uid] = $doc;
            }

            $cycleMap = [];
            foreach ($db->selectCollection('cycles')->find([]) as $doc) {
                $doc = (array) $doc;
                $uid = (string) ($doc['id_user'] ?? '');
                if (!$uid) continue;
                $currentDate  = strtotime($doc['tanggal_mulai_haid'] ?? '0');
                $existingDate = isset($cycleMap[$uid])
                    ? strtotime($cycleMap[$uid]['tanggal_mulai_haid'] ?? '0')
                    : 0;
                if (!isset($cycleMap[$uid]) || $currentDate > $existingDate) {
                    $cycleMap[$uid] = $doc;
                }
            }

            $seenUsers   = [];
            $prediksiRaw = [];
            foreach ($db->selectCollection('predictions')->find([], ['sort' => ['created_at' => -1]]) as $doc) {
                $doc = (array) $doc;
                $uid = (string) ($doc['id_user'] ?? '');
                if (!$uid || isset($seenUsers[$uid])) continue;
                $seenUsers[$uid] = true;
                $prediksiRaw[]   = $doc;
            }

            $prediksi = [];
            foreach ($prediksiRaw as $p) {
                $uid = (string) ($p['id_user'] ?? '');
                if (!$uid) continue;
                $u = $userMap[$uid] ?? [];
                $c = $cycleMap[$uid] ?? [];
                $prediksi[] = [
                    'id_user'                => $uid,
                    'nama'                   => $u['nama_lengkap']           ?? 'User ' . $uid,
                    'usia'                   => $u['age']                    ?? '-',
                    'panjang_siklus'         => $c['cycle_length_days']      ?? $p['cycle_length_used'] ?? '-',
                    'pattern'                => $p['current_phase']          ?? '-',
                    'ovulasi'                => $p['predicted_ovulation']    ?? '-',
                    'tanggal_mulai_haid'     => $p['predicted_next_period']  ?? '-',
                    'fertile_start'          => $p['fertile_start']          ?? '-',
                    'fertile_end'            => $p['fertile_end']            ?? '-',
                    'confidence_score'       => is_numeric($p['confidence_score'] ?? null)
                                                ? (int) $p['confidence_score'] : '-',
                    'mae_error'              => $p['mae_error']              ?? '-',
                    'cycle_status'           => $p['cycle_status']           ?? '-',
                    'predicted_next_period'  => $p['predicted_next_period']  ?? '-',
                    'predicted_ovulation'    => $p['predicted_ovulation']    ?? '-',
                    'predicted_cycle_length' => $p['predicted_cycle_length'] ?? '-',
                ];
            }

            $faseCount = ['folikel' => 0, 'ovulasi' => 0, 'luteal' => 0, 'menstruasi' => 0];
            foreach ($prediksi as $p) {
                $f = strtolower(trim($p['pattern'] ?? ''));
                if (array_key_exists($f, $faseCount)) $faseCount[$f]++;
            }

            $totalPrediksi = count($prediksi);
            $confValues = array_filter(
                array_column($prediksi, 'confidence_score'),
                fn($v) => is_numeric($v)
            );
            $avgConf = count($confValues)
                ? round(array_sum($confValues) / count($confValues), 1)
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
                'faseCount'     => ['folikel' => 0, 'ovulasi' => 0, 'luteal' => 0, 'menstruasi' => 0],
                'totalPrediksi' => 0,
                'avgConf'       => 0,
                'sedangSubur'   => 0,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
