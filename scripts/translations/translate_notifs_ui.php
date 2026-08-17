<?php
$translations = [
    'Recent' => ['ar' => 'الأحدث', 'fr' => 'Récents', 'en' => 'Recent'],
    'No notifications yet' => ['ar' => 'لا توجد إشعارات بعد', 'fr' => 'Aucune notification pour le moment', 'en' => 'No notifications yet'],
    'New notification' => ['ar' => 'إشعار جديد', 'fr' => 'Nouvelle notification', 'en' => 'New notification'],
    'You have a new transport deal for' => ['ar' => 'لديك عرض نقل جديد لـ', 'fr' => 'Vous avez une nouvelle offre de transport pour', 'en' => 'You have a new transport deal for'],
    'of' => ['ar' => 'من', 'fr' => 'de', 'en' => 'of'],
    'New Transport Deal' => ['ar' => 'عرض نقل جديد', 'fr' => 'Nouvelle offre de transport', 'en' => 'New Transport Deal'],
    'View Deal' => ['ar' => 'عرض التفاصيل', 'fr' => 'Voir l\'offre', 'en' => 'View Deal'],
];

foreach (['ar', 'fr'] as $lang) {
    $file = __DIR__ . "/resources/lang/{$lang}.json";
    if (file_exists($file)) {
        $json = json_decode(file_get_contents($file), true) ?: [];
        foreach ($translations as $key => $values) {
            $json[$key] = $values[$lang];
        }
        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Updated notification UI translations for $lang\n";
    }
}
