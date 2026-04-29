<?php

return [
    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        // Untuk user biasa (Breeze bawaan) - WEB
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        // Untuk admin web panel
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],

        // ========== TAMBAHKAN INI UNTUK API MOBILE ==========
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'users',  // Menggunakan provider users untuk mobile
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
        'admins' => [
            'provider' => 'admins',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];