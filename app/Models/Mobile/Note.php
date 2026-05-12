<?php

namespace App\Models\Mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Note extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notes';
    
    protected $fillable = [
        'user_id',
        'date',
        'mood_level',
        'symptoms',
        'notes',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'user_id' => 'integer',
        'date' => 'datetime',
        'mood_level' => 'integer',
        'symptoms' => 'array',
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
    
    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
    
    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
                     ->whereMonth('date', $month);
    }
}