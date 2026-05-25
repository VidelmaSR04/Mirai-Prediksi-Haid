<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan Halaman Login Admin
     */
    public function showLogin()
    {
        // Jika sudah login sebagai admin, langsung ke dashboard
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // Menggunakan view yang sudah ada di resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    /**
     * Logout Admin
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Tampilkan Halaman Register (jika diperlukan)
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses Register (bisa diisi nanti)
     */
    public function register(Request $request)
    {
        // Untuk sementara redirect ke login
        return redirect()->route('admin.login')
            ->with('info', 'Fitur register belum diaktifkan.');
    }
}
