<?php
$translations = [
    'nav.home' => ['ar' => 'الرئيسية', 'fr' => 'Accueil', 'en' => 'Home'],
    'nav.prices' => ['ar' => 'الأسعار', 'fr' => 'Prix', 'en' => 'Prices'],
    'nav.how_it_works' => ['ar' => 'كيف نعمل', 'fr' => 'Comment ça marche', 'en' => 'How it works'],
    'nav.about' => ['ar' => 'من نحن', 'fr' => 'À propos', 'en' => 'About us'],
    'nav.dashboard' => ['ar' => 'لوحة التحكم', 'fr' => 'Tableau de bord', 'en' => 'Dashboard'],
    'nav.profile' => ['ar' => 'الملف الشخصي', 'fr' => 'Profil', 'en' => 'Profile'],
    'nav.admin_panel' => ['ar' => 'لوحة الإدارة', 'fr' => 'Administration', 'en' => 'Admin Panel'],
    'nav.logout' => ['ar' => 'تسجيل الخروج', 'fr' => 'Déconnexion', 'en' => 'Logout'],
    'nav.login' => ['ar' => 'تسجيل الدخول', 'fr' => 'Connexion', 'en' => 'Login'],
    'nav.pricing' => ['ar' => 'الباقات', 'fr' => 'Tarification', 'en' => 'Pricing'],
    'nav.terms' => ['ar' => 'الشروط والأحكام', 'fr' => 'Conditions générales', 'en' => 'Terms & Conditions'],
    'nav.privacy' => ['ar' => 'سياسة الخصوصية', 'fr' => 'Confidentialité', 'en' => 'Privacy Policy'],
    'nav.seller_policy' => ['ar' => 'سياسة البائع', 'fr' => 'Politique du vendeur', 'en' => 'Seller Policy'],
    'nav.commission_policy' => ['ar' => 'سياسة العمولات', 'fr' => 'Politique de commission', 'en' => 'Commission Policy']
];

foreach (['ar', 'fr', 'en'] as $lang) {
    $file = __DIR__ . "/resources/lang/{$lang}.json";
    if (file_exists($file)) {
        $json = json_decode(file_get_contents($file), true) ?: [];
        foreach ($translations as $key => $values) {
            $json[$key] = $values[$lang];
        }
        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Fixed nav translations for $lang\n";
    }
}
