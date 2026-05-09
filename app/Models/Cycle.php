<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Cycle extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cycles';
    
    protected $fillable = [
        // ✅ Gunakan id_user (konsisten dengan request Flutter)
        'id_user',
        
        // Data tanggal
        'last_period_date',      // Tanggal mulai haid bulan ini
        'previous_period_date',  // Tanggal mulai haid bulan lalu (opsional)
        
        // Data siklus
        'cycle_length_days',     // Panjang siklus (default atau hasil prediksi)
        'period_duration_days',  // Lama haid
        
        // Data untuk model AI
        'pain_level',            // 0-10
        'stress_score_cycle',    // 0-10
        'sleep_hours_cycle',     // 0-24
        'mood_score',            // 1-10
        
        // Data fisik opsional
        'weight_kg',
        'height_cm',
        
        // Data tambahan
        'symptoms',
        'notes',
        
        // Timestamp
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'last_period_date' => 'date',
        'previous_period_date' => 'date',
        'cycle_length_days' => 'integer',
        'period_duration_days' => 'integer',
        'pain_level' => 'integer',
        'stress_score_cycle' => 'integer',
        'sleep_hours_cycle' => 'float',
        'mood_score' => 'integer',
        'weight_kg' => 'float',
        'height_cm' => 'float',
        'symptoms' => 'array',
        'notes' => 'string',
    ];
    
    // Helper: hitung panjang siklus dari selisih tanggal
    public function getCalculatedCycleLengthAttribute()
    {
        if ($this->last_period_date && $this->previous_period_date) {
            return $this->last_period_date->diffInDays($this->previous_period_date);
        }
        return $this->cycle_length_days ?? 28;
    }
    
    // Helper: prev_cycle_length untuk AI (dari 2 periode sebelumnya)
    public function getPrevCycleLengthAttribute()
    {
        // Bisa dihitung dari data sebelumnya jika ada
        return $this->getCalculatedCycleLengthAttribute();
    }
}