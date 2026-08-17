<?php
$translations = [
    'More Settings' => ['ar' => 'إعدادات إضافية', 'fr' => 'Plus de paramètres', 'en' => 'More Settings'],
    'notifications' => ['ar' => 'الإشعارات', 'fr' => 'Notifications', 'en' => 'Notifications'],
    'Notifications' => ['ar' => 'الإشعارات', 'fr' => 'Notifications', 'en' => 'Notifications'],
    'No notifications' => ['ar' => 'لا توجد إشعارات', 'fr' => 'Aucune notification', 'en' => 'No notifications']
];

foreach (['ar', 'fr'] as $lang) {
    $file = __DIR__ . "/resources/lang/{$lang}.json";
    if (file_exists($file)) {
        $json = json_decode(file_get_contents($file), true) ?: [];
        foreach ($translations as $key => $values) {
            $json[$key] = $values[$lang];
        }
        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Updated more translations for $lang\n";
    }
}
