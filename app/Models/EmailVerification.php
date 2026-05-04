<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class EmailVerification extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'email_verifications';

    protected $fillable = [
        'email',
        'otp',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public $timestamps = true;
}