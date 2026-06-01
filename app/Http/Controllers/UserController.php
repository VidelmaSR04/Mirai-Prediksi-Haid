<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // ----------------------------------------------------------------
    // Koneksi MongoDB
    // ----------------------------------------------------------------
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    // ----------------------------------------------------------------
    // Ambil semua user — hanya field yang ada di MongoDB
    // ----------------------------------------------------------------
    private function getAllUsers(): array
    {
        $users = [];

        foreach ($this->getDb()->selectCollection('users')->find([]) as $doc) {
            $doc = (array) $doc;

            $users[] = [
                'user_id'           => $doc['user_id']           ?? null,
                'nama_lengkap'      => $doc['nama_lengkap']      ?? '-',
                'email'             => $doc['email']             ?? '-',
                'status'            => ucfirst(strtolower($doc['status'] ?? 'aktif')),
                'age'               => $doc['age']               ?? '-',
                'bmi'               => $doc['bmi']               ?? '-',
                'pcos_diagnosed'    => $doc['pcos_diagnosed']    ?? 0,
                'birth_control_use' => $doc['birth_control_use'] ?? 0,
            ];
        }

        return $users;
    }

    // ----------------------------------------------------------------
    // INDEX — daftar pengguna dengan search, filter status, pagination
    // ----------------------------------------------------------------
    public function index(Request $request)
    {
        try {
            $allUsers = $this->getAllUsers();

            // Search by nama atau email
            $search = trim($request->get('search', ''));
            if ($search !== '') {
                $q        = strtolower($search);
                $allUsers = array_values(array_filter(
                    $allUsers,
                    fn($u) =>
                        str_contains(strtolower($u['nama_lengkap']), $q) ||
                        str_contains(strtolower($u['email']), $q)
                ));
            }

            // Filter status
            $status = trim($request->get('status', ''));
            if ($status !== '') {
                $allUsers = array_values(array_filter(
                    $allUsers,
                    fn($u) => strtolower($u['status']) === strtolower($status)
                ));
            }

            // Pagination
            $perPage     = 10;
            $currentPage = max(1, (int) $request->get('page', 1));
            $total       = count($allUsers);
            $totalPages  = max(1, (int) ceil($total / $perPage));
            $pageUsers   = array_slice($allUsers, ($currentPage - 1) * $perPage, $perPage);

            // Stats untuk header / card summary (opsional)
            $stats = [
                'total' => $total,
                'aktif' => count(array_filter(
                    $allUsers,
                    fn($u) => strtolower($u['status']) === 'aktif'
                )),
            ];

            return view('admin.pengguna.index', compact(
                'pageUsers',
                'stats',
                'total',
                'totalPages',
                'currentPage',
                'search'
            ));

        } catch (\Exception $e) {
            Log::error('UserController@index: ' . $e->getMessage());

            return view('admin.pengguna.index', [
                'pageUsers'   => [],
                'stats'       => ['total' => 0, 'aktif' => 0],
                'total'       => 0,
                'totalPages'  => 1,
                'currentPage' => 1,
                'search'      => '',
                'error'       => 'Gagal memuat data pengguna.',
            ]);
        }
    }

    // ----------------------------------------------------------------
    // SHOW — detail satu user untuk modal AJAX
    // field user_id di MongoDB adalah string seperti "U00001"
    // ----------------------------------------------------------------
    public function show($id)
    {
        try {
            $user = null;

            // Cari berdasarkan field user_id (string)
            foreach ($this->getDb()->selectCollection('users')->find(['user_id' => $id]) as $doc) {
                $user = (array) $doc;
                break;
            }

            if (!$user) {
                return response()->json(['error' => 'Pengguna tidak ditemukan.'], 404);
            }

            // Kembalikan hanya 8 field yang ada di MongoDB
            return response()->json([
                'user_id'           => $user['user_id']           ?? null,
                'nama_lengkap'      => $user['nama_lengkap']      ?? '-',
                'email'             => $user['email']             ?? '-',
                'status'            => ucfirst(strtolower($user['status'] ?? 'aktif')),
                'age'               => $user['age']               ?? '-',
                'bmi'               => $user['bmi']               ?? '-',
                'pcos_diagnosed'    => $user['pcos_diagnosed']    ?? 0,
                'birth_control_use' => $user['birth_control_use'] ?? 0,
            ]);

        } catch (\Exception $e) {
            Log::error('UserController@show: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat detail: ' . $e->getMessage()], 500);
        }
    }
}
