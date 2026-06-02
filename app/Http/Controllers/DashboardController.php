<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    // Hasil evaluasi model Linear Regression
    // Sumber: perbandingan algoritma Python
    private const MODEL_MAE  = 1.72;
    private const MODEL_RMSE = 2.21;
    private const MODEL_R2   = 0.1654;

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

            // 1. Total Data Siklus
            $totalSiklus = $cyclesCol->countDocuments();

            // 2. Rata-rata Siklus
            $avgResult = $cyclesCol->aggregate([
                ['$match' => ['cycle_length_days' => ['$gt' => 0]]],
                ['$group' => ['_id' => null, 'avg' => ['$avg' => '$cycle_length_days']]]
            ])->toArray();

            $rataSiklus = !empty($avgResult)
                ? round($avgResult[0]['avg'] ?? 0, 1)
                : 0;

            // 3. Persentase Siklus Normal (21-35 hari)
            $normalCount = $cyclesCol->countDocuments([
                'cycle_length_days' => ['$gte' => 21, '$lte' => 35]
            ]);

            $persenNormal = $totalSiklus > 0
                ? round(($normalCount / $totalSiklus) * 100, 1)
                : 0;

            // 4. Metrik Model — dari hasil evaluasi Python (Linear Regression)
            $stats = [
                'rata_siklus'   => $rataSiklus,
                'persen_normal' => $persenNormal,
                'total_siklus'  => $totalSiklus,
                'mae'           => self::MODEL_MAE,
                'rmse'          => self::MODEL_RMSE,
                'r2'            => self::MODEL_R2,
                'model_name'    => 'Linear Regression',
            ];

            return view('admin.dashboard.index', compact('stats'));

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());

            return view('admin.dashboard.index', [
                'stats' => [
                    'rata_siklus'   => 0,
                    'persen_normal' => 0,
                    'total_siklus'  => 0,
                    'mae'           => self::MODEL_MAE,
                    'rmse'          => self::MODEL_RMSE,
                    'r2'            => self::MODEL_R2,
                    'model_name'    => 'Linear Regression',
                ],
                'error' => 'Gagal memuat data siklus: ' . $e->getMessage()
            ]);
        }
    }
}