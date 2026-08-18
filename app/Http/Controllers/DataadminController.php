<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class DataadminController extends Controller
{
    /**
     * Menampilkan daftar semua admin (dengan fitur pencarian sederhana)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Admin::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
        }

        $admins = $query->orderBy('created_at', 'asc')->get();

        return view('admin.dataadmin.index', compact('admins', 'search'));
    }

    /**
     * Menampilkan detail satu admin.
     * - Kalau dipanggil via fetch() dari modal popup → balas JSON
     * - Kalau dibuka langsung lewat URL browser → tampilkan halaman
     */
    public function show(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json([
                'id'         => (string) $admin->_id,
                'name'       => $admin->name,
                'email'      => $admin->email,
                'created_at' => optional($admin->created_at)->format('d M Y, H:i'),
            ]);
        }

        return view('admin.dataadmin.show', compact('admin'));
    }
}