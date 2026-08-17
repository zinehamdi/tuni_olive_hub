<?php
$translations = [
    'Your profile showcase photos' => [
        'ar' => 'صور العرض الخاصة بملفك الشخصي',
        'fr' => 'Photos de présentation de votre profil'
    ],
    'No photos in your gallery' => [
        'ar' => 'لا توجد صور في معرضك',
        'fr' => 'Aucune photo dans votre galerie'
    ],
    'Upload photos from your profile settings' => [
        'ar' => 'قم برفع الصور من إعدادات ملفك الشخصي',
        'fr' => 'Téléchargez des photos depuis les paramètres de votre profil'
    ],
    'Photo Gallery' => [
        'ar' => 'معرض الصور',
        'fr' => 'Galerie de photos'
    ]
];

foreach (['ar', 'fr'] as $lang) {
    $file = __DIR__ . "/resources/lang/{$lang}.json";
    if (file_exists($file)) {
        $json = json_decode(file_get_contents($file), true) ?: [];
        foreach ($translations as $key => $values) {
            $json[$key] = $values[$lang];
        }
        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Updated gallery translations for $lang\n";
    }
}
