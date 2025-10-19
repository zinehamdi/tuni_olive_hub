<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

class SrCode
{
    /**
     * Generate a short reference code with prefix and checksum.
     * Example: TRP-AB12C-7
     */
    public static function make(string $prefix = 'SR'): string
    {
        $base = strtoupper(Str::random(5));
        $payload = $prefix.'-'.$base;
        $sum = 0;
        for ($i = 0; $i < strlen($payload); $i++) {
            $sum += ord($payload[$i]);
        }
        $check = $sum % 10;
        return $payload.'-'.$check;
    }
}
