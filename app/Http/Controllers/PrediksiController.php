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

            // ── User map ─────────────────────────────────────────────────────
            $userMap = [];
            foreach ($db->selectCollection('users')->find([]) as $doc) {
                $doc = (array) $doc;
                $userMap[$doc['id_user'] ?? 0] = $doc;
            }

            // ── Cycle map (latest per user) ───────────────────────────────────
            $cycleMap = [];
            foreach ($db->selectCollection('cycles')->find([]) as $doc) {
                $doc = (array) $doc;
                $uid = $doc['id_user'] ?? 0;
                // Simpan yang terbaru berdasarkan tanggal_mulai_haid
                if (!isset($cycleMap[$uid]) ||
                    ($doc['tanggal_mulai_haid'] ?? '') > ($cycleMap[$uid]['tanggal_mulai_haid'] ?? '')) {
                    $cycleMap[$uid] = $doc;
                }
            }

            // ── Prediksi ─────────────────────────────────────────────────────
            $prediksiRaw = [];
            foreach ($db->selectCollection('predictions')->find([], ['sort' => ['created_at' => -1]]) as $doc) {
                $prediksiRaw[] = (array) $doc;
            }

            // Gabungkan dengan data user & cycle untuk blade
            $prediksi = [];
            foreach ($prediksiRaw as $p) {
                $uid    = $p['id_user'] ?? 0;
                $u      = $userMap[$uid]   ?? [];
                $c      = $cycleMap[$uid]  ?? [];

                $prediksi[] = [
                    'id_user'              => $uid,
                    'nama'                 => $u['nama_lengkap']         ?? '-',
                    'usia'                 => $u['age']                  ?? '-',     // field baru
                    'panjang_siklus'       => $c['cycle_length_days']    ?? '-',     // field baru
                    'pattern'              => $p['current_phase']        ?? '-',
                    'ovulasi'              => $p['predicted_ovulation']  ?? '-',
                    'tanggal_mulai_haid'   => $p['predicted_next_period'] ?? '-',
                    'fertile_start'        => $p['fertile_start']        ?? '-',
                    'fertile_end'          => $p['fertile_end']          ?? '-',
                    'confidence_score'     => $p['confidence_score']     ?? '-',
                    'mae_error'            => $p['mae_error']            ?? '-',
                    'cycle_status'         => $p['cycle_status']         ?? '-',

                    // Untuk detail prediksi
                    'predicted_next_period'  => $p['predicted_next_period']  ?? '-',
                    'predicted_ovulation'    => $p['predicted_ovulation']    ?? '-',
                    'predicted_cycle_length' => $p['predicted_cycle_length'] ?? '-',
                ];
            }

            // ── Stats distribusi fase ────────────────────────────────────────
            $faseCount = ['folikel' => 0, 'ovulasi' => 0, 'luteal' => 0, 'menstruasi' => 0];
            foreach ($prediksi as $p) {
                $f = strtolower($p['pattern'] ?? '');
                if (isset($faseCount[$f])) $faseCount[$f]++;
            }

            // ── KPI ──────────────────────────────────────────────────────────
            $totalPrediksi = count($prediksi);
            $normalCount   = count(array_filter($prediksi, fn($p) => ($p['cycle_status'] ?? '') === 'normal'));
            $avgConf       = $totalPrediksi
                ? round(array_sum(array_column($prediksi, 'confidence_score')) / $totalPrediksi, 1)
                : 0;

            // Sedang subur = fase ovulasi hari ini
            $sedangSubur = $faseCount['ovulasi'] ?? 0;

            return view('admin.prediksi.index', compact(
                'prediksi', 'faseCount', 'totalPrediksi', 'avgConf', 'sedangSubur'
            ));

        } catch (\Exception $e) {
            Log::error('PrediksiController@index: ' . $e->getMessage());
            return view('admin.prediksi.index', [
                'prediksi'       => [],
                'faseCount'      => ['folikel' => 0, 'ovulasi' => 0, 'luteal' => 0, 'menstruasi' => 0],
                'totalPrediksi'  => 0,
                'avgConf'        => 0,
                'sedangSubur'    => 0,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
