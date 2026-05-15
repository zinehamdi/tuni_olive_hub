<?php
$keys = [
    "You have a new transport deal for" => [
        "ar" => "لديك عرض نقل جديد لـ",
        "fr" => "Vous avez une nouvelle offre de transport pour",
        "en" => "You have a new transport deal for"
    ],
    "of" => [
        "ar" => "من",
        "fr" => "de",
        "en" => "of"
    ],
    "New Transport Deal" => [
        "ar" => "عرض نقل جديد",
        "fr" => "Nouvelle Offre de Transport",
        "en" => "New Transport Deal"
    ],
    "View Deal" => [
        "ar" => "عرض التفاصيل",
        "fr" => "Voir l'offre",
        "en" => "View Deal"
    ],
    "Transport Offers & Tasks" => [
        "ar" => "عروض ومهام النقل",
        "fr" => "Offres et tâches de transport",
        "en" => "Transport Offers & Tasks"
    ],
    "Load" => [
        "ar" => "شحنة",
        "fr" => "Chargement",
        "en" => "Load"
    ]
];

foreach (['ar', 'fr', 'en'] as $lang) {
    $file = "/Users/zinehamdi/Sites/localhost/tuni-olive-hub/resources/lang/{$lang}.json";
    if (file_exists($file)) {
        $json = json_decode(file_get_contents($file), true) ?: [];
        foreach ($keys as $key => $translations) {
            if (!isset($json[$key])) {
                $json[$key] = $translations[$lang];
            }
        }
        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Updated $lang.json\n";
    }
}
