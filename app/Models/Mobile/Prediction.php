<?php

namespace App\Models\Mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'predictions';
    
    protected $fillable = [
        'id_prediction',
        'user_id',
        'last_cycle_start_date',
        'previous_cycle_start_date',
        'predicted_cycle_length',
        'predicted_next_date',
        'error_margin',
        'confidence_level',
        'input_data',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'id_prediction' => 'integer',
        'user_id' => 'integer',
        'last_cycle_start_date' => 'datetime',
        'previous_cycle_start_date' => 'datetime',
        'predicted_cycle_length' => 'float',
        'predicted_next_date' => 'datetime',
        'error_margin' => 'float',
        'input_data' => 'array',
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