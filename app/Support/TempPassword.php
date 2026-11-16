<?php

namespace App\Support;

class TempPassword
{
    // 12 characters, without look-alike characters (no 0/O, 1/I)
    public static function generate(int $length = 12): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';

        for ($i = 0; $i < $length; $i++) {
            $out .= $chars[random_int(0, strlen($chars) - 1)];   // random_int is cryptographically secure
        }

        return $out;
    }
}