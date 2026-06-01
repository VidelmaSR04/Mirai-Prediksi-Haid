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

            $doc['usia']   = $doc['age'] ?? '-';
            $doc['bmi']    = $doc['bmi'] ?? '-';
            $doc['status'] = ucfirst(strtolower($doc['status'] ?? 'aktif'));

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

            $stats = [
                'total' => $total,
                'aktif' => count(array_filter($allUsers, fn($u) => strtolower($u['status'] ?? '') === 'aktif')),
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
                'error'       => 'Gagal memuat data',
            ]);
        }
    }

    // Untuk Modal (AJAX) — id_user adalah STRING seperti "U00001"
    public function show($id)
    {
        try {
            $db   = $this->getDb();
            $user = null;

            // Query pakai string, bukan (int)$id
            foreach ($db->selectCollection('users')->find(['id_user' => $id]) as $doc) {
                $user = (array) $doc;
                break;
            }

            if (!$user) {
                return response()->json(['error' => 'Pengguna tidak ditemukan'], 404);
            }

            // Normalisasi
            $user['usia']   = $user['age'] ?? '-';
            $user['bmi']    = $user['bmi'] ?? '-';
            $user['status'] = ucfirst(strtolower($user['status'] ?? 'aktif'));

            return response()->json($user);

        } catch (\Exception $e) {
            Log::error('UserController@show: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat detail: ' . $e->getMessage()], 500);
        }
    }
}
