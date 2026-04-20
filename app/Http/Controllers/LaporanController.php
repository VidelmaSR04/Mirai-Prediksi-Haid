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

            $riwayat = [];
            foreach ($riwayatCol->find([], ['sort' => ['created_at' => -1], 'limit' => 50]) as $doc) {
                $doc       = (array) $doc;
                $doc['id'] = (string) ($doc['_id'] ?? '');
                $riwayat[] = $doc;
            }

            // Format riwayat untuk blade
            $laporan = array_map(fn($r) => [
                'id'     => $r['id']              ?? '',
                'nama'   => $r['nama']             ?? '-',
                'format' => $r['format']           ?? '-',
                'oleh'   => $r['dibuat_oleh']      ?? '-',
                'waktu'  => $r['created_at'] instanceof UTCDateTime
                    ? $r['created_at']->toDateTime()->format('d M Y H:i')
                    : ($r['created_at'] ?? '-'),
            ], $riwayat);

            $eksporHariIni = $riwayatCol->countDocuments([
                'created_at' => ['$gte' => new UTCDateTime(now()->startOfDay()->getTimestamp() * 1000)],
            ]);

            $totalUkuranKb = array_sum(array_column($riwayat, 'ukuran_kb'));

            $stats = [
                'total'          => $riwayatCol->countDocuments(),
                'ekspor_hari_ini' => $eksporHariIni,
                'terjadwal'      => 2,
                'total_ukuran_mb' => round($totalUkuranKb / 1024, 1),
            ];

            return view('admin.laporan.index', compact('laporan', 'stats'));

        } catch (\Exception $e) {
            Log::error('LaporanController@index: ' . $e->getMessage());
            return view('admin.laporan.index', [
                'laporan' => [],
                'stats'   => ['total' => 0, 'ekspor_hari_ini' => 0, 'terjadwal' => 0, 'total_ukuran_mb' => 0],
                'error'   => $e->getMessage(),
            ]);
        }
    }

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

            [$rows, $headers, $namaFile] = $this->buildReportData($db, $template, $dari, $sampai);

            // Estimasi ukuran
            $csvContent = implode(',', $headers) . "\n";
            foreach ($rows as $row) {
                $csvContent .= implode(',', array_map('strval', $row)) . "\n";
            }
            $ukuranKb = round(strlen($csvContent) / 1024, 2);

            // Simpan riwayat ekspor
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

            $filename = $namaFile . '_' . now()->format('Ymd') . '.csv';

            return response()->stream(function () use ($headers, $rows) {
                $out = fopen('php://output', 'w');
                fputs($out, "\xEF\xBB\xBF");
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

    private function buildReportData($db, string $template, string $dari, string $sampai): array
    {
        $key = match (true) {
            str_contains(strtolower($template), 'pengguna')  => 'pengguna',
            str_contains(strtolower($template), 'siklus') ||
            str_contains(strtolower($template), 'prediksi') &&
            str_contains(strtolower($template), 'kesuburan') => 'siklus',
            str_contains(strtolower($template), 'prediksi')  => 'prediksi',
            str_contains(strtolower($template), 'performa')  => 'prediksi',
            default                                           => 'bulanan',
        };

        switch ($key) {

            // ── PENGGUNA ────────────────────────────────────────────────────
            case 'pengguna':
                $rows = [];
                foreach ($db->selectCollection('users')->find([]) as $doc) {
                    $doc = (array) $doc;
                    $rows[] = [
                        $doc['id_user']      ?? '',
                        $doc['nama_lengkap'] ?? '',
                        $doc['email']        ?? '',
                        $doc['no_telepon']   ?? '',
                        $doc['age']          ?? '',   // field baru
                        $doc['bmi']          ?? '',
                        $doc['status']       ?? 'Aktif',
                    ];
                }
                return [
                    $rows,
                    ['ID User', 'Nama Lengkap', 'Email', 'No. Telepon', 'Usia', 'BMI', 'Status'],
                    'Laporan_Pengguna',
                ];

            // ── SIKLUS ──────────────────────────────────────────────────────
            case 'siklus':
                $rows = [];
                foreach ($db->selectCollection('cycles')->find([]) as $doc) {
                    $doc = (array) $doc;
                    $tgl = $doc['tanggal_mulai_haid'] ?? '';
                    if ($tgl >= $dari && $tgl <= $sampai) {
                        $rows[] = [
                            $doc['id_user']              ?? '',
                            $doc['tanggal_mulai_haid']   ?? '',
                            $doc['tanggal_selesai_haid'] ?? '',
                            $doc['cycle_length_days']    ?? '',  // field baru
                            $doc['pain_level']           ?? '',
                            $doc['stress_score_cycle']   ?? '',
                            $doc['sleep_hours_cycle']    ?? '',
                            $doc['mood_score']           ?? '',
                            $doc['estrogen_pgml']        ?? '',  // fitur ML baru
                            $doc['progesterone_ngml']    ?? '',  // fitur ML baru
                        ];
                    }
                }
                return [
                    $rows,
                    ['ID User', 'Tgl Mulai', 'Tgl Selesai', 'Panjang Siklus', 'Pain', 'Stress', 'Tidur', 'Mood', 'Estrogen', 'Progesteron'],
                    'Laporan_Siklus',
                ];

            // ── PREDIKSI ────────────────────────────────────────────────────
            case 'prediksi':
                $rows = [];
                foreach ($db->selectCollection('predictions')->find([]) as $doc) {
                    $doc    = (array) $doc;
                    $tgl    = $doc['last_period_date'] ?? '';
                    if ($tgl >= $dari && $tgl <= $sampai) {
                        $rows[] = [
                            $doc['id_user']                ?? '',
                            $doc['predicted_cycle_length'] ?? '',
                            $doc['predicted_next_period']  ?? '',
                            $doc['predicted_ovulation']    ?? '',
                            $doc['fertile_start']          ?? '',
                            $doc['fertile_end']            ?? '',
                            $doc['cycle_status']           ?? '',
                            $doc['confidence_score']       ?? '',
                            $doc['mae_error']              ?? '',
                        ];
                    }
                }
                return [
                    $rows,
                    ['ID User', 'Prediksi Siklus (hari)', 'Prediksi Haid', 'Prediksi Ovulasi', 'Fertile Start', 'Fertile End', 'Status', 'Confidence', 'MAE'],
                    'Laporan_Prediksi',
                ];

            // ── BULANAN (gabung users + cycles) ────────────────────────────
            default:
                // Build user map
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
                        $uid  = $doc['id_user'] ?? 0;
                        $u    = $userMap[$uid] ?? [];
                        $rows[] = [
                            $uid,
                            $u['nama_lengkap']           ?? '-',
                            $u['email']                  ?? '-',
                            $doc['cycle_length_days']    ?? '',   // field baru
                            $doc['prev_cycle_length']    ?? '',   // fitur ML baru
                            $doc['stress_score_cycle']   ?? '',
                            $doc['sleep_hours_cycle']    ?? '',
                            $doc['pain_level']           ?? '',
                            $tgl,
                            $doc['tanggal_selesai_haid'] ?? '',
                        ];
                    }
                }
                return [
                    $rows,
                    ['ID User', 'Nama', 'Email', 'Panjang Siklus', 'Siklus Sebelumnya', 'Stress', 'Tidur', 'Pain', 'Tgl Mulai', 'Tgl Selesai'],
                    'Laporan_Bulanan',
                ];
        }
    }

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

        } catch (\MongoDB\Exception\InvalidArgumentException $e) {
            return back()->with('error', 'ID tidak valid: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('LaporanController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus laporan.');
        }
    }
}
