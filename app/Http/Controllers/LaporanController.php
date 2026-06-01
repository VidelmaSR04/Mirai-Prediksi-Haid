<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    public function index()
    {
        try {
            $db         = $this->getDb();
            $riwayatCol = $db->selectCollection('laporan_exports');

            $riwayat = $riwayatCol->find([], [
                'sort'  => ['created_at' => -1],
                'limit' => 30,
            ])->toArray();

            $laporan = array_map(function ($r) {
                $r = (array) $r;
                return [
                    'id'     => (string) ($r['_id'] ?? ''),
                    'nama'   => $r['nama'] ?? '-',
                    'format' => $r['format'] ?? 'CSV',
                    'oleh'   => $r['dibuat_oleh'] ?? '-',
                    'waktu'  => $r['created_at'] instanceof UTCDateTime
                        ? $r['created_at']->toDateTime()->format('d M Y H:i')
                        : ($r['created_at'] ?? '-'),
                ];
            }, $riwayat);

            $stats = [
                'total'           => $riwayatCol->countDocuments(),
                'ekspor_hari_ini' => $riwayatCol->countDocuments([
                    'created_at' => ['$gte' => new UTCDateTime(now()->startOfDay()->getTimestamp() * 1000)],
                ]),
            ];

            return view('admin.laporan.index', compact('laporan', 'stats'));

        } catch (\Exception $e) {
            Log::error('LaporanController@index: ' . $e->getMessage());
            return view('admin.laporan.index', [
                'laporan' => [],
                'stats'   => ['total' => 0, 'ekspor_hari_ini' => 0],
            ]);
        }
    }

    public function generate(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'format'   => 'required|in:CSV',
            'dari'     => 'required|date',
            'sampai'   => 'required|date|after_or_equal:dari',
        ]);

        try {
            $db       = $this->getDb();
            $admin    = auth('admin')->user();
            $template = $request->template;
            $dari     = $request->dari;
            $sampai   = $request->sampai;

            [$rows, $headers, $namaFile] = $this->buildReportData($db, $template, $dari, $sampai);

            $timestamp = now()->format('Ymd_His');
            $baseName  = str_replace([' ', '&', '-', '/', '+'], '_', strtolower($namaFile)) . '_' . $timestamp;
            $filename  = $baseName . '.csv';

            $ukuranKb = $this->estimateSize($rows, $headers);

            $db->selectCollection('laporan_exports')->insertOne([
                'nama'            => $baseName,
                'template'        => $template,
                'format'          => 'CSV',
                'ukuran_kb'       => $ukuranKb,
                'dibuat_oleh'     => $admin->name ?? 'Admin',
                'status'          => 'Selesai',
                'created_at'      => new UTCDateTime(now()->getTimestamp() * 1000),
                'tanggal_mulai'   => $dari,
                'tanggal_selesai' => $sampai,
                'jumlah_baris'    => count($rows),
            ]);

            return $this->downloadCsv($rows, $headers, $filename);

        } catch (\Exception $e) {
            Log::error('LaporanController@generate: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    private function downloadCsv(array $rows, array $headers, string $filename)
    {
        return response()->stream(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF"); // BOM UTF-8 agar Excel tidak acak karakter
            fputcsv($out, $headers, ',');
            foreach ($rows as $row) {
                fputcsv($out, array_map('strval', $row), ',');
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
        ]);
    }

    private function estimateSize(array $rows, array $headers): float
    {
        $content = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $content .= implode(',', array_map('strval', $row)) . "\n";
        }
        return round(strlen($content) / 1024, 2);
    }

    private function buildReportData($db, string $template, string $dari, string $sampai): array
    {
        $t = strtolower($template);

        // ══════════════════════════════════════════════════════
        // TEMPLATE 1: Profil & Demografi Pengguna
        // Collection: users
        // Field sesuai struktur MongoDB terbaru
        // ══════════════════════════════════════════════════════
        if (str_contains($t, 'demografi') || str_contains($t, 'profil')) {
            $rows = [];
            foreach ($db->selectCollection('users')->find([]) as $doc) {
                $doc = (array) $doc;
                $rows[] = [
                    $doc['user_id']            ?? '',
                    $doc['nama_lengkap']       ?? '',
                    $doc['email']              ?? '',
                    $doc['age']                ?? '',
                    $doc['bmi']                ?? '',
                    isset($doc['pcos_diagnosed'])    ? ($doc['pcos_diagnosed'] == 1    ? 'Ya' : 'Tidak') : '',
                    isset($doc['birth_control_use']) ? ($doc['birth_control_use'] == 1 ? 'Ya' : 'Tidak') : '',
                    ucfirst(strtolower($doc['status'] ?? 'aktif')),
                ];
            }
            $headers = [
                'ID User', 'Nama Lengkap', 'Email',
                'Usia', 'BMI',
                'PCOS', 'Kontrasepsi',
                'Status',
            ];
            return [$rows, $headers, 'Profil_Demografi_Pengguna'];
        }

        // ══════════════════════════════════════════════════════
        // TEMPLATE 2: Riwayat Siklus Menstruasi
        // Collection: cycles
        // Field sesuai struktur MongoDB terbaru
        // Tidak ada filter tanggal karena kolom tanggal tidak tersedia
        // ══════════════════════════════════════════════════════
        if (str_contains($t, 'siklus') || str_contains($t, 'menstruasi') || str_contains($t, 'riwayat')) {
            $rows = [];

            foreach ($db->selectCollection('cycles')->find([]) as $doc) {
                $doc = (array) $doc;

                $rows[] = [
                    $doc['user_id']           ?? '',
                    $doc['cycle_length_days'] ?? '',
                    $doc['prev_cycle_length'] ?? '',
                    $doc['pain_level']        ?? '',
                    $doc['stress_score_cycle']?? '',
                    $doc['sleep_hours_cycle'] ?? '',
                    $doc['mood_score']        ?? '',
                ];
            }

            $headers = [
                'ID User',
                'Panjang Siklus (hari)', 'Panjang Siklus Sebelumnya',
                'Pain Level', 'Stress Score', 'Sleep Hours',
                'Mood Score',
            ];
            return [$rows, $headers, 'Riwayat_Siklus_Menstruasi'];
        }

        // ══════════════════════════════════════════════════════
        // TEMPLATE 3: Ringkasan Prediksi AI
        // Collection: predictions
        // Header dibangun DINAMIS dari dokumen pertama
        // ══════════════════════════════════════════════════════
        $rows        = [];
        $headers     = [];
        $headerBuilt = false;

        foreach ($db->selectCollection('predictions')->find([]) as $doc) {
            $doc = (array) $doc;

            // Buang _id (ObjectId tidak bisa di-strval)
            unset($doc['_id']);

            // Bangun header dari dokumen pertama yang tidak kosong
            if (!$headerBuilt && !empty($doc)) {
                $headers     = array_keys($doc);
                $headerBuilt = true;
            }

            if (!$headerBuilt) continue;

            $row = [];
            foreach ($headers as $key) {
                $val = $doc[$key] ?? '';
                if (is_array($val) || is_object($val)) {
                    $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                }
                $row[] = (string) $val;
            }
            $rows[] = $row;
        }

        if (empty($headers)) {
            $headers = ['Tidak ada data prediksi'];
        }

        // Ubah snake_case ke label yang lebih ramah baca
        $headersLabel = array_map(fn($h) => ucwords(str_replace('_', ' ', $h)), $headers);

        return [$rows, $headersLabel, 'Ringkasan_Prediksi_AI'];
    }

    public function destroy(string $id)
    {
        try {
            if (!preg_match('/^[a-f\d]{24}$/i', $id)) {
                return back()->with('error', 'ID laporan tidak valid.');
            }

            $this->getDb()->selectCollection('laporan_exports')
                ->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            return back()->with('success', 'Laporan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('LaporanController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus laporan.');
        }
    }
}
