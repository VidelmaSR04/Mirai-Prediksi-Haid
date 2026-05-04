<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
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
            $cyclesCol      = $db->selectCollection('cycles');
            $predictionsCol = $db->selectCollection('predictions');

            // 1. Total Data Siklus
            $totalSiklus = $cyclesCol->countDocuments();

            // 2. Rata-rata Siklus
            $avgResult = $cyclesCol->aggregate([
                ['$match' => ['cycle_length_days' => ['$gt' => 0]]],
                ['$group' => ['_id' => null, 'avg' => ['$avg' => '$cycle_length_days']]]
            ])->toArray();

            $rataSiklus = !empty($avgResult) ? round($avgResult[0]['avg'] ?? 0, 1) : 0;

            // 3. Persentase Siklus Normal (21-35 hari)
            $normalCount = $cyclesCol->countDocuments([
                'cycle_length_days' => ['$gte' => 21, '$lte' => 35]
            ]);

            $persenNormal = $totalSiklus > 0
                ? round(($normalCount / $totalSiklus) * 100, 1)
                : 0;

            // 4. Metrik Model Terbaru (aman dari null)
            $latestPrediction = $predictionsCol->findOne(
                [],
                ['sort' => ['created_at' => -1]]
            );

            $stats = [
                'rata_siklus'   => $rataSiklus,
                'persen_normal' => $persenNormal,
                'total_siklus'  => $totalSiklus,
                'mae'           => 0,
                'rmse'          => 0,
                'r2'            => 0,
            ];

            // Isi data model jika ada dokumen
            if ($latestPrediction) {
                $stats['mae']  = round($latestPrediction['mae_error']   ??
                                     $latestPrediction['mae']          ?? 0, 2);

                $stats['rmse'] = round($latestPrediction['rmse_error']  ??
                                     $latestPrediction['prediction_error'] ?? 0, 2);

                $stats['r2']   = round($latestPrediction['r2_score']    ??
                                     $latestPrediction['confidence_score'] ?? 0, 2);
            }

            return view('admin.dashboard.index', compact('stats'));

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());

            return view('admin.dashboard.index', [
                'stats' => [
                    'rata_siklus'   => 0,
                    'persen_normal' => 0,
                    'total_siklus'  => 0,
                    'mae'           => 0,
                    'rmse'          => 0,
                    'r2'            => 0
                ],
                'error' => 'Gagal memuat dashboard: ' . $e->getMessage()
            ]);
        }
    }
}
