<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $connection = 'mongodb';
    protected $collection = 'users';

    
    protected $fillable = [
        'id_user',
        'name',
        'email',
        'password',
        'status',
        'nama_lengkap',
        'no_telepon',
        'age',
        'weight_kg',
        'height_cm',
        'bmi',
        'email_verified',    
        'verification_token',
        'created_at',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_verified' => 'boolean',
    ];
}
