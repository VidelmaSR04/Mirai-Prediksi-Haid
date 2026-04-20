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

    /** Ambil semua users — struktur baru: 1 doc = 1 user */
    private function getAllUsers(): array
    {
        $users = [];
        foreach ($this->getDb()->selectCollection('users')->find([]) as $doc) {
            $doc = (array) $doc;
            // Normalisasi field baru → alias lama agar blade tetap jalan
            $doc['usia']         = $doc['age']       ?? '-';
            $doc['berat_badan']  = $doc['weight_kg'] ?? '-';
            $doc['tinggi_badan'] = $doc['height_cm'] ?? '-';
            $doc['status']       = $doc['status']    ?? 'Aktif';
            // Field detail
            $doc['sleep_hours']           = $doc['sleep_hours']           ?? '-';
            $doc['exercise_frequency']    = $doc['exercise_frequency']    ?? '-';
            $doc['stress_score_baseline'] = $doc['stress_score_baseline'] ?? '-';
            $doc['diet_quality']          = $doc['diet_quality']          ?? '-';
            $doc['water_intake_l']        = $doc['water_intake_l']        ?? '-';
            $doc['caffeine_cups_day']     = $doc['caffeine_cups_day']     ?? '-';
            $doc['pcos_diagnosed']        = $doc['pcos_diagnosed']        ?? '-';
            $doc['birth_control_use']     = $doc['birth_control_use']     ?? '-';
            $doc['smoking_status']        = $doc['smoking_status']        ?? '-';
            $doc['alcohol_consumption']   = $doc['alcohol_consumption']   ?? '-';

            $users[] = $doc;
        }
        return $users;
    }

    public function index(Request $request)
    {
        try {
            $allUsers = $this->getAllUsers();

            // Search
            $search = trim($request->get('search', ''));
            if ($search) {
                $q        = strtolower($search);
                $allUsers = array_values(array_filter($allUsers,
                    fn($u) => str_contains(strtolower($u['nama_lengkap'] ?? ''), $q)
                           || str_contains(strtolower($u['email'] ?? ''), $q)
                ));
            }

            // Filter status
            $statusFilter = $request->get('status', '');
            if ($statusFilter) {
                $allUsers = array_values(array_filter($allUsers,
                    fn($u) => ($u['status'] ?? 'Aktif') === $statusFilter
                ));
            }

            // Sort
            $sort = $request->get('sort', '');
            match ($sort) {
                'nama'      => usort($allUsers, fn($a, $b) => strcmp($a['nama_lengkap'] ?? '', $b['nama_lengkap'] ?? '')),
                'nama_desc' => usort($allUsers, fn($a, $b) => strcmp($b['nama_lengkap'] ?? '', $a['nama_lengkap'] ?? '')),
                'usia'      => usort($allUsers, fn($a, $b) => (int)($a['age'] ?? 0) - (int)($b['age'] ?? 0)),
                default     => null,
            };

            // Stats — dari data asli tanpa filter
            $allForStats = $this->getAllUsers();
            $stats = [
                'total'    => count($allForStats),
                'aktif'    => count(array_filter($allForStats, fn($u) => ($u['status'] ?? 'Aktif') === 'Aktif')),
                'menunggu' => count(array_filter($allForStats, fn($u) => ($u['status'] ?? '') === 'Menunggu')),
                'nonaktif' => count(array_filter($allForStats, fn($u) => ($u['status'] ?? '') === 'Nonaktif')),
            ];

            // Pagination
            $perPage     = 10;
            $currentPage = max(1, (int) $request->get('page', 1));
            $total       = count($allUsers);
            $totalPages  = max(1, (int) ceil($total / $perPage));
            $pageUsers   = array_slice($allUsers, ($currentPage - 1) * $perPage, $perPage);

            return view('admin.pengguna.index', compact(
                'pageUsers', 'stats', 'total', 'totalPages',
                'currentPage', 'perPage', 'search', 'statusFilter', 'sort'
            ));

        } catch (\Exception $e) {
            Log::error('UserController@index: ' . $e->getMessage());
            return view('admin.pengguna.index', [
                'pageUsers'    => [], 'stats' => ['total' => 0, 'aktif' => 0, 'menunggu' => 0, 'nonaktif' => 0],
                'total'        => 0, 'totalPages' => 1, 'currentPage' => 1,
                'perPage'      => 10, 'search' => '', 'statusFilter' => '', 'sort' => '',
                'error'        => 'Gagal memuat data: ' . $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        try {
            $db   = $this->getDb();
            $user = null;

            // Cari user berdasarkan id_user
            foreach ($db->selectCollection('users')->find(['id_user' => (int) $id]) as $doc) {
                $doc             = (array) $doc;
                // Normalisasi alias
                $doc['usia']         = $doc['age']       ?? '-';
                $doc['berat_badan']  = $doc['weight_kg'] ?? '-';
                $doc['tinggi_badan'] = $doc['height_cm'] ?? '-';
                $user = $doc;
                break;
            }

            if (!$user) {
                return back()->with('error', 'Pengguna tidak ditemukan.');
            }

            // Riwayat siklus — query langsung by id_user
            $userCycles = [];
            foreach ($db->selectCollection('cycles')->find(['id_user' => (int) $id]) as $doc) {
                $doc = (array) $doc;
                // Normalisasi field baru ke alias blade lama
                $doc['panjang_siklus']     = $doc['cycle_length_days']   ?? 0;
                $doc['cycle_length_days']  = $doc['cycle_length_days']   ?? 0;
                $doc['stress_score_cycle'] = $doc['stress_score_cycle']  ?? '-';
                $doc['sleep_hours_cycle']  = $doc['sleep_hours_cycle']   ?? '-';
                $userCycles[] = $doc;
            }

            // Prediksi dari collection predictions
            $predictions = [];
            foreach ($db->selectCollection('predictions')->find(
                ['id_user' => (int) $id],
                ['sort' => ['created_at' => -1], 'limit' => 5]
            ) as $doc) {
                $predictions[] = (array) $doc;
            }

            return view('admin.pengguna.show', compact('user', 'userCycles', 'predictions'));

        } catch (\Exception $e) {
            Log::error('UserController@show: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail pengguna.');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Aktif,Menunggu,Nonaktif']);
        try {
            $this->getDb()->selectCollection('users')->updateOne(
                ['id_user' => (int) $id],
                ['$set'    => ['status' => $request->status]]
            );
            return back()->with('success', 'Status berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('UserController@updateStatus: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui status.');
        }
    }

    public function exportCsv()
    {
        $users    = $this->getAllUsers();
        $filename = 'mirai_pengguna_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($users) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Nama Lengkap', 'Email', 'No. Telepon', 'Usia', 'BMI', 'Status']);
            foreach ($users as $u) {
                fputcsv($out, [
                    $u['id_user']      ?? '-',
                    $u['nama_lengkap'] ?? '-',
                    $u['email']        ?? '-',
                    $u['no_telepon']   ?? '-',
                    $u['age']          ?? '-',   // field baru
                    $u['bmi']          ?? '-',
                    $u['status']       ?? 'Aktif',
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
