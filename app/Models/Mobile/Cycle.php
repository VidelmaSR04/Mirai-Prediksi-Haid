<?php

namespace App\Models\Mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Cycle extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cycles';
    
    protected $fillable = [
        'id_cycle',
        'user_id',
        'last_period_date',
        'previous_period_date',
        'cycle_length_days',
        'period_duration_days',  // Tambahkan ini!
        'pain_level',
        'stress_score_cycle',
        'sleep_hours_cycle',
        'mood_score',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'id_cycle' => 'integer',
        'user_id' => 'integer',
        'last_period_date' => 'datetime',
        'previous_period_date' => 'datetime',
        'cycle_length_days' => 'integer',
        'period_duration_days' => 'integer',  // Tambahkan ini!
        'pain_level' => 'integer',
        'stress_score_cycle' => 'integer',
        'sleep_hours_cycle' => 'float',
        'mood_score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
