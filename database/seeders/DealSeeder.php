<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deal;
use App\Models\User;

class DealSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) return;

        $deals = [
            [
                'user_id' => $admin->id,
                'type' => 'demand',
                'title' => [
                    'ar' => 'أبحث عن 5000 لتر زيت زيتون بكر ممتاز',
                    'en' => 'Searching for 5000L Extra Virgin Olive Oil',
                    'fr' => 'Recherche 5000L d\'Huile d\'Olive Vierge Extra',
                ],
                'description' => [
                    'ar' => 'نحن شركة تصدير نبحث عن كمية كبيرة من الزيت التونسي الأصلي من القيروان أو سيدي بوزيد.',
                    'en' => 'We are an export company looking for a large quantity of authentic Tunisian oil from Kairouan or Sidi Bouzid.',
                    'fr' => 'Nous sommes une société d\'exportation à la recherche d\'une grande quantité d\'huile tunisienne authentique.',
                ],
                'price_range' => '15 - 18 TND',
                'location' => 'Kairouan',
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'user_id' => $admin->id,
                'type' => 'service',
                'title' => [
                    'ar' => 'خدمات عصر الزيتون بأحدث التقنيات',
                    'en' => 'Modern Olive Pressing Services',
                    'fr' => 'Services de Pressage d\'Olives Modernes',
                ],
                'description' => [
                    'ar' => 'معصرتنا توفر لكم عصر بارد وحفظ عالي الجودة لمنتجاتكم. اتصل بنا للحجز.',
                    'en' => 'Our mill provides cold pressing and high-quality storage for your products. Contact us for booking.',
                    'fr' => 'Notre moulin propose un pressage à froid et un stockage de haute qualité.',
                ],
                'price_range' => 'Sur devis',
                'location' => 'Sousse',
                'status' => 'active',
                'is_featured' => false,
            ],
            [
                'user_id' => $admin->id,
                'type' => 'supply',
                'title' => [
                    'ar' => 'عرض خاص: عبوات زيت زيتون 1 لتر بالجملة',
                    'en' => 'Wholesale Offer: 1L Olive Oil Bottles',
                    'fr' => 'Offre de gros : Bouteilles d\'huile d\'olive 1L',
                ],
                'description' => [
                    'ar' => 'متوفر كميات كبيرة من الزيت المعلب للتوزيع المحلي أو التصدير.',
                    'en' => 'Large quantities of bottled oil available for local distribution or export.',
                    'fr' => 'Grandes quantités d\'huile en bouteille disponibles pour la distribution locale ou l\'exportation.',
                ],
                'price_range' => '22 TND / Unit',
                'location' => 'Tunis',
                'status' => 'active',
                'is_featured' => true,
            ],
        ];

        foreach ($deals as $deal) {
            Deal::create($deal);
        }
    }
}
