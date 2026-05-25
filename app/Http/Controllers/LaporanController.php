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
                'limit' => 30
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
                'terjadwal'       => 0,
                'total_ukuran_mb' => round(array_sum(array_column($riwayat, 'ukuran_kb')) / 1024, 1) ?: 0,
            ];

            return view('admin.laporan.index', compact('laporan', 'stats'));

        } catch (\Exception $e) {
            Log::error('LaporanController@index: ' . $e->getMessage());
            return view('admin.laporan.index', [
                'laporan' => [],
                'stats'   => ['total' => 0, 'ekspor_hari_ini' => 0, 'terjadwal' => 0, 'total_ukuran_mb' => 0]
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
            $baseName  = str_replace([' ', '&', '-'], '_', strtolower($namaFile)) . '_' . $timestamp;
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
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, array_map('strval', $row));
            }
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
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

    /**
     * Build Data Laporan - Hanya 2 Template
     */
    private function buildReportData($db, string $template, string $dari, string $sampai): array
    {
        $templateLower = strtolower($template);

        // Template 1: Demografis Pengguna
        if (str_contains($templateLower, 'demografis') || str_contains($templateLower, 'pengguna')) {
            $rows = [];
            foreach ($db->selectCollection('users')->find([]) as $doc) {
                $doc = (array) $doc;
                $rows[] = [
                    $doc['id_user'] ?? '',
                    $doc['nama_lengkap'] ?? '',
                    $doc['email'] ?? '',
                    $doc['age'] ?? '',
                    $doc['bmi'] ?? '',
                    $doc['state'] ?? '',
                    $doc['status'] ?? 'Aktif',
                ];
            }
            return [$rows, ['ID User', 'Nama Lengkap', 'Email', 'Usia', 'BMI', 'State', 'Status'], 'Demografis_Pengguna'];
        }

        // Template 2: Log Siklus Menstruasi (Default)
        $rows = [];
        foreach ($db->selectCollection('cycles')->find([]) as $doc) {
            $doc = (array) $doc;
            $tgl = $doc['tanggal_mulai_haid'] ?? '';
            if ($tgl >= $dari && $tgl <= $sampai) {
                $rows[] = [
                    $doc['id_user'] ?? '',
                    $tgl,
                    $doc['tanggal_selesai_haid'] ?? '',
                    $doc['cycle_length_days'] ?? '',
                    $doc['pain_level'] ?? '',
                    $doc['stress_score_cycle'] ?? '',
                    $doc['sleep_hours_cycle'] ?? '',
                    $doc['mood_score'] ?? '',
                    $doc['estrogen_pgml'] ?? '',
                    $doc['progesterone_ngml'] ?? '',
                ];
            }
        }
        return [$rows, ['ID User', 'Tgl Mulai Haid', 'Tgl Selesai Haid', 'Panjang Siklus', 'Pain Level', 'Stress Score', 'Sleep Hours', 'Mood Score', 'Estrogen', 'Progesterone'], 'Log_Siklus_Menstruasi'];
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
