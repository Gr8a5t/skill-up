<?php

namespace App\Utils;

class HashId
{
    private static $salt = 'skillup-secret-salt-2024';

    /**
     * Encode an ID into a hashed string.
     */
    public static function encode($id)
    {
        if (!$id) return '';
        
        // Simple obfuscation: ID + hash(salt + ID)
        $checksum = substr(md5(self::$salt . $id), 0, 6);
        $combined = $id . '-' . $checksum;
        
        // Base64 encode and make URL-safe
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($combined));
    }

    /**
     * Decode a hashed string back into an ID.
     */
    public static function decode($hash)
    {
        if (!$hash) return [];

        // Reverse URL-safe base64
        $combined = base64_decode(str_replace(['-', '_'], ['+', '/'], $hash));
        if (!$combined) return [];

        $parts = explode('-', $combined);
        if (count($parts) !== 2) return [];

        $id = $parts[0];
        $checksum = $parts[1];

        // Verify checksum
        if (substr(md5(self::$salt . $id), 0, 6) === $checksum) {
            return [(int)$id];
        }

        return [];
    }
}
