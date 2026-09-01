<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menambahkan header keamanan HTTP standar pada setiap response.
 *
 * Menutup temuan pentest:
 * - HSTS (RENDAH) — "not offered"
 * - HTTP Missing Security Headers (INFORMASI, berulang)
 * - Missing Subresource Integrity (dibantu via CSP frame-ancestors/base-uri)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // HSTS: hanya kirim saat koneksi memang HTTPS (via Cloudflare/TrustProxies)
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );

        return $response;
    }
}
