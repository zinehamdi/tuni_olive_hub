<?php
$dirs = [
    __DIR__ . '/resources/views',
    __DIR__ . '/app'
];

$pattern = '/__\(\s*[\'"]([^\'"]+)[\'"]\s*\)/';
$strings = [];

function scan_dir($dir, $pattern, &$strings) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade'])) {
            $content = file_get_contents($file->getPathname());
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $strings[] = $match;
                }
            }
        }
    }
}

foreach ($dirs as $dir) {
    if (file_exists($dir)) {
        scan_dir($dir, $pattern, $strings);
    }
}

$strings = array_unique($strings);
$langFiles = ['ar', 'fr', 'en'];

foreach ($langFiles as $lang) {
    $filePath = __DIR__ . "/resources/lang/{$lang}.json";
    $json = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
    $updated = false;
    
    foreach ($strings as $str) {
        if (!isset($json[$str])) {
            $json[$str] = $str; // Default to English string, will need manual translation later or basic auto-translation
            $updated = true;
        }
    }
    
    if ($updated) {
        file_put_contents($filePath, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Updated {$lang}.json with new keys.\n";
    }
}
echo "Found " . count($strings) . " unique translation keys.\n";
