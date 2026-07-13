<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketingService;

class MarketingServicesSeeder extends Seeder
{
    public function run()
    {
        MarketingService::updateOrCreate(['id' => 1], [
            'title_ar' => 'تنمية الصفحات (Growth)',
            'title_en' => 'Page Growth',
            'title_fr' => 'Croissance de la page',
            'price_tnd_weekly' => 215.00,
            'currency' => 'TND',
            'icon_url' => '🌱',
            'results_ar' => '100 إلى 150 متابع يومياً.',
            'results_en' => '100 to 150 followers daily.',
            'results_fr' => '100 à 150 abonnés par jour.',
        ]);

        MarketingService::updateOrCreate(['id' => 2], [
            'title_ar' => 'حملة المراسلات (Messaging)',
            'title_en' => 'Messaging Campaign',
            'title_fr' => 'Campagne de messages',
            'price_tnd_weekly' => 260.00,
            'currency' => 'TND',
            'icon_url' => '💬',
            'results_ar' => '30 إلى 100 رسالة إجمالاً.',
            'results_en' => '30 to 100 messages total.',
            'results_fr' => '30 à 100 messages au total.',
        ]);

        MarketingService::updateOrCreate(['id' => 3], [
            'title_ar' => 'المكالمات الهاتفية (Calls)',
            'title_en' => 'Phone Calls',
            'title_fr' => 'Appels téléphoniques',
            'price_tnd_weekly' => 350.00,
            'currency' => 'TND',
            'icon_url' => '📞',
            'results_ar' => 'حوالي 15 مكالمة من حرفاء مهتمين.',
            'results_en' => 'Around 15 calls from interested clients.',
            'results_fr' => 'Environ 15 appels de clients intéressés.',
        ]);

        MarketingService::updateOrCreate(['id' => 4], [
            'title_ar' => 'باقة المواقع والمتاجر الاحترافية (Web/Store Pro)',
            'title_en' => 'Pro Web/Store Package',
            'title_fr' => 'Pack Web/Boutique Pro',
            'price_tnd_weekly' => 550.00,
            'currency' => 'TND',
            'icon_url' => '🛍️',
            'results_ar' => '1000 زيارة للموقع / 20 مبيعة مؤكدة.',
            'results_en' => '1000 site visits / 20 confirmed sales.',
            'results_fr' => '1000 visites du site / 20 ventes confirmées.',
        ]);

        MarketingService::updateOrCreate(['id' => 5], [
            'title_ar' => 'تصميم علامة تجارية وملصق (Brand & Label)',
            'title_en' => 'Brand & Label Design',
            'title_fr' => 'Design de marque & étiquette',
            'price_tnd_weekly' => 300.00,
            'currency' => 'TND',
            'icon_url' => '🎨',
            'results_ar' => 'هوية بصرية كاملة وملصق جاهز للطباعة.',
            'results_en' => 'Complete visual identity and print-ready label.',
            'results_fr' => 'Identité visuelle complète et étiquette prête à imprimer.',
        ]);
    }
}
