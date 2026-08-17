<?php
$translations = [
    'Accept' => ['ar' => 'قبول', 'fr' => 'Accepter', 'en' => 'Accept'],
    'Reject' => ['ar' => 'رفض', 'fr' => 'Refuser', 'en' => 'Reject'],
    'Chat' => ['ar' => 'دردشة', 'fr' => 'Discuter', 'en' => 'Chat']
];

foreach (['ar', 'fr'] as $lang) {
    $file = __DIR__ . "/resources/lang/{$lang}.json";
    if (file_exists($file)) {
        $json = json_decode(file_get_contents($file), true) ?: [];
        foreach ($translations as $key => $values) {
            $json[$key] = $values[$lang];
        }
        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Updated button translations for $lang\n";
    }
}
