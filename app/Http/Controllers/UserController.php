<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use MongoDB\BSON\ObjectId;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Mendapatkan koneksi database MongoDB
     */
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    /**
     * Mengambil semua data user dari MongoDB
     */
    private function getAllUsers(): array
    {
        $users = [];
        $collection = $this->getDb()->selectCollection('users');

        foreach ($collection->find() as $doc) {
            $users[] = [
                '_id'               => (string) $doc['_id'],                             // ObjectId asli -> dipakai untuk fetch detail
                'user_id'           => $doc['id_user'] ?? (string) $doc['_id'],           // Nomor urut -> hanya untuk label tampilan
                'nama_lengkap'      => $doc['nama_lengkap'] ?? $doc['nama'] ?? '-',
                'email'             => $doc['email'] ?? '-',
                'status'            => ucfirst(strtolower($doc['status'] ?? 'aktif')),
                'age'               => $doc['age'] ?? $doc['umur'] ?? '-',
                'bmi'               => $doc['bmi'] ?? '-',
                'pcos_diagnosed'    => $doc['pcos_diagnosed'] ?? 0,
                'birth_control_use' => $doc['birth_control_use'] ?? 0,
            ];
        }

        return $users;
    }

    /**
     * Menampilkan halaman daftar pengguna
     */
    public function index(Request $request)
    {
        try {
            $allUsers = $this->getAllUsers();

            // Fitur Pencarian
            $search = trim($request->get('search', ''));
            if ($search !== '') {
                $q = strtolower($search);
                $allUsers = array_values(array_filter(
                    $allUsers,
                    fn($u) =>
                        str_contains(strtolower($u['nama_lengkap']), $q) ||
                        str_contains(strtolower($u['email']), $q)
                ));
            }

            // Filter Status
            $status = trim($request->get('status', ''));
            if ($status !== '') {
                $allUsers = array_values(array_filter(
                    $allUsers,
                    fn($u) => strtolower($u['status']) === strtolower($status)
                ));
            }

            // Pagination Manual
            $perPage     = 10;
            $currentPage = max(1, (int) $request->get('page', 1));
            $total       = count($allUsers);
            $totalPages  = max(1, (int) ceil($total / $perPage));
            $pageUsers   = array_slice($allUsers, ($currentPage - 1) * $perPage, $perPage);

            // Statistik Ringkasan
            $stats = [
                'total' => $total,
                'aktif' => count(array_filter($allUsers, fn($u) => strtolower($u['status']) === 'aktif')),
            ];

            return view('admin.pengguna.index', compact(
                'pageUsers', 'stats', 'total', 'totalPages', 'currentPage', 'search'
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

    /**
     * Mengambil detail satu pengguna untuk Modal (via AJAX)
     * Catatan: $id yang diterima di sini HARUS berupa ObjectId MongoDB
     * (24 karakter hex), bukan id_user biasa. Frontend (index.blade.php)
     * sudah diperbaiki agar mengirim $u['_id'], bukan $u['user_id'].
     */
    public function show($id)
    {
        try {
            $collection = $this->getDb()->selectCollection('users');

            // Validasi format ObjectId MongoDB
            if (!preg_match('/^[a-f0-9]{24}$/i', $id)) {
                return response()->json(['error' => 'Format ID tidak valid'], 400);
            }

            $user = $collection->findOne(['_id' => new ObjectId($id)]);

            if (!$user) {
                return response()->json(['error' => 'Pengguna tidak ditemukan'], 404);
            }

            // Kembalikan data JSON
            return response()->json([
                'user_id'           => $user['id_user'] ?? (string) $user['_id'],
                'nama_lengkap'      => $user['nama_lengkap'] ?? $user['nama'] ?? '-',
                'email'             => $user['email'] ?? '-',
                'status'            => ucfirst(strtolower($user['status'] ?? 'aktif')),
                'age'               => $user['age'] ?? $user['umur'] ?? '-',
                'bmi'               => $user['bmi'] ?? '-',
                'pcos_diagnosed'    => $user['pcos_diagnosed'] ?? 0,
                'birth_control_use' => $user['birth_control_use'] ?? 0,
            ]);

        } catch (\Exception $e) {
            Log::error('UserController@show: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat detail: ' . $e->getMessage()], 500);
        }
    }
}