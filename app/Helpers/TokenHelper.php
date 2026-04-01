<?php

namespace App\Helpers;

class TokenHelper
{
    public static function generateToken($userId)
    {
        return base64_encode($userId . '|' . time() . '|' . uniqid());
    }

    public static function validateToken($token)
    {
        // Simple validation - bisa dikembangkan lebih lanjut
        return true;
    }
}