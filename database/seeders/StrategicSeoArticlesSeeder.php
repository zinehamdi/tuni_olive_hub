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
                'image' => 'images/hero_slide_1.png',
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
                    'fr' => 'Huile d\'Olive en Marque Privée en Tunisie : Guide du Co-Packing & Embouteillage',
                    'ar' => 'تصنيع وتعبئة زيت الزيتون بالعلامة الخاصة في تونس: دليل الشركات والمستوردين',
                ],
                'category' => [
                    'en' => 'Private Label',
                    'fr' => 'Marque Privée',
                    'ar' => 'العلامة الخاصة',
                ],
                'image' => 'images/hero_slide_2.png',
                'is_active' => true,
                'content' => [
                    'en' => "Launching a private label olive oil brand allows food retailers, gourmet brands, and hospitality groups to market high-grade Mediterranean EVOO under their own signature identity.\n\n"
                        . "### Advantages of Co-Packing in Tunisia\n"
                        . "1. **Cost Efficiency**: Turnkey production costs in Tunisia provide high margins compared to European packaging facilities.\n"
                        . "2. **Custom Cultivar Profiles**: Select between smooth Chemlali (central and southern Tunisia) and robust, peppery Chetoui (northern Tunisia).\n"
                        . "3. **Packaging Versatility**: Automated bottling lines support 250ml, 500ml, 750ml, 1L bottles, and 3L/5L tins with nitrogen injection to guarantee freshness.\n\n"
                        . "Explore accredited packaging units on ZinToop: https://zintoop.com/en/private-label-olive-oil-tunisia",
                    'fr' => "Le développement d'une marque privée (Private Label) d'huile d'olive en Tunisie permet aux distributeurs et épiceries fines de proposer une huile d'excellence sous leur propre marque.\n\n"
                        . "### Les Avantages du Co-Packing en Tunisie\n"
                        . "- Maîtrise des coûts de mise en bouteille.\n"
                        . "- Sélection des profils variétaux (Chemlali fruité doux ou Chétoui corsé).\n"
                        . "- Lignes d'embouteillage modernes avec injection d'azote pour préserver les arômes.\n\n"
                        . "Consultez nos services de conditionnement : https://zintoop.com/fr/marque-privee-huile-olive-tunisie",
                    'ar' => "يتيح تصنيع زيت الزيتون بالعلامة الخاصة (Private Label) للشركات وسلاسل التوزيع إطلاق علامتها التجارية الخاصة بزيت بكر ممتاز تونسي عالي الجودة.\n\n"
                        . "### ميزات التعبئة في تونس:\n"
                        . "- تكلفة تصنيع وتعبئة تنافسية للغاية.\n"
                        . "- اختيار الأصناف المناسبة (شملالي ناعم أو شتاوي غني بمضادات الأكسدة).\n"
                        . "- خطوط تعبئة آلية حديثة بالغاز الخامل للحفاظ على الجودة.\n\n"
                        . "اكتشف خدمات التعبئة المعتمدة: https://zintoop.com/ar/علامة-خاصة-زيت-زيتون-تونس",
                ],
            ],
            [
                'id' => 3,
                'title' => [
                    'en' => 'Tunisian Olive Oil Price Outlook 2026: Trends, Data & Market Factors',
                    'fr' => 'Prix de l\'Huile d\'Olive en Tunisie 2026 : Tendances du Marché & Facteurs Clés',
                    'ar' => 'مؤشرات وتوقعات أسعار زيت الزيتون في تونس 2026: تحليل السوق والبورصة',
                ],
                'category' => [
                    'en' => 'Market Intelligence',
                    'fr' => 'Marché & Cours',
                    'ar' => 'أسعار السوق',
                ],
                'image' => 'images/hero_slide_3.png',
                'is_active' => true,
                'content' => [
                    'en' => "Olive oil pricing in Tunisia is driven by seasonal climate patterns, Mediterranean harvest yields across Spain and Italy, and international export demand.\n\n"
                        . "### Regional Price Dynamics\n"
                        . "Prices vary noticeably across Tunisian governorates based on local milling capacity and olive cultivar:\n"
                        . "- **Sfax & Central Souks**: Major national trading hub with high liquidity for Chemlali oil.\n"
                        . "- **Kairouan & Sahel**: Intense local and export demand for early-harvest extra virgin lots.\n"
                        . "- **Northern Regions (Baja, Zaghouan, Bizerte)**: Renowned for intense, green-fruity Chetoui oil commanding premium prices.\n\n"
                        . "Check live regional prices updated daily on ZinToop: https://zintoop.com/en/olive-oil-prices",
                    'fr' => "Les cours de l'huile d'olive en Tunisie sont influencés par la pluviométrie, le volume de récolte en Espagne et en Italie, ainsi que la demande internationale à l'exportation.\n\n"
                        . "Consultez les cours régionaux en direct : https://zintoop.com/fr/prix-huile-olive-tunisie",
                    'ar' => "تتأثر أسعار زيت الزيتون في تونس بحجم الصابة الوطنية، والظروف المناخية في حوض المتوسط، ومستويات الطلب العالمي للتصدير.\n\n"
                        . "تابع الأسعار الحية المحدثة يومياً حسب الولايات: https://zintoop.com/ar/أسعار-زيت-الزيتون-تونس",
                ],
            ],
            [
                'id' => 4,
                'title' => [
                    'en' => 'Chemlali vs Chetoui: Sourcing the Right Tunisian Olive Oil Variety',
                    'fr' => 'Chemlali vs Chétoui : Quelle Variété d\'Huile d\'Olive Tunisienne Choisir ?',
                    'ar' => 'مقارنة بين زيت الشملالي والشتاوي: دليلك لاختيار الصنف التونسي الأنسب',
                ],
                'category' => [
                    'en' => 'Varieties & Quality',
                    'fr' => 'Variétés & Qualité',
                    'ar' => 'الأصناف والجودة',
                ],
                'image' => 'images/hero_slide_1.png',
                'is_active' => true,
                'content' => [
                    'en' => "Tunisia is home to two primary flagship olive cultivars that account for over 90% of national production:\n\n"
                        . "### 1. Chemlali (الشملالي)\n"
                        . "- **Geography**: Central and Southern Tunisia (Sfax, Kairouan, Sahel, Zarzis).\n"
                        . "- **Profile**: Delicate, sweet, and mild with fruity almond and golden apple notes.\n"
                        . "- **Best for**: Global blending, everyday culinary use, and international palates seeking low bitterness.\n\n"
                        . "### 2. Chetoui (الشتوي)\n"
                        . "- **Geography**: Northern Tunisia (Baja, Jendouba, Bizerte, Kef).\n"
                        . "- **Profile**: Robust, peppery, herbaceous with rich green-artichoke notes.\n"
                        . "- **Best for**: Premium monovarietal bottling and high-antioxidant health positioning.\n\n"
                        . "Discover variety listings on ZinToop: https://zintoop.com/en/catalog",
                    'fr' => "La Tunisie compte deux variétés majeures représentant plus de 90% de son oliveraie :\n\n"
                        . "- **Le Chemlali** : Originaire du Centre et du Sud (Sfax, Kairouan, Sahel), il offre un goût fruité doux, rond et équilibré.\n"
                        . "- **Le Chétoui** : Cultivé au Nord (Béja, Bizerte, Le Kef), il se caractérise par une intensité herbacée remarquable et une très forte teneur en polyphénols.",
                    'ar' => "تمتلك تونس صنفين رئيسيين يمثلان أكثر من 90% من بساتين الزيتون:\n\n"
                        . "- **الشملالي**: صنف الوسط والجنوب (صفاقس، القيروان، الساحل، جرجيس)، يتميز بمذاق ناعم وفواكه ناضجة ومثالي للخلط التجاري.\n"
                        . "- **الشتاوي**: صنف الشمال التونسي (باجة، جندوبة، بنزرت، الكاف)، يتميز بنكهة عشبية قوية ومرارة خفيفة فاخرة وغنى فائق بمضادات الأكسدة.",
                ],
            ],
            [
                'id' => 5,
                'title' => [
                    'en' => 'Organic Extra Virgin Olive Oil from Tunisia: Certified Bio Sourcing',
                    'fr' => 'Huile d\'Olive Extra Vierge Biologique de Tunisie : Normes & Certification Bio',
                    'ar' => 'زيت الزيتون البيولوجي البكر الممتاز في تونس: الشهادات ومزايا التصدير',
                ],
                'category' => [
                    'en' => 'Organic & Bio',
                    'fr' => 'Bio & Organique',
                    'ar' => 'الزيت العضوي',
                ],
                'image' => 'images/hero_slide_2.png',
                'is_active' => true,
                'content' => [
                    'en' => "Tunisia has established itself as the world's leading exporter of certified organic olive oil outside Europe, with over 300,000 hectares of certified organic olive groves.\n\n"
                        . "### Key International Certifications\n"
                        . "- **EU Organic (Euro-feuille)**: Full equivalence with European organic farming regulations.\n"
                        . "- **USDA NOP (National Organic Program)**: For the United States and North American markets.\n"
                        . "- **JAS (Japanese Agricultural Standards)** & Bio Suisse certification.\n\n"
                        . "Filter organic certified lots on ZinToop: https://zintoop.com/en/bulk-tunisian-olive-oil?organic=1",
                    'fr' => "La Tunisie est le premier exportateur mondial d'huile d'olive biologique avec plus de 300 000 hectares d'oliveraies certifiées bio sans engrais chimiques ni pesticides.",
                    'ar' => "تعد تونس المصدر الأول لزيت الزيتون العضوي (البيولوجي) في العالم بمساحات تتجاوز 300 ألف هكتار من البساتين الطبيعية الحاصلة على شهادات المطابقة الأوروبية والأمريكية.",
                ],
            ],
            [
                'id' => 6,
                'title' => [
                    'en' => 'FOB vs CIF Terms: Shipping Olive Oil from Tunisian Commercial Ports',
                    'fr' => 'Incoterms FOB vs CIF : Expédition d\'Huile d\'Olive depuis les Ports Tunisiens',
                    'ar' => 'شروط الشحن الدولي FOB و CIF لتصدير زيت الزيتون من الموانئ التونسية',
                ],
                'category' => [
                    'en' => 'Logistics & Shipping',
                    'fr' => 'Logistique & Transit',
                    'ar' => 'الشحن واللوجستيك',
                ],
                'image' => 'images/hero_slide_3.png',
                'is_active' => true,
                'content' => [
                    'en' => "When executing international olive oil contracts from Tunisia, selecting the appropriate Incoterm determines liability, insurance, and freight costs:\n\n"
                        . "- **FOB (Free on Board - Rades / Sfax)**: The seller covers domestic trucking, customs clearance, and loading onto the vessel. The buyer handles ocean freight and destination import tariffs.\n"
                        . "- **CIF (Cost, Insurance & Freight)**: The seller arranges and pays for ocean transport and maritime cargo insurance up to the buyer's destination port.\n\n"
                        . "Explore logistics and transit services on ZinToop: https://zintoop.com/en/servicehub",
                    'fr' => "Le choix entre FOB (Free On Board) et CIF (Cost, Insurance and Freight) est capital lors de la conclusion de contrats d'exportation d'huile d'olive au départ des ports de Radès et Sfax.",
                    'ar' => "يحدد اختيار شرط الشحن (FOB أو CIF) مسؤولية النقل البحري والتأمين في عقود تصدير زيت الزيتون التونسي.",
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
                'image' => 'images/hero_slide_1.png',
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
                'image' => 'images/hero_slide_2.png',
                'is_active' => true,
                'content' => [
                    'en' => "In Tunisia, olive oil extraction yield is traditionally measured in 'Tabouiz' (التبويز) — the amount of olive fruit (in kg or wiba) required to produce a standard measure of olive oil.\n\n"
                        . "Calculate your harvest extraction return and list your oil directly on ZinToop: https://zintoop.com/en/catalog",
                    'fr' => "Le 'Tabouiz' désigne en Tunisie le taux de rendement de l'extraction de l'huile d'olive à partir du poids d'olives triturées au moulin.",
                    'ar' => "التبويز هو المصطلح التونسي التقليدي لحساب نسبة استخراج الزيت من الزيتون المعصور، وهو المقياس الأساسي لتحديد مردودية الصابة وأرباح الفلاحين والمعاصر.",
                ],
            ],
        ];

        foreach ($articles as $data) {
            Article::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
