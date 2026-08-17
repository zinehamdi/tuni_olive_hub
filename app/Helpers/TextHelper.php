<?php

namespace App\Helpers;

class TextHelper
{
    /**
     * Romanize/Transliterate Arabic text to Latin characters phonetically.
     */
    public static function romanizeArabic(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }

        // Replace "ال" at the beginning of words first
        $text = preg_replace_callback('/(^|\s)ال/u', function($matches) {
            return $matches[1] . 'El ';
        }, $text);

        // Basic phonetic mapping for Arabic to Latin
        $mapping = [
            'ا' => 'a', 'أ' => 'a', 'إ' => 'i', 'آ' => 'a',
            'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j',
            'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'dh',
            'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'ch', // Using 'ch' often for Tunisian phonetics (e.g. chebba instead of shabba)
            'ص' => 's', 'ض' => 'd', 'ط' => 't', 'ظ' => 'dh',
            'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'k', // Using 'k' or 'q', Tunisian often 'k' or 'g' but 'k' is safe
            'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n',
            'ه' => 'h', 'و' => 'ou', 'ي' => 'y', 'ى' => 'a',
            'ة' => 'a', 'ئ' => 'i', 'ؤ' => 'ou', 'ء' => 'a',
            'َ' => 'a', 'ُ' => 'ou', 'ِ' => 'i', 'ّ' => '', // ignore shadda
            'ً' => 'an', 'ٌ' => 'oun', 'ٍ' => 'in',
        ];

        $romanized = strtr($text, $mapping);
        
        // Clean up multiple spaces
        $romanized = preg_replace('/\s+/', ' ', $romanized);
        $romanized = trim($romanized);
        
        // Capitalize words
        return ucwords($romanized);
    }
    
    /**
     * Return translated/transliterated name based on locale
     */
    public static function localizeArabicString(?string $text, $locale = null): ?string
    {
        if (empty($text)) {
            return $text;
        }
        
        $locale = $locale ?? app()->getLocale();
        
        // If locale is Arabic, return as is
        if ($locale === 'ar') {
            return $text;
        }
        
        // Check if string contains Arabic characters
        if (preg_match('/\p{Arabic}/u', $text)) {
            return self::romanizeArabic($text);
        }
        
        return $text;
    }
}
