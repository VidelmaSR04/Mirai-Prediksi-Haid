<?php

namespace App\Http\Controllers;

use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
     * Helper: konversi start_date (bisa berupa string ATAU objek MongoDB
     * BSON UTCDateTime) menjadi objek \DateTime PHP biasa.
     * Dipakai bersama oleh hitungFase(), hitungOvulasi(), dan hitungNextPeriod()
     * supaya logikanya konsisten dan tidak duplikat.
     */
    private function normalizeDate($startDate): ?\DateTime
    {
        if ($startDate instanceof \MongoDB\BSON\UTCDateTime) {
            return $startDate->toDateTime();
        }

        if (is_string($startDate) && $startDate !== '') {
            try {
                return new \DateTime($startDate);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Hitung confidence score dari data kesehatan siklus.
     */
    private function hitungConfidence(array $row): int
    {
        $health = (float)($row['overall_health_score'] ?? 0);
        $mood   = (float)($row['mood_score']           ?? 5);
        $stress = (float)($row['stress_score']         ?? $row['stress_score_cycle'] ?? 5);
        $sleep  = (float)($row['sleep_hours']           ?? $row['sleep_hours_cycle']  ?? 7);

        $healthConf = ($health / 10) * 100;
        $moodConf   = ($mood   / 10) * 100;
        $stressConf = ((10 - $stress) / 10) * 100;
        $sleepConf  = max(0, 100 - abs($sleep - 8) * 10);

        $conf = (0.50 * $healthConf)
              + (0.20 * $moodConf)
              + (0.20 * $stressConf)
              + (0.10 * $sleepConf);

        // Bonus jika panjang siklus normal
        $cycleLength = (int)($row['cycle_length'] ?? $row['cycle_length_days'] ?? 0);
        if ($cycleLength >= 21 && $cycleLength <= 35) {
            $conf += 5;
        }

        return (int)min(100, round($conf));
    }

    /**
     * Hitung prediksi tanggal haid berikutnya.
     * PERBAIKAN: sebelumnya fungsi ini menerima array $row dan mencari
     * key 'cycle_length_days', padahal data siklus di controller ini
     * memakai key 'cycle_length' -> panjang siklus selalu terbaca 0,
     * sehingga tanggal prediksi gagal dihitung. Sekarang fungsi menerima
     * $startDate dan $cycleLength secara eksplisit (konsisten dengan
     * hitungFase() dan hitungOvulasi()), dan mendukung start_date berupa
     * string maupun objek MongoDB UTCDateTime.
     */
    private function hitungNextPeriod($startDate, int $cycleLength): string
    {
        $dt = $this->normalizeDate($startDate);

        if (!$dt || $cycleLength <= 0) {
            return '-';
        }

        $dt->modify("+{$cycleLength} days");
        return $dt->format('d/m/Y');
    }

    /**
     * Hitung fase siklus berdasarkan tanggal
     */
    private function hitungFase($startDate, $cycleLength): string
    {
        $dt = $this->normalizeDate($startDate);

        if (!$dt) {
            return 'folikel';
        }

        try {
            $now = new \DateTime();
            $daysSinceStart = $now->diff($dt)->days;

            if ($daysSinceStart <= 5) {
                return 'menstruasi';
            } elseif ($daysSinceStart <= 14) {
                return 'folikel';
            } elseif ($daysSinceStart <= 16) {
                return 'ovulasi';
            } elseif ($daysSinceStart <= $cycleLength) {
                return 'luteal';
            } else {
                return 'folikel';
            }
        } catch (\Exception $e) {
            return 'folikel';
        }
    }

    /**
     * Hitung estimasi ovulasi (sekitar hari ke-14 dari start_date)
     */
    private function hitungOvulasi($startDate, $cycleLength): string
    {
        $dt = $this->normalizeDate($startDate);

        if (!$dt) {
            return 'Tidak';
        }

        try {
            $dt->modify('+14 days');
            return $dt->format('d/m/Y');
        } catch (\Exception $e) {
            return 'Tidak';
        }
    }

    public function index()
    {
        try {
            $db = $this->getDb();

            /*
            |--------------------------------------------------------------------------
            | USERS → map user_id ke data user
            |--------------------------------------------------------------------------
            */
            $userMap = [];
            foreach ($db->users->find([]) as $user) {
                $user = (array)$user;
                // Coba berbagai format ID
                $uid = $user['_id_user'] ?? $user['id_user'] ?? $user['user_id'] ?? (string)$user['_id'] ?? '';
                if ($uid) {
                    $userMap[(string)$uid] = $user;
                }
                // Juga map dengan _id string
                if (isset($user['_id'])) {
                    $userMap[(string)$user['_id']] = $user;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA SIKLUS DARI COLLECTION CYCLES
            |--------------------------------------------------------------------------
            */
            $cycles = [];
            foreach ($db->cycles->find([]) as $cycle) {
                $cycle = (array)$cycle;
                $userId = $cycle['id_user'] ?? $cycle['user_id'] ?? null;
                if ($userId) {
                    $cycles[] = [
                        'user_id' => (string)$userId,
                        // last_period_date = tanggal mulai haid TERAKHIR yang tercatat di DB
                        'start_date' => $cycle['start_date'] ?? $cycle['last_period_date'] ?? $cycle['tanggal_mulai'] ?? null,
                        'end_date' => $cycle['end_date'] ?? $cycle['previous_period_date'] ?? $cycle['tanggal_selesai'] ?? null,
                        // cycle_length_days = nama field asli di collection cycles
                        'cycle_length' => (int)($cycle['cycle_length_days'] ?? $cycle['cycle_length'] ?? $cycle['panjang_siklus'] ?? 0),
                        'pain_level' => $cycle['pain_level'] ?? $cycle['tingkat_nyeri'] ?? 0,
                        'flow_level' => $cycle['flow_level'] ?? $cycle['flow'] ?? 0,
                        'sleep_hours' => $cycle['sleep_hours'] ?? $cycle['sleep_hours_cycle'] ?? 7,
                        'stress_score' => $cycle['stress_score'] ?? $cycle['stress_score_cycle'] ?? 5,
                        'mood_score' => $cycle['mood_score'] ?? 5,
                        'overall_health_score' => $cycle['overall_health_score'] ?? 7,
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE PREDIKSI DARI DATA SIKLUS
            |--------------------------------------------------------------------------
            */
            $prediksi = [];
            $faseCount = [
                'folikel' => 0,
                'ovulasi' => 0,
                'luteal' => 0,
                'menstruasi' => 0,
            ];

            $totalConfidence = 0;
            $confidenceCounter = 0;

            // Kelompokkan siklus per user
            $userCycles = [];
            foreach ($cycles as $cycle) {
                $userId = $cycle['user_id'];
                if (!isset($userCycles[$userId])) {
                    $userCycles[$userId] = [];
                }
                $userCycles[$userId][] = $cycle;
            }

            foreach ($userCycles as $userId => $userCycleData) {
                // Ambil data user
                $user = $userMap[$userId] ?? [];

                // Hitung rata-rata panjang siklus
                $totalLength = array_sum(array_column($userCycleData, 'cycle_length'));
                $avgCycleLength = count($userCycleData) > 0 ? round($totalLength / count($userCycleData)) : 28;

                // Ambil siklus terakhir
                $lastCycle = end($userCycleData);

                // Hitung fase saat ini
                $fase = $this->hitungFase($lastCycle['start_date'] ?? null, $avgCycleLength);
                $faseCount[$fase]++;

                // Nama pengguna
                $nama = $user['_name'] ?? $user['name'] ?? $user['nama_lengkap'] ?? $user['nama'] ?? ('User ' . $userId);

                // Usia
                $usia = $user['_age'] ?? $user['age'] ?? $user['umur'] ?? '-';

                // Confidence Score
                $rawConf = null;
                // Coba ambil dari data siklus
                if (isset($lastCycle['confidence_score'])) {
                    $rawConf = $lastCycle['confidence_score'];
                }

                if ($rawConf !== null && $rawConf !== '') {
                    $confidence = (float)$rawConf;
                    if ($confidence <= 1) {
                        $confidence *= 100;
                    }
                    $confidence = (int)round($confidence);
                } else {
                    // Hitung dari metrik kesehatan
                    $confidence = $this->hitungConfidence($lastCycle);
                }

                $totalConfidence += $confidence;
                $confidenceCounter++;

                // Status siklus
                $cycleStatus = ($avgCycleLength >= 21 && $avgCycleLength <= 35) ? 'normal' : 'tidak_normal';

                // Estimasi Ovulasi
                $ovulasi = $this->hitungOvulasi($lastCycle['start_date'] ?? null, $avgCycleLength);

                // Prediksi Haid Berikutnya (pakai avgCycleLength, bukan cycle_length_days yang tidak ada)
                $prediksiHaid = $this->hitungNextPeriod($lastCycle['start_date'] ?? null, $avgCycleLength);

                $prediksi[] = [
                    'id_user' => $userId,
                    'nama' => $nama,
                    'usia' => $usia,
                    'panjang_siklus' => $avgCycleLength,
                    'pattern' => $fase,
                    'fase_saat_ini' => ucfirst($fase),
                    'ovulasi' => $ovulasi,
                    'tanggal_mulai_haid' => $prediksiHaid,
                    'confidence_score' => $confidence,
                    'cycle_status' => $cycleStatus,
                    'pain_level' => $lastCycle['pain_level'] ?? '-',
                    'stress_score' => $lastCycle['stress_score'] ?? '-',
                    'sleep_hours' => $lastCycle['sleep_hours'] ?? '-',
                    'mood_score' => $lastCycle['mood_score'] ?? '-',
                    'overall_health_score' => $lastCycle['overall_health_score'] ?? '-',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | SUMMARY
            |--------------------------------------------------------------------------
            */
            $totalPrediksi = count($prediksi);
            $avgConf = $confidenceCounter > 0 ? round($totalConfidence / $confidenceCounter, 1) : 0;
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
                    'prediksi' => [],
                    'faseCount' => [
                        'folikel' => 0,
                        'ovulasi' => 0,
                        'luteal' => 0,
                        'menstruasi' => 0,
                    ],
                    'totalPrediksi' => 0,
                    'avgConf' => 0,
                    'sedangSubur' => 0,
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Generate prediksi manual (untuk tombol generate)
     */
    public function generate()
    {
        try {
            $db = $this->getDb();

            // Ambil semua user
            $users = [];
            foreach ($db->users->find([]) as $user) {
                $user = (array)$user;
                $users[(string)($user['_id_user'] ?? $user['id_user'] ?? (string)$user['_id'])] = $user;
            }

            // Ambil data siklus
            $cycles = [];
            foreach ($db->cycles->find([]) as $cycle) {
                $cycle = (array)$cycle;
                $userId = $cycle['id_user'] ?? $cycle['user_id'] ?? null;
                if ($userId) {
                    $cycles[] = [
                        'user_id' => (string)$userId,
                        'start_date' => $cycle['start_date'] ?? $cycle['last_period_date'] ?? $cycle['tanggal_mulai'] ?? null,
                        'cycle_length' => (int)($cycle['cycle_length_days'] ?? $cycle['cycle_length'] ?? $cycle['panjang_siklus'] ?? 28),
                        'pain_level' => $cycle['pain_level'] ?? 0,
                        'sleep_hours' => $cycle['sleep_hours'] ?? $cycle['sleep_hours_cycle'] ?? 7,
                        'stress_score' => $cycle['stress_score'] ?? $cycle['stress_score_cycle'] ?? 5,
                        'mood_score' => $cycle['mood_score'] ?? 5,
                        'overall_health_score' => $cycle['overall_health_score'] ?? 7,
                    ];
                }
            }

            // Kelompokkan per user
            $userCycles = [];
            foreach ($cycles as $cycle) {
                $uid = $cycle['user_id'];
                if (!isset($userCycles[$uid])) {
                    $userCycles[$uid] = [];
                }
                $userCycles[$uid][] = $cycle;
            }

            // Hapus data prediksi lama
            $db->predictions->deleteMany([]);

            // Generate prediksi per user
            $inserted = 0;
            foreach ($userCycles as $userId => $userCycleData) {
                $user = $users[$userId] ?? [];
                $totalLength = array_sum(array_column($userCycleData, 'cycle_length'));
                $avgCycleLength = count($userCycleData) > 0 ? round($totalLength / count($userCycleData)) : 28;
                $lastCycle = end($userCycleData);

                $fase = $this->hitungFase($lastCycle['start_date'] ?? null, $avgCycleLength);
                $confidence = $this->hitungConfidence($lastCycle);
                $ovulasi = $this->hitungOvulasi($lastCycle['start_date'] ?? null, $avgCycleLength);
                $nextPeriod = $this->hitungNextPeriod($lastCycle['start_date'] ?? null, $avgCycleLength);
                $nama = $user['_name'] ?? $user['name'] ?? $user['nama_lengkap'] ?? 'User ' . $userId;

                $db->predictions->insertOne([
                    'user_id' => $userId,
                    'user_name' => $nama,
                    'cycle_length_days' => $avgCycleLength,
                    'cycle_phase' => $fase,
                    'ovulation_result' => $ovulasi,
                    'predicted_next_period_date' => $nextPeriod,
                    'confidence_score' => $confidence,
                    'start_date' => $lastCycle['start_date'] ?? null,
                    'pain_level' => $lastCycle['pain_level'] ?? 0,
                    'sleep_hours_cycle' => $lastCycle['sleep_hours'] ?? 7,
                    'stress_score_cycle' => $lastCycle['stress_score'] ?? 5,
                    'mood_score' => $lastCycle['mood_score'] ?? 5,
                    'overall_health_score' => $lastCycle['overall_health_score'] ?? 7,
                    'created_at' => new \MongoDB\BSON\UTCDateTime(),
                ]);
                $inserted++;
            }

            return response()->json([
                'success' => true,
                'message' => 'Prediksi berhasil digenerate',
                'total' => $inserted
            ]);

        } catch (\Exception $e) {
            Log::error('Generate Prediksi Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}