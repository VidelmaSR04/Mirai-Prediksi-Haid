<?php

namespace App\Http\Controllers;

use MongoDB\Client;

class NotifikasiController extends Controller
{
    private function getDb()
    {
        $client = new Client("mongodb://127.0.0.1:27017");
        return $client->selectDatabase("mirai");
    }

    public function getData()
    {
        $db = $this->getDb();

        $userBaru = iterator_to_array(
            $db->users->find([], [
                'sort' => ['_id' => -1],
                'limit' => 5
            ])
        );

        $siklusBaru = iterator_to_array(
            $db->cycles->find([], [
                'sort' => ['_id' => -1],
                'limit' => 5
            ])
        );

        $notifikasi = [];

        foreach ($userBaru as $u) {
            $u = (array) $u;

            $notifikasi[] = [
                'tipe' => 'user_baru',
                'pesan' => 'User baru: ' . ($u['nama_lengkap'] ?? 'Unknown'),
                'icon' => 'person_add'
            ];
        }

        foreach ($siklusBaru as $s) {
            $s = (array) $s;

            $notifikasi[] = [
                'tipe' => 'siklus_baru',
                'pesan' => 'Data siklus baru',
                'icon' => 'calendar_month'
            ];
        }

        return response()->json([
            'total' => count($notifikasi),
            'data' => $notifikasi
        ]);
    }
}