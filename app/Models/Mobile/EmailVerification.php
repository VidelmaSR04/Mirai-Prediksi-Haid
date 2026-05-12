<?php

namespace App\Models\Mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'email_verifications';
    
    protected $fillable = [
        'email',
        'otp',
        'token',
        'expires_at',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}