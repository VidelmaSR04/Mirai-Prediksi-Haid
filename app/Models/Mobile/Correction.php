<?php

namespace App\Models\Mobile;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class Correction extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'corrections';
    
    protected $fillable = [
        'user_id',
        'expected_start_date',
        'actual_start_date',
        'expected_end_date',
        'actual_end_date',
        'correction_type',
        'created_at'
    ];
    
    protected $casts = [
        'user_id' => 'integer',
        'expected_start_date' => 'datetime',
        'actual_start_date' => 'datetime',
        'expected_end_date' => 'datetime',
        'actual_end_date' => 'datetime',
        'created_at' => 'datetime',
    ];
    
    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
    
    /**
     * Hitung selisih error (untuk retraining model)
     */
    public function getErrorMargin()
    {
        if ($this->correction_type === 'start') {
            return $this->expected_start_date->diffInDays($this->actual_start_date);
        }
        if ($this->expected_end_date && $this->actual_end_date) {
            return $this->expected_end_date->diffInDays($this->actual_end_date);
        }
        return 0;
    }
}