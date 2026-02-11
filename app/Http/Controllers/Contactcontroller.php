<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * =====================================================
 * CONTACT CONTROLLER
 * =====================================================
 * 
 * Controller untuk menangani form kontak
 * Letakkan file ini di: app/Http/Controllers/ContactController.php
 * 
 * Cara membuat:
 * php artisan make:controller ContactController
 */
class ContactController extends Controller
{
    /**
     * Menampilkan halaman kontak (jika diperlukan halaman terpisah)
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * Menangani submit form kontak
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request)
    {
        // =====================================================
        // VALIDASI INPUT
        // =====================================================
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ], [
            // Pesan error dalam Bahasa Indonesia
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter',
            'message.required' => 'Pesan harus diisi',
            'message.max' => 'Pesan tidak boleh lebih dari 1000 karakter',
        ]);

        // =====================================================
        // OPSI 1: SIMPAN KE DATABASE
        // =====================================================
        // Jika Anda ingin menyimpan pesan ke database,
        // buat model Contact dan migration terlebih dahulu:
        // php artisan make:model Contact -m
        
        /*
        try {
            Contact::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'message' => $validated['message'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving contact: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.')
                ->withInput();
        }
        */

        // =====================================================
        // OPSI 2: KIRIM EMAIL
        // =====================================================
        // Pastikan sudah konfigurasi mail di .env:
        // MAIL_MAILER=smtp
        // MAIL_HOST=smtp.gmail.com
        // MAIL_PORT=587
        // MAIL_USERNAME=your-email@gmail.com
        // MAIL_PASSWORD=your-app-password
        // MAIL_ENCRYPTION=tls
        // MAIL_FROM_ADDRESS=your-email@gmail.com
        // MAIL_FROM_NAME="${APP_NAME}"
        
        /*
        try {
            Mail::send('emails.contact', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'messageContent' => $validated['message']
            ], function ($message) use ($validated) {
                $message->to('hello@cyclepredictor.com')
                    ->subject('Pesan Kontak Baru dari ' . $validated['name']);
                $message->from($validated['email'], $validated['name']);
            });
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim pesan.')
                ->withInput();
        }
        */

        // =====================================================
        // OPSI 3: LOG KE FILE (UNTUK DEVELOPMENT)
        // =====================================================
        Log::info('New contact message received', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'timestamp' => now()
        ]);

        // =====================================================
        // REDIRECT DENGAN PESAN SUKSES
        // =====================================================
        return redirect()->back()
            ->with('success', 'Terima kasih! Pesan Anda telah berhasil dikirim. Kami akan segera menghubungi Anda.');
    }

    /**
     * Menangani subscribe newsletter (jika ada)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        // TODO: Simpan email ke database atau kirim ke service email marketing

        Log::info('New newsletter subscription', [
            'email' => $validated['email'],
            'timestamp' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Terima kasih telah berlangganan newsletter kami!');
    }
}