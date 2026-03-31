<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Cycle extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'cycles';

    protected $fillable = [
        'user_id',
        'last_period_date',
        'previous_period_date',
        'cycle_length',
        'period_duration',
        'stress_level',
        'sleep_hours',
        'health_score',
        'symptoms',
        'notes',
        'next_period_date',
        'ovulation_date',
    ];

    protected $casts = [
        'last_period_date' => 'date',
        'previous_period_date' => 'date',
        'next_period_date' => 'date',
        'ovulation_date' => 'date',
        'symptoms' => 'array',
    ];

    /**
     * Get the user that owns the cycle.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}