<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mobile\Correction;
use App\Models\Mobile\Cycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;  // 🔴 TAMBAHKAN INI
use Carbon\Carbon;


class CorrectionController extends Controller
{
    /**
     * POST /api/mobile/correction
     * Simpan koreksi siklus dari user
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'expected_start_date' => 'required|date',
                'actual_start_date' => 'required|date',
                'correction_type' => 'required|in:start,end',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user;

            // Simpan koreksi
            $correction = Correction::create([
                'user_id' => $user->id_user,
                'expected_start_date' => Carbon::parse($request->expected_start_date),
                'actual_start_date' => Carbon::parse($request->actual_start_date),
                'correction_type' => $request->correction_type,
                'created_at' => now(),
            ]);

            // Update siklus terakhir user dengan tanggal yang benar
            $lastCycle = Cycle::where('user_id', $user->id_user)
                ->orderBy('last_period_date', 'desc')
                ->first();

            if ($lastCycle) {
                // Hitung panjang siklus baru
                $newCycleLength = $correction->actual_start_date->diffInDays($lastCycle->last_period_date);
                
                // Update cycle length
                $lastCycle->cycle_length_days = max(20, min(45, $newCycleLength));
                $lastCycle->save();
            }

            // Log untuk retraining model
            Log::info('Cycle Correction', [
                'user_id' => $user->id_user,
                'expected_start' => $request->expected_start_date,
                'actual_start' => $request->actual_start_date,
                'error_days' => $correction->getErrorMargin(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Koreksi berhasil disimpan, terima kasih telah membantu AI belajar!',
                'data' => $correction
            ]);

        } catch (\Exception $e) {
            Log::error('Correction store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan koreksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/mobile/corrections
     * Ambil semua koreksi user (untuk admin)
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user;
            
            $corrections = Correction::where('user_id', $user->id_user)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $corrections
            ]);

        } catch (\Exception $e) {
            Log::error('Correction index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data koreksi'
            ], 500);
        }
    }

    /**
     * GET /api/mobile/corrections/stats
     * Statistik error untuk retraining model
     */
    public function stats(Request $request)
    {
        try {
            $user = $request->user;
            
            $corrections = Correction::where('user_id', $user->id_user)->get();
            
            $totalError = 0;
            foreach ($corrections as $correction) {
                $totalError += $correction->getErrorMargin();
            }
            
            $avgError = $corrections->count() > 0 ? $totalError / $corrections->count() : 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_corrections' => $corrections->count(),
                    'average_error_days' => round($avgError, 2),
                    'corrections' => $corrections
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Correction stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik'
            ], 500);
        }
    }
}