<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    /**
     * Koneksi ke MongoDB
     */
    private function getDb()
    {
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        return $client->selectDatabase(env('MONGODB_DATABASE', 'mirai'));
    }

    /**
     * Menampilkan Halaman Laporan Utama
     * Route: /admin/laporan
     */
    public function index()
    {
        try {
            $db         = $this->getDb();
            $riwayatCol = $db->selectCollection('laporan_exports');

            // Ambil riwayat ekspor, diurutkan dari yang terbaru
            $riwayat = $riwayatCol->find(
                [],
                [
                    'sort'  => ['created_at' => -1],
                    'limit' => 50
                ]
            )->toArray();

            // Format data untuk ditampilkan di Blade
            $laporan = array_map(function ($r) {
                $r = (array) $r;
                return [
                    'id'     => (string) ($r['_id'] ?? ''),
                    'nama'   => $r['nama'] ?? '-',
                    'format' => $r['format'] ?? '-',
                    'oleh'   => $r['dibuat_oleh'] ?? '-',
                    'waktu'  => $r['created_at'] instanceof UTCDateTime
                        ? $r['created_at']->toDateTime()->format('d M Y H:i')
                        : ($r['created_at'] ?? '-'),
                ];
            }, $riwayat);

            // Hitung statistik
            $eksporHariIni = $riwayatCol->countDocuments([
                'created_at' => ['$gte' => new UTCDateTime(now()->startOfDay()->getTimestamp() * 1000)],
            ]);

            $totalUkuranKb = array_sum(array_column($riwayat, 'ukuran_kb'));

            $stats = [
                'total'           => $riwayatCol->countDocuments(),
                'ekspor_hari_ini' => $eksporHariIni,
                'terjadwal'       => 2,                    // TODO: bisa dibuat dinamis nanti
                'total_ukuran_mb' => round($totalUkuranKb / 1024, 1) ?: 0,
            ];

            return view('admin.laporan.index', compact('laporan', 'stats'));

        } catch (\Exception $e) {
            Log::error('LaporanController@index: ' . $e->getMessage());

            // Kirim data kosong agar halaman tidak error
            return view('admin.laporan.index', [
                'laporan' => [],
                'stats'   => [
                    'total'           => 0,
                    'ekspor_hari_ini' => 0,
                    'terjadwal'       => 0,
                    'total_ukuran_mb' => 0,
                ],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate & Download Laporan
     * Dipanggil dari form di halaman laporan
     */
    public function generate(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'format'   => 'required|in:CSV,XLSX,PDF',
            'dari'     => 'required|date',
            'sampai'   => 'required|date|after_or_equal:dari',
        ]);

        try {
            $db       = $this->getDb();
            $admin    = auth('admin')->user();
            $template = $request->template;
            $format   = $request->format;
            $dari     = $request->dari;
            $sampai   = $request->sampai;

            // Bangun data laporan sesuai template yang dipilih
            [$rows, $headers, $namaFile] = $this->buildReportData($db, $template, $dari, $sampai);

            // Hitung estimasi ukuran file
            $csvContent = implode(',', $headers) . "\n";
            foreach ($rows as $row) {
                $csvContent .= implode(',', array_map('strval', $row)) . "\n";
            }
            $ukuranKb = round(strlen($csvContent) / 1024, 2);

            // Simpan riwayat ke MongoDB
            $db->selectCollection('laporan_exports')->insertOne([
                'nama'            => $namaFile . '_' . now()->format('YmdHis'),
                'template'        => $template,
                'format'          => $format,
                'ukuran_kb'       => $ukuranKb,
                'dibuat_oleh'     => $admin->name ?? 'Admin',
                'status'          => 'Selesai',
                'created_at'      => new UTCDateTime(now()->getTimestamp() * 1000),
                'tanggal_mulai'   => $dari,
                'tanggal_selesai' => $sampai,
                'jumlah_baris'    => count($rows),
            ]);

            // Download file CSV (saat ini hanya support CSV)
            $filename = $namaFile . '_' . now()->format('Ymd') . '.csv';

            return response()->stream(function () use ($headers, $rows) {
                $out = fopen('php://output', 'w');
                fputs($out, "\xEF\xBB\xBF"); // BOM untuk Excel agar karakter tidak rusak
                fputcsv($out, $headers);
                foreach ($rows as $row) {
                    fputcsv($out, $row);
                }
                fclose($out);
            }, 200, [
                'Content-Type'        => 'text/csv;charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);

        } catch (\Exception $e) {
            Log::error('LaporanController@generate: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Membuat data laporan berdasarkan template yang dipilih
     */
    private function buildReportData($db, string $template, string $dari, string $sampai): array
    {
        $key = match (true) {
            str_contains(strtolower($template), 'pengguna')   => 'pengguna',
            str_contains(strtolower($template), 'siklus')     => 'siklus',
            str_contains(strtolower($template), 'prediksi')   => 'prediksi',
            str_contains(strtolower($template), 'performa')   => 'prediksi',
            default                                           => 'bulanan',
        };

        switch ($key) {

            case 'pengguna':
                // ... (kode existing)
                $rows = [];
                foreach ($db->selectCollection('users')->find([]) as $doc) {
                    $doc = (array) $doc;
                    $rows[] = [
                        $doc['id_user'] ?? '',
                        $doc['nama_lengkap'] ?? '',
                        $doc['email'] ?? '',
                        $doc['no_telepon'] ?? '',
                        $doc['age'] ?? '',
                        $doc['bmi'] ?? '',
                        $doc['status'] ?? 'Aktif',
                    ];
                }
                return [$rows, ['ID User', 'Nama Lengkap', 'Email', 'No. Telepon', 'Usia', 'BMI', 'Status'], 'Laporan_Pengguna'];

            case 'siklus':
                // ... (kode existing)
                $rows = [];
                foreach ($db->selectCollection('cycles')->find([]) as $doc) {
                    $doc = (array) $doc;
                    $tgl = $doc['tanggal_mulai_haid'] ?? '';
                    if ($tgl >= $dari && $tgl <= $sampai) {
                        $rows[] = [
                            $doc['id_user'] ?? '',
                            $doc['tanggal_mulai_haid'] ?? '',
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
                return [
                    $rows,
                    ['ID User', 'Tgl Mulai', 'Tgl Selesai', 'Panjang Siklus', 'Pain', 'Stress', 'Tidur', 'Mood', 'Estrogen', 'Progesteron'],
                    'Laporan_Siklus'
                ];

            case 'prediksi':
                // ... (kode existing)
                $rows = [];
                foreach ($db->selectCollection('predictions')->find([]) as $doc) {
                    $doc = (array) $doc;
                    $tgl = $doc['last_period_date'] ?? '';
                    if ($tgl >= $dari && $tgl <= $sampai) {
                        $rows[] = [
                            $doc['id_user'] ?? '',
                            $doc['predicted_cycle_length'] ?? '',
                            $doc['predicted_next_period'] ?? '',
                            $doc['predicted_ovulation'] ?? '',
                            $doc['fertile_start'] ?? '',
                            $doc['fertile_end'] ?? '',
                            $doc['cycle_status'] ?? '',
                            $doc['confidence_score'] ?? '',
                            $doc['mae_error'] ?? '',
                        ];
                    }
                }
                return [
                    $rows,
                    ['ID User', 'Prediksi Siklus (hari)', 'Prediksi Haid', 'Prediksi Ovulasi', 'Fertile Start', 'Fertile End', 'Status', 'Confidence', 'MAE'],
                    'Laporan_Prediksi'
                ];

            default: // bulanan
                // ... (kode existing)
                $userMap = [];
                foreach ($db->selectCollection('users')->find([]) as $doc) {
                    $doc = (array) $doc;
                    $userMap[$doc['id_user'] ?? 0] = $doc;
                }

                $rows = [];
                foreach ($db->selectCollection('cycles')->find([]) as $doc) {
                    $doc = (array) $doc;
                    $tgl = $doc['tanggal_mulai_haid'] ?? '';
                    if ($tgl >= $dari && $tgl <= $sampai) {
                        $uid = $doc['id_user'] ?? 0;
                        $u   = $userMap[$uid] ?? [];
                        $rows[] = [
                            $uid,
                            $u['nama_lengkap'] ?? '-',
                            $u['email'] ?? '-',
                            $doc['cycle_length_days'] ?? '',
                            $doc['prev_cycle_length'] ?? '',
                            $doc['stress_score_cycle'] ?? '',
                            $doc['sleep_hours_cycle'] ?? '',
                            $doc['pain_level'] ?? '',
                            $tgl,
                            $doc['tanggal_selesai_haid'] ?? '',
                        ];
                    }
                }
                return [
                    $rows,
                    ['ID User', 'Nama', 'Email', 'Panjang Siklus', 'Siklus Sebelumnya', 'Stress', 'Tidur', 'Pain', 'Tgl Mulai', 'Tgl Selesai'],
                    'Laporan_Bulanan'
                ];
        }
    }

    /**
     * Hapus riwayat laporan
     */
    public function destroy(string $id)
    {
        try {
            if (!preg_match('/^[a-f\d]{24}$/i', $id)) {
                return back()->with('error', 'ID laporan tidak valid.');
            }

            $this->getDb()
                ->selectCollection('laporan_exports')
                ->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            return back()->with('success', 'Laporan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('LaporanController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus laporan.');
        }
    }
}
