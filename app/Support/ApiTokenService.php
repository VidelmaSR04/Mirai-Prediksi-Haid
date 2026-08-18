<?php

namespace App\Support;

/**
 * Token API mobile dengan HMAC signature.
 *
 * Format: base64(payload_json).hex(hmac_sha256(base64_payload, APP_KEY))
 *
 * Ini menggantikan token lama yang cuma base64(json) tanpa signature —
 * yang membuat siapa pun bisa memalsukan token untuk id_user berapa pun.
 * Dengan HMAC, payload tidak bisa diubah tanpa mengetahui APP_KEY server,
 * yang tidak pernah dikirim ke client.
 */
class ApiTokenService
{
    /**
     * Buat token bertanda tangan untuk user.
     *
     * @param array $claims Minimal harus berisi 'id_user'. Boleh tambah claim lain.
     * @param int $ttlSeconds Masa berlaku token dalam detik (default 7 hari).
     */
    public static function generate(array $claims, int $ttlSeconds = 86400 * 7): string
    {
        $payload = array_merge($claims, [
            'exp' => time() + $ttlSeconds,
            'iat' => time(),
        ]);

        $payloadEncoded = base64_encode(json_encode($payload));
        $signature = self::sign($payloadEncoded);

        return $payloadEncoded . '.' . $signature;
    }

    /**
     * Verifikasi token dan kembalikan payload jika valid, null jika tidak.
     */
    public static function verify(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 2) {
            return null;
        }

        [$payloadEncoded, $signature] = $parts;

        // Bandingkan signature pakai hash_equals -> aman dari timing attack
        if (!hash_equals(self::sign($payloadEncoded), $signature)) {
            return null;
        }

        $decoded = json_decode(base64_decode($payloadEncoded), true);

        if (!is_array($decoded) || !isset($decoded['exp']) || !isset($decoded['id_user'])) {
            return null;
        }

        if ($decoded['exp'] < time()) {
            return null;
        }

        return $decoded;
    }

    private static function sign(string $payloadEncoded): string
    {
        // APP_KEY Laravel (di .env) dipakai sebagai secret HMAC.
        // Ini sudah unik per instalasi dan tidak pernah dikirim ke client.
        $secret = config('app.key');

        return hash_hmac('sha256', $payloadEncoded, $secret);
    }
}
