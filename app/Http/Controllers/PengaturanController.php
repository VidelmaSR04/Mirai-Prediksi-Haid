<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class PengaturanController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();
        // ✅ View path benar — sesuai file: resources/views/admin/pengaturan/index.blade.php
        return view('admin.pengaturan.index', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = auth('admin')->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
        ]);

        $admin->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'telepon' => $request->telepon ?? null,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $admin = auth('admin')->user();

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => ['required', 'confirmed', Password::min(6)],
        ]);

        if (!Hash::check($request->password_lama, $admin->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $admin->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
