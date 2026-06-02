<?php

namespace App\Http\Controllers;

use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class PrediksiController extends Controller
{
    private function getDb()
    {
        $client = new MongoClient(
            env('MONGODB_URI', 'mongodb://127.0.0.1:27017')
        );

        return $client->selectDatabase(
            env('MONGODB_DATABASE', 'mirai')
        );
    }

    /**
     * Hitung confidence score dari data kesehatan siklus.
     * Digunakan ketika confidence_score tidak tersedia di database.
     *
     * Formula:
     *   50% overall_health_score (skala 0-10)
     *   20% mood_score          (skala 0-10)
     *   20% stress_score (dibalik, stres rendah = baik)
     *   10% sleep_hours (optimal 7-9 jam)
     *   +5 bonus siklus normal (21-35 hari)
     */
    private function hitungConfidence(array $row): int
    {
        $health = (float)($row['overall_health_score'] ?? 0);
        $mood   = (float)($row['mood_score']           ?? 5);
        $stress = (float)($row['stress_score_cycle']   ?? 5);
        $sleep  = (float)($row['sleep_hours_cycle']    ?? 7);

        $healthConf = ($health / 10) * 100;
        $moodConf   = ($mood   / 10) * 100;
        $stressConf = ((10 - $stress) / 10) * 100;
        $sleepConf  = max(0, 100 - abs($sleep - 8) * 10);

        $conf = (0.50 * $healthConf)
              + (0.20 * $moodConf)
              + (0.20 * $stressConf)
              + (0.10 * $sleepConf);

        // Bonus kecil jika panjang siklus normal
        $cycleLength = (int)($row['cycle_length_days'] ?? 0);
        if ($cycleLength >= 21 && $cycleLength <= 35) {
            $conf += 5;
        }

        return (int)min(100, round($conf));
    }

    /**
     * Hitung prediksi tanggal haid berikutnya dari start_date + cycle_length_days.
     * Fallback ke field lain jika tersedia.
     */
    private function hitungNextPeriod(array $row): string
    {
        // Prioritaskan field eksplisit di database
        if (!empty($row['predicted_next_period_date'])) {
            return $row['predicted_next_period_date'];
        }
        if (!empty($row['next_period_date'])) {
            return $row['next_period_date'];
        }

        // Hitung dari start_date + cycle_length_days
        $startDate   = $row['start_date']        ?? '';
        $cycleLength = (int)($row['cycle_length_days'] ?? 0);

        if ($startDate && $cycleLength > 0) {
            try {
                $dt = new \DateTime($startDate);
                $dt->modify("+{$cycleLength} days");
                return $dt->format('Y-m-d');
            } catch (\Exception $e) {
                // biarkan jatuh ke default
            }
        }

        return $startDate ?: '-';
    }

    public function index()
    {
        try {

            $db = $this->getDb();

            /*
            |--------------------------------------------------------------------------
            | USERS  →  map user_id ke data user
            |--------------------------------------------------------------------------
            */
            $userMap = [];

            foreach ($db->users->find([]) as $user) {
                $user = (array)$user;
                $uid  = (string)($user['user_id'] ?? $user['_id'] ?? '');
                if ($uid) {
                    $userMap[$uid] = $user;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | INITIAL STATE
            |--------------------------------------------------------------------------
            */
            $prediksi = [];

            $faseCount = [
                'folikel'     => 0,
                'ovulasi'     => 0,
                'luteal'      => 0,
                'menstruasi'  => 0,
            ];

            $totalConfidence    = 0;
            $confidenceCounter  = 0;

            /*
            |--------------------------------------------------------------------------
            | PREDICTIONS
            |--------------------------------------------------------------------------
            */
            foreach ($db->predictions->find([]) as $row) {

                $row    = (array)$row;
                $userId = (string)($row['user_id'] ?? '');
                $user   = $userMap[$userId] ?? [];

                // ── Nama Pengguna ─────────────────────────────────────────────
                $nama = $user['nama_lengkap']
                     ?? $user['name']
                     ?? $user['username']
                     ?? ('User ' . $userId);

                // ── Fase Siklus ───────────────────────────────────────────────
                $faseRaw = strtolower(trim($row['cycle_phase'] ?? $row['phase'] ?? ''));

                switch ($faseRaw) {
                    case 'follicular':
                    case 'folikel':
                        $fase = 'folikel';
                        break;
                    case 'ovulation':
                    case 'ovulasi':
                        $fase = 'ovulasi';
                        break;
                    case 'luteal':
                        $fase = 'luteal';
                        break;
                    case 'menstrual':
                    case 'menstruation':
                    case 'menstruasi':
                        $fase = 'menstruasi';
                        break;
                    default:
                        $fase = 'folikel';
                        break;
                }

                $faseCount[$fase]++;

                // ── Confidence Score ──────────────────────────────────────────
                // Gunakan nilai dari DB jika ada, jika tidak hitung otomatis
                $rawConf = $row['confidence_score'] ?? $row['log_consistency_score'] ?? null;

                if ($rawConf !== null && $rawConf !== '') {
                    $confidence = (float)$rawConf;
                    if ($confidence <= 1) {
                        $confidence *= 100;   // konversi 0-1 ke 0-100
                    }
                    $confidence = (int)round($confidence);
                } else {
                    // Hitung dari metrik kesehatan
                    $confidence = $this->hitungConfidence($row);
                }

                $totalConfidence += $confidence;
                $confidenceCounter++;

                // ── Status Siklus ─────────────────────────────────────────────
                $cycleLength  = (int)($row['cycle_length_days'] ?? 0);
                $cycleStatus  = ($cycleLength >= 21 && $cycleLength <= 35)
                              ? 'normal'
                              : 'tidak_normal';

                // ── Estimasi Ovulasi ──────────────────────────────────────────
                // Ovulasi biasanya terjadi sekitar hari ke-14 sebelum haid berikutnya
                $ovulasi = $row['ovulation_result'] ?? null;
                if ($ovulasi === null) {
                    if ($fase === 'ovulasi') {
                        $ovulasi = 'Ya';
                    } elseif (!empty($row['start_date']) && $cycleLength > 0) {
                        // Perkiraan tanggal ovulasi = start_date + (cycle_length - 14)
                        try {
                            $dt = new \DateTime($row['start_date']);
                            $dt->modify('+' . max(1, $cycleLength - 14) . ' days');
                            $ovulasi = $dt->format('Y-m-d');
                        } catch (\Exception $e) {
                            $ovulasi = 'Tidak';
                        }
                    } else {
                        $ovulasi = 'Tidak';
                    }
                }

                // ── Prediksi Haid Berikutnya ──────────────────────────────────
                $prediksiHaid = $this->hitungNextPeriod($row);

                // ── Usia ──────────────────────────────────────────────────────
                $usia = $user['age'] ?? $user['usia'] ?? '-';

                // ── MAE (Mean Absolute Error) ─────────────────────────────────
                // Estimasi MAE dari cycle_length variance jika tidak tersedia
                $mae = $row['mae'] ?? $row['mae_error'] ?? null;
                if ($mae === null) {
                    // Estimasi: semakin jauh dari 28 hari (rata-rata normal), makin besar error
                    $mae = round(abs($cycleLength - 28) * 0.3, 1);
                }

                // ── Kumpulkan ─────────────────────────────────────────────────
                $prediksi[] = [
                    'id_user'            => $userId,
                    'nama'               => $nama,
                    'usia'               => $usia,
                    'panjang_siklus'     => $cycleLength,
                    'pattern'            => $fase,
                    'fase_saat_ini'      => ucfirst($fase),
                    'ovulasi'            => $ovulasi,
                    'tanggal_mulai_haid' => $prediksiHaid,
                    'confidence_score'   => $confidence,
                    'cycle_status'       => $cycleStatus,
                    'pain_level'         => $row['pain_level']         ?? '-',
                    'stress_score'       => $row['stress_score_cycle'] ?? '-',
                    'sleep_hours'        => $row['sleep_hours_cycle']  ?? '-',
                    'mood_score'         => $row['mood_score']         ?? '-',
                    'overall_health_score' => $row['overall_health_score'] ?? '-',
                    'mae_error'          => $mae,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | SUMMARY
            |--------------------------------------------------------------------------
            */
            $totalPrediksi = count($prediksi);

            $avgConf = $confidenceCounter > 0
                     ? round($totalConfidence / $confidenceCounter, 1)
                     : 0;

            $sedangSubur = $faseCount['ovulasi'];

            return view(
                'admin.prediksi.index',
                compact(
                    'prediksi',
                    'faseCount',
                    'totalPrediksi',
                    'avgConf',
                    'sedangSubur'
                )
            );

        } catch (\Exception $e) {

            Log::error('Prediksi Error: ' . $e->getMessage());

            return view(
                'admin.prediksi.index',
                [
                    'prediksi'      => [],
                    'faseCount'     => [
                        'folikel'    => 0,
                        'ovulasi'    => 0,
                        'luteal'     => 0,
                        'menstruasi' => 0,
                    ],
                    'totalPrediksi' => 0,
                    'avgConf'       => 0,
                    'sedangSubur'   => 0,
                    'error'         => $e->getMessage(),
                ]
            );
        }
    }
}