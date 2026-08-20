<?php

/**
 * Helper function to safely retrieve a localized value from an associative array.
 *
 * Many of our Eloquent models store multilingual strings as an array, e.g.
 *   ['en' => 'Olive Oil', 'fr' => 'Huile d'olive', 'ar' => 'زيت زيتون']
 * When a locale does not have a translation, accessing `$values['es']` throws
 * an "Undefined array key" exception.  This function abstracts the fallback
 * logic so the view can simply call `localized($model->title)`.
 *
 * The fallback order is:
 *   1. Current application locale (`app()->getLocale()`)
 *   2. English (`en`) – the primary source language of the project
 *   3. Arabic (`ar`) – the original locale that existed before the new ones
 *   4. An empty string if nothing matches
 */
if (!function_exists('localized')) {
    function localized(array $values, string $locale = null, array $fallback = ['en', 'ar']): string
    {
        $locale ??= app()->getLocale();
        foreach (array_merge([$locale], $fallback) as $l) {
            if (isset($values[$l]) && $values[$l] !== '') {
                return $values[$l];
            }
        }
        return '';
    }
}
