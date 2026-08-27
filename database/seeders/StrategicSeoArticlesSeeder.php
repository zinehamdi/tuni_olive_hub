<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;

class StrategicSeoArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'id' => 1,
                'title' => [
                    'en' => 'How to Import Olive Oil from Tunisia: A Complete B2B Sourcing Guide',
                    'fr' => 'Guide Complet : Comment Importer de l\'Huile d\'Olive de Tunisie (Sourcing B2B)',
                    'ar' => 'الدليل الشامل لاستيراد وتصدير زيت الزيتون التونسي: شروط وخطوات الشراء B2B',
                ],
                'category' => [
                    'en' => 'Export & Trade',
                    'fr' => 'Export & Commerce',
                    'ar' => 'التصدير والتجارة',
                ],
                'image' => 'images/articles/import_b2b_sourcing.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "Tunisia stands as one of the world's leading producers and the foremost exporter of organic olive oil globally. For international importers, distributors, restaurant chains, and food manufacturers, sourcing Tunisian extra virgin olive oil (EVOO) represents an outstanding strategic advantage in terms of cost-efficiency, chemical purity, and high polyphenol content.\n\n"
                        . "### 1. Understanding Bulk vs Bottled Sourcing\n"
                        . "International buyers can choose between two main supply models on ZinToop:\n"
                        . "- **Bulk Shipments (Vrac)**: Loaded in 24,000-liter Flexitanks, 1,000-liter IBC containers, or 200L food-grade drums from the major commercial ports of Sfax, Rades, or Sousse.\n"
                        . "- **Bottled & Private Label**: Fully packaged in dark glass bottles (Marasca, Dorica) or metallic tins under your custom brand with international barcode compliance.\n\n"
                        . "### 2. Export Compliance and ONH Inspection\n"
                        . "Every export batch from Tunisia undergoes strict chemical and organoleptic analysis conducted by accredited laboratories under the supervision of the National Oil Board (Office National de l'Huile - ONH). Required export documentation includes:\n"
                        . "- Official Certificate of Analysis (Acidity, Peroxide value, K232/K270 coefficients).\n"
                        . "- Certificate of Origin & EUR.1 Movement Certificate (for tariff preferences).\n"
                        . "- Phytosanitary and health certificates issued by the Ministry of Agriculture.\n\n"
                        . "### 3. How ZinToop Streamlines Direct B2B Purchasing\n"
                        . "Through the ZinToop ecosystem, buyers can bypass unnecessary broker intermediaries, review verified producer profiles, access real-time regional mill prices, and request verified laboratory certificates directly from local producers.\n\n"
                        . "Access our live marketplace: https://zintoop.com/en/bulk-tunisian-olive-oil",
                    'fr' => "La Tunisie figure parmi les leaders mondiaux de la production oléicole et se classe premier exportateur mondial d'huile d'olive biologique hors Union Européenne. Pour les importateurs, négociants et chaînes de distribution, l'huile d'olive extra vierge tunisienne offre une exceptionnelle stabilité et une richesse remarquable en antioxydants.\n\n"
                        . "### 1. Sourcing en Vrac ou Bouteilles Conditionnées\n"
                        . "Les acheteurs internationaux peuvent structurer leurs commandes selon deux formats principaux :\n"
                        . "- **Expéditions en Vrac (Bulk)** : Flexitanks de 24 000 litres, conteneurs IBC de 1 000 litres ou fûts métalliques au départ des ports de Sfax et Radès.\n"
                        . "- **Conditionnement sous Marque Privée** : Bouteilles en verre anti-UV (Marasca, Dorica) ou bidons 5L étiquetés selon les normes de votre marché de destination.\n\n"
                        . "### 2. Contrôles Qualité et Certification ONH\n"
                        . "Chaque lot destiné à l'exportation est soumis à des analyses physico-chimiques et sensorielles strictes encadrées par l'Office National de l'Huile (ONH) et les laboratoires agréés.\n\n"
                        . "Découvrez les offres en vrac disponibles : https://zintoop.com/fr/huile-olive-tunisienne-en-vrac",
                    'ar' => "تعتبر تونس من أبرز رواد إنتاج وتصدير زيت الزيتون في العالم، والمصدر الأول عالمياً لزيت الزيتون البيولوجي خارج الاتحاد الأوروبي. يوفر زيت الزيتون البكر الممتاز التونسي للمستوردين والتجار الدوليين ميزة تنافسية كبرى بفضل نسب البوليفينول العالية وجودة الطعم.\n\n"
                        . "### 1. خيارات الشراء: السائب (Bulk) أو المعبأ (Bottled)\n"
                        . "- **الشحن السائب**: في صهاريج Flexitanks سعة 24,000 لتر أو حاويات IBC سعة 1,000 لتر عبر موانئ صفاقس ورادس.\n"
                        . "- **التعبئة بالعلامة الخاصة**: عبوات زجاجية معتمة أو صفائح 5 لتر مطابقة لمعايير الأسواق المستهدفة.\n\n"
                        . "### 2. الفحص والمطابقة الجمركية\n"
                        . "تخضع جميع الشحنات لتحاليل مخبرية دقيقة تحت إشراف ديوان الزيت لضمان نسب الحموضة والمطابقة الدولية.\n\n"
                        . "تصفح عروض الجملة المباشرة: https://zintoop.com/ar/زيت-الزيتون-التونسي-بالجملة",
                ],
            ],
            [
                'id' => 2,
                'title' => [
                    'en' => 'Private Label & Contract Bottling Olive Oil in Tunisia: Complete Guide',
                    'fr' => 'Marque Privée et Conditionnement d\'Huile d\'Olive en Tunisie : Guide B2B',
                    'ar' => 'العلامة الخاصة والتعبئة لزيت الزيتون التونسي: دليل الشركات والموزعين',
                ],
                'category' => [
                    'en' => 'Private Label',
                    'fr' => 'Marque Privée',
                    'ar' => 'العلامة الخاصة',
                ],
                'image' => 'images/articles/private_label_bottles.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "Private labeling (OEM / White Label) allows international retailers, gourmet brands, and supermarket distributors to bottle authentic Tunisian Extra Virgin Olive Oil under their own brand identity.\n\n"
                        . "### Packaging Options\n"
                        . "- **Marasca & Dorica Dark Glass**: 250ml, 500ml, 750ml, 1L.\n"
                        . "- **Lithographed Tin Cans**: 1L, 3L, 5L with tamper-evident pourers.\n"
                        . "- **Bag-in-Box (BiB)**: Modern 3L and 5L oxygen-barrier packaging.\n\n"
                        . "Learn more about our certified packaging partners: https://zintoop.com/en/private-label-olive-oil-tunisia",
                    'fr' => "Le conditionnement sous marque privée (Private Label) permet aux distributeurs et marques internationales de commercialiser de l'huile d'olive extra vierge tunisienne d'excellence sous leur propre étiquette.",
                    'ar' => "تتيح خدمات التعبئة بالعلامة الخاصة (White Label / Private Label) للموزعين والشركات العالمية تعبئة أجود أنواع زيت الزيتون التونسي البكر الممتاز بعبوات مخصصة وهوية بصرية تحمل علامتهم التجارية.",
                ],
            ],
            [
                'id' => 3,
                'title' => [
                    'en' => 'Tunisian Olive Oil Price Outlook 2026: Trends, Data & Market Factors',
                    'fr' => 'Perspectives et Prix de l\'Huile d\'Olive en Tunisie 2026 : Analyse du Marché',
                    'ar' => 'توقعات أسعار زيت الزيتون في تونس 2026: تحليل السوق والإنتاج والأسعار',
                ],
                'category' => [
                    'en' => 'Market Analysis',
                    'fr' => 'Analyse du Marché',
                    'ar' => 'تحليل السوق',
                ],
                'image' => 'images/articles/market_price_charts.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "The 2025/2026 Tunisian olive oil campaign is characterized by strong export momentum, favorable climatic conditions in central regions, and steady international demand across European and North American markets.\n\n"
                        . "Track daily live souk prices across all Tunisian governorates: https://zintoop.com/en/prices",
                    'fr' => "La campagne oléicole 2025/2026 en Tunisie s'annonce prometteuse avec une reprise des volumes et une forte attractivité sur les marchés d'exportation.",
                    'ar' => "تحليل شامل لاتجاهات أسعار زيت الزيتون في تونس للموسم الجديد، وعلاقة الأسعار المحلية ببورصة إسبانيا (Jaén) وإيطاليا والطلب العالمي على التصدير.",
                ],
            ],
            [
                'id' => 4,
                'title' => [
                    'en' => 'Chemlali vs Chetoui: Sourcing the Right Tunisian Olive Oil Variety',
                    'fr' => 'Chemlali vs Chétoui : Guide des Variétés d\'Huile d\'Olive Tunisienne',
                    'ar' => 'مقارنة الشملالي والشتوي: دليلك لاختيار صنف زيت الزيتون التونسي المناسب',
                ],
                'category' => [
                    'en' => 'Cultivars & Taste',
                    'fr' => 'Variétés & Terroir',
                    'ar' => 'الأصناف والجودة',
                ],
                'image' => 'images/articles/chemlali_vs_chetoui.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "Tunisia boasts two primary native olive varieties that account for over 90% of the national olive forest:\n\n"
                        . "- **Chemlali (الشتوي / الوسط والجنوب)**: Dominates Sfax, Sahel, and southern regions. Produces a golden, balanced, fruity oil with subtle almond notes, perfect for international blending.\n"
                        . "- **Chetoui (الشمال)**: Grown in northern valleys. Renowned for its intense green color, robust herbal aroma, and exceptionally high polyphenol count (antioxidants).\n\n"
                        . "Explore varieties guide: https://zintoop.com/en/olive-varieties",
                    'fr' => "Le Chemlali (Centre et Sud) et le Chétoui (Nord) constituent les deux piliers de l'oléiculture tunisienne, offrant des profils aromatiques uniques adaptés à tous les besoins culinaires et industriels.",
                    'ar' => "مقارنة دقيقة بين الشملالي (الأكثر انتشاراً بالوسط والجنوب ويمتاز بطعمه المتوازن المناسب للمزج) والشتوي (شمال تونس، غني جداً بمضادات الأكسدة والبوليفينول والحدة المحبوبة).",
                ],
            ],
            [
                'id' => 5,
                'title' => [
                    'en' => 'Organic Extra Virgin Olive Oil from Tunisia: Certified Bio Sourcing',
                    'fr' => 'Huile d\'Olive Biologique Tunisienne : Certification et Export Bio',
                    'ar' => 'زيت الزيتون العضوي (البيولوجي) التونسي: فرص التصدير والشهادات المعتمدة',
                ],
                'category' => [
                    'en' => 'Organic (Bio)',
                    'fr' => 'Bio & Terroir',
                    'ar' => 'الزيت البيولوجي',
                ],
                'image' => 'images/articles/organic_bio_olive_oil.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "With more than 250,000 hectares of certified organic olive groves, Tunisia is the world leader in certified organic olive oil exports. Natural arid conditions in central and southern Tunisia naturally protect trees from pests without synthetic pesticides.\n\n"
                        . "Browse verified organic producers: https://zintoop.com/en/tunisian-olive-oil-suppliers",
                    'fr' => "Leader mondial des exportations d'huile d'olive bio hors UE, la Tunisie bénéficie d'un climat naturellement protecteur qui limite les traitements chimiques.",
                    'ar' => "تونس هي المصدر الأول عالمياً لزيت الزيتون البيولوجي خارج الاتحاد الأوروبي بمساحة تتجاوز 250 ألف هكتار من الغابات المعتمدة عضوياً بشهادات دولية.",
                ],
            ],
            [
                'id' => 6,
                'title' => [
                    'en' => 'FOB vs CIF Terms: Shipping Olive Oil from Tunisian Commercial Ports',
                    'fr' => 'Incoterms FOB vs CIF : Logistique Maritime d\'Export d\'Huile d\'Olive en Tunisie',
                    'ar' => 'شحن زيت الزيتون من موانئ تونس: الفارق بين عقود FOB و CIF وشروط Flexitank',
                ],
                'category' => [
                    'en' => 'Logistics & Shipping',
                    'fr' => 'Logistique & Ports',
                    'ar' => 'اللوجستيك والشحن',
                ],
                'image' => 'images/articles/shipping_flexitank_ports.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "When contracting olive oil shipments from Tunisian ports (Sfax, Rades, Bizerte, Sousse), international buyers must clearly establish Incoterms:\n\n"
                        . "- **FOB (Free on Board)**: Seller handles customs clearance, ONH certificate, and loads onto the vessel. Buyer manages sea freight.\n"
                        . "- **CIF (Cost, Insurance & Freight)**: Seller covers shipping and marine insurance to the destination port.\n\n"
                        . "Connect with logistics partners on ZinToop: https://zintoop.com/en/servicehub",
                    'fr' => "Comprendre les Incoterms FOB et CIF pour sécuriser vos importations maritimes d'huile d'olive tunisienne depuis les ports de Sfax et Radès.",
                    'ar' => "دليل المصدر والمستورد لفهم شروط الشحن البحري Incoterms والفرق بين تسليم الميناء التونسي (FOB) والتسليم مع التأمين والشحن حتى ميناء الوصول (CIF).",
                ],
            ],
            [
                'id' => 7,
                'title' => [
                    'en' => 'Tunisian Olive Oil Export Regulations & ONH Guidelines 2026',
                    'fr' => 'Guide Réglementaire : Procédures et Cahier des Charges Export ONH 2026',
                    'ar' => 'كراس الشروط والإجراءات الرسمية لتصدير زيت الزيتون التونسي 2026',
                ],
                'category' => [
                    'en' => 'Regulations & Legal',
                    'fr' => 'Réglementation',
                    'ar' => 'القوانين والإجراءات',
                ],
                'image' => 'images/articles/onh_lab_analysis_inspection.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "Exporting olive oil from Tunisia requires adherence to the national export regulatory framework established by the Ministry of Trade and the National Oil Board (ONH).\n\n"
                        . "Consult our legal and export advisory desk: https://zintoop.com/en/services/pricing",
                    'fr' => "L'exportation d'huile d'olive depuis la Tunisie est encadrée par un cahier des charges officiel garantissant la traçabilité et la qualité des lots exportés.",
                    'ar' => "دليل شامل للمصدرين وأصحاب المعاصر حول كراس شروط تصدير زيت الزيتون والتحاليل الإلزامية لدى ديوان الزيت والديوانة التونسية.",
                ],
            ],
            [
                'id' => 8,
                'title' => [
                    'en' => 'Understanding Tabouiz: Olive Oil Extraction Yield Calculation in Tunisia',
                    'fr' => 'Comprendre le Tabouiz : Calcul du Rendement d\'Extraction d\'Huile d\'Olive en Tunisie',
                    'ar' => 'دليل التبويز وحساب نسبة استخراج ومردودية زيت الزيتون في تونس',
                ],
                'category' => [
                    'en' => 'Farming & Milling',
                    'fr' => 'Production & Moulin',
                    'ar' => 'الفلاحة والمعاصر',
                ],
                'image' => 'images/articles/tabouiz_yield_calculation.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "In Tunisia, olive oil extraction yield is traditionally measured in 'Tabouiz' (التبويز) — the amount of olive fruit (in kg or wiba) required to produce a standard measure of olive oil.\n\n"
                        . "Calculate your harvest extraction return and list your oil directly on ZinToop: https://zintoop.com/en/catalog",
                    'fr' => "Le 'Tabouiz' désigne en Tunisie le taux de rendement de l'extraction de l'huile d'olive à partir du poids d'olives triturées au moulin.",
                    'ar' => "التبويز هو المصطلح التونسي التقليدي لحساب نسبة استخراج الزيت من الزيتون المعصور، وهو المقياس الأساسي لتحديد مردودية الصابة وأرباح الفلاحين والمعاصر.",
                ],
            ],
            [
                'id' => 9,
                'title' => [
                    'en' => 'The Ultimate B2B Sourcing Marketplace for Tunisian Olive Oil',
                    'fr' => 'La Marketplace B2B de Référence pour l\'Huile d\'Olive Tunisienne',
                    'ar' => 'سوق المعاملات المباشرة B2B لزيت الزيتون التونسي: الموردون والمطاعم والفنادق',
                ],
                'category' => [
                    'en' => 'B2B Marketplace',
                    'fr' => 'Marketplace B2B',
                    'ar' => 'سوق المعاملات',
                ],
                'image' => 'images/articles/b2b_marketplace_trading.jpg',
                'is_active' => true,
                'content' => [
                    'en' => "How ZinToop connects certified Tunisian olive oil mills, packaging units, and organic farmers directly with international buyers, HORECA chains, and food distributors worldwide.\n\n"
                        . "Join our B2B trade network today: https://zintoop.com/en/catalog",
                    'fr' => "Découvrez comment ZinToop digitalise la filière oléicole tunisienne en connectant directement moulins, conditionneurs et acheteurs internationaux.",
                    'ar' => "كيف تساهم منصة ZinToop في ربط معاصر الزيتون ووحدات التعبئة والفلاحين مباشرة بكبرى الفنادق وسلاسل المطاعم والمشترين الدوليين دون وسطاء.",
                ],
            ],
        ];

        foreach ($articles as $data) {
            Article::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
