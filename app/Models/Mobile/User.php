<?php

namespace App\Models\Mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Mobile\Cycle;
use App\Models\Mobile\Prediction;

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
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_verified' => 'boolean',
        'id_user' => 'integer',
        'age' => 'integer',
        'weight_kg' => 'float',
        'height_cm' => 'float',
        'bmi' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Hitung BMI otomatis
     */
    public function calculateBmi()
    {
        if ($this->weight_kg && $this->height_cm) {
            $heightM = $this->height_cm / 100;
            return round($this->weight_kg / ($heightM * $heightM), 1);
        }
        return null;
    }
    
    /**
     * Relasi ke Cycle
     */
    public function cycles()
    {
        return $this->hasMany(Cycle::class, 'user_id', 'id_user');
    }
    
    /**
     * Relasi ke Prediction
     */
    public function predictions()
    {
        return $this->hasMany(Prediction::class, 'user_id', 'id_user');
    }
}