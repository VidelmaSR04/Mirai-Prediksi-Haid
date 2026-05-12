<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mobile\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NoteController extends Controller
{
    /**
     * POST /api/mobile/note
     * Simpan catatan harian user
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'mood_level' => 'required|integer|min:1|max:10',
                'symptoms' => 'nullable|array',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user;
            $date = Carbon::parse($request->date)->startOfDay();

            // Upsert catatan (update jika sudah ada)
            $note = Note::updateOrCreate(
                [
                    'user_id' => $user->id_user,
                    'date' => $date,
                ],
                [
                    'mood_level' => $request->mood_level,
                    'symptoms' => $request->symptoms ?? [],
                    'notes' => $request->notes ?? '',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil disimpan',
                'data' => $note
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan catatan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/mobile/note/{date}
     * Ambil catatan berdasarkan tanggal
     */
    public function show(Request $request, $date)
    {
        try {
            $user = $request->user;
            $parsedDate = Carbon::parse($date)->startOfDay();

            $note = Note::where('user_id', $user->id_user)
                ->whereDate('date', $parsedDate)
                ->first();

            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $note
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil catatan'
            ], 500);
        }
    }

    /**
     * GET /api/mobile/notes/{year}/{month}
     * Ambil semua catatan dalam satu bulan
     */
    public function getByMonth(Request $request, $year, $month)
    {
        try {
            $user = $request->user;

            $notes = Note::where('user_id', $user->id_user)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get(['date']);

            return response()->json([
                'success' => true,
                'data' => $notes->map(function($note) {
                    return [
                        'date' => $note->date->format('Y-m-d')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil catatan'
            ], 500);
        }
    }

    /**
     * DELETE /api/mobile/note/{id}
     * Hapus catatan
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user;

            $note = Note::where('user_id', $user->id_user)
                ->where('_id', $id)
                ->first();

            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan tidak ditemukan'
                ], 404);
            }

            $note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus catatan'
            ], 500);
        }
    }
}