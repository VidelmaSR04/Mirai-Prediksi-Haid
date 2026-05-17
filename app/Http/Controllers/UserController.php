<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    private function getAllUsers(): array
    {
        $users = [];
        foreach ($this->getDb()->selectCollection('users')->find([]) as $doc) {
            $doc = (array) $doc;

            // Normalisasi field DB → field yang dipakai Blade
            $doc['usia']         = $doc['age'] ?? '-';
            $doc['berat_badan']  = $doc['weight_kg'] ?? '-';
            $doc['tinggi_badan'] = $doc['height_cm'] ?? '-';
            $doc['bmi']          = $doc['bmi'] ?? '-';
            $doc['status']       = ucfirst(strtolower($doc['status'] ?? 'Aktif')); // Pastikan konsisten

            // Semua field detail kesehatan
            $fields = [
                'sleep_hours', 'exercise_frequency', 'stress_score_baseline',
                'diet_quality', 'water_intake_l', 'caffeine_cups_day',
                'pcos_diagnosed', 'birth_control_use', 'smoking_status',
                'alcohol_consumption'
            ];
            foreach ($fields as $f) {
                $doc[$f] = $doc[$f] ?? '-';
            }

            $users[] = $doc;
        }
        return $users;
    }

    public function index(Request $request)
    {
        try {
            $allUsers = $this->getAllUsers();

            // Search
            // Search
        $search = trim($request->get('search', ''));
           if ($search) {
        $q = strtolower($search);
        $allUsers = array_values(array_filter($allUsers, fn($u) =>
        str_contains(strtolower($u['nama_lengkap'] ?? ''), $q) ||
        str_contains(strtolower($u['email'] ?? ''), $q)
        ));
    }

        // Filter status
        $status = trim($request->get('status', ''));
           if ($status) {
        $allUsers = array_values(array_filter($allUsers, fn($u) =>
        strtolower($u['status'] ?? '') === strtolower($status)
     ));
   }

            // Pagination
            $perPage     = 10;
            $currentPage = max(1, (int) $request->get('page', 1));
            $total       = count($allUsers);
            $totalPages  = max(1, (int) ceil($total / $perPage));
            $pageUsers   = array_slice($allUsers, ($currentPage - 1) * $perPage, $perPage);

            // Stats
            $allForStats = $this->getAllUsers();
            $stats = [
                'total' => count($allForStats),
                'aktif' => count(array_filter($allForStats, fn($u) => strtolower($u['status'] ?? '') === 'aktif')),
            ];

            return view('admin.pengguna.index', compact(
                'pageUsers', 'stats', 'total', 'totalPages', 'currentPage',
                'search'
            ));

        } catch (\Exception $e) {
            Log::error('UserController@index: ' . $e->getMessage());
            return view('admin.pengguna.index', [
                'pageUsers' => [], 'stats' => ['total' => 0, 'aktif' => 0],
                'total' => 0, 'totalPages' => 1, 'currentPage' => 1, 'search' => '',
                'error' => 'Gagal memuat data'
            ]);
        }
    }

    public function show($id)
    {
        try {
            $db = $this->getDb();
            $user = null;

            foreach ($db->selectCollection('users')->find(['id_user' => (int)$id]) as $doc) {
                $user = (array) $doc;
                break;
            }

            if (!$user) {
                return back()->with('error', 'Pengguna tidak ditemukan.');
            }

            // Normalisasi semua field
            $user['usia']         = $user['age'] ?? '-';
            $user['berat_badan']  = $user['weight_kg'] ?? '-';
            $user['tinggi_badan'] = $user['height_cm'] ?? '-';
            $user['bmi']          = $user['bmi'] ?? '-';
            $user['status']       = ucfirst(strtolower($user['status'] ?? 'Aktif'));

            return view('admin.pengguna.show', compact('user'));

        } catch (\Exception $e) {
            Log::error('UserController@show: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail.');
        }
    }

}
