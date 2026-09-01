<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Aplikasi ini berjalan di belakang Cloudflare, sehingga proxy harus
     * dipercaya agar Laravel membaca skema asli (HTTPS) dari header
     * X-Forwarded-Proto. Tanpa ini, Request::secure() selalu bernilai
     * false di belakang proxy sehingga cookie sesi tidak pernah mendapat
     * atribut Secure meski SESSION_SECURE_COOKIE=true, dan redirect HTTPS
     * paksa tidak berjalan dengan benar.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
