<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Obfuscator — Reversible URL-safe ID obfuscation using Feistel cipher + Base62
 *
 * IMPORTANT: Uses OBFUSCATOR_SALT env variable, NOT APP_KEY.
 * Changing APP_KEY would break all shared/published links.
 *
 * Usage:
 *   Obfuscator::encode(42, 'listing')  → "k9XqL"
 *   Obfuscator::decode("k9XqL", 'listing') → 42
 *   Obfuscator::decode("tampered", 'listing') → null
 */
class Obfuscator
{
    private const BASE62_CHARS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const MIN_LENGTH   = 6;
    private const ROUNDS       = 4;

    public static function encode(int $id, string $context): string
    {
        if ($id <= 0) {
            return '';
        }

        $key   = self::getKey($context);
        $mixed = self::feistel($id, $key, true);
        return self::toBase62($mixed);
    }

    public static function decode(string $hash, string $context): ?int
    {
        if (empty($hash)) {
            return null;
        }

        $decoded = self::fromBase62($hash);
        if ($decoded === null || $decoded <= 0) {
            return null;
        }

        $key = self::getKey($context);
        $id  = self::feistel($decoded, $key, false);

        if ($id <= 0 || $id > PHP_INT_MAX) {
            return null;
        }

        return $id;
    }

    private static function getKey(string $context): int
    {
        $salt = config('app.obfuscator_salt', env('OBFUSCATOR_SALT', 'zintoop_olive_default_salt_change_in_production'));
        $hash = hash('sha256', $salt . ':' . $context);
        return (int) hexdec(substr($hash, 0, 8));
    }

    private static function feistel(int $value, int $key, bool $encrypt): int
    {
        $left  = ($value >> 16) & 0xFFFF;
        $right = $value & 0xFFFF;

        $rounds = range(0, self::ROUNDS - 1);
        if ($encrypt) {
            foreach ($rounds as $round) {
                $roundKey = ($key ^ ($round * 0x9E3779B9)) & 0xFFFF;
                $newLeft  = $right;
                $newRight = $left ^ (($right * $roundKey + $round) & 0xFFFF);
                $left     = $newLeft;
                $right    = $newRight;
            }
        } else {
            $rounds = array_reverse($rounds);
            foreach ($rounds as $round) {
                $roundKey  = ($key ^ ($round * 0x9E3779B9)) & 0xFFFF;
                $origRight = $left;
                $origLeft  = $right ^ (($origRight * $roundKey + $round) & 0xFFFF);
                $left      = $origLeft;
                $right     = $origRight;
            }
        }

        return (($left & 0xFFFF) << 16) | ($right & 0xFFFF);
    }

    private static function toBase62(int $num): string
    {
        if ($num === 0) {
            return str_repeat(self::BASE62_CHARS[0], self::MIN_LENGTH);
        }

        $chars  = self::BASE62_CHARS;
        $base   = strlen($chars);
        $result = '';
        $num    = $num & 0xFFFFFFFF;

        while ($num > 0) {
            $result = $chars[$num % $base] . $result;
            $num    = intdiv($num, $base);
        }

        while (strlen($result) < self::MIN_LENGTH) {
            $result = $chars[0] . $result;
        }

        return $result;
    }

    private static function fromBase62(string $str): ?int
    {
        if (strlen($str) > 10) {
            return null;
        }

        $chars  = self::BASE62_CHARS;
        $base   = strlen($chars);
        $map    = array_flip(str_split($chars));
        $result = 0;

        foreach (str_split($str) as $char) {
            if (!isset($map[$char])) {
                return null;
            }
            $result = $result * $base + $map[$char];
            if (is_float($result) || $result > PHP_INT_MAX) {
                return null;
            }
        }

        return (int) $result;
    }
}

