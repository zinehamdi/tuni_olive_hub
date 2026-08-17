<?php

use App\Models\Article;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$articles = [
    [
        'title' => [
            'ar' => 'سر زيت الزيتون التونسي: مناخ متوسطي فريد',
            'en' => 'The Secret of Tunisian Olive Oil: A Unique Mediterranean Climate',
            'fr' => 'Le secret de l\'huile d\'olive tunisienne : Un climat méditerranéen unique'
        ],
        'category' => [
            'ar' => 'المناخ',
            'en' => 'Climate',
            'fr' => 'Climat'
        ],
        'content' => [
            'ar' => 'موقع تونس الجغرافي الفريد، المحصور بين البحر الأبيض المتوسط والصحراء، يخلق مناخاً محلياً استثنائياً لأشجار الزيتون. ساعات سطوع الشمس الطويلة، إلى جانب الرياح الجافة والشتاء المعتدل، تحمي الأشجار بشكل طبيعي من الآفات والأمراض، مما يسمح بنمو عضوي قوي دون الحاجة إلى مواد كيميائية قاسية. ينتج عن هذا التوازن المثالي زيت زيتون بنقاء لا مثيل له، وحموضة منخفضة، ونكهة غنية ومميزة.',
            'en' => 'Tunisia\'s unique geographic location, nestled between the Mediterranean Sea and the Sahara Desert, creates an exceptional microclimate for olive trees. The extended hours of sunshine, combined with dry winds and mild winters, naturally protect the trees from pests and diseases, allowing for robust, organic growth without the need for harsh chemicals. This perfect balance yields an olive oil with unmatched purity, low acidity, and a distinctive, rich flavor profile.',
            'fr' => 'La situation géographique unique de la Tunisie, nichée entre la mer Méditerranée et le désert du Sahara, crée un microclimat exceptionnel pour les oliviers. Les longues heures d\'ensoleillement, combinées aux vents secs et aux hivers doux, protègent naturellement les arbres des parasites et des maladies, permettant une croissance organique robuste sans produits chimiques agressifs. Cet équilibre parfait donne une huile d\'olive d\'une pureté inégalée, avec une faible acidité et un profil aromatique riche et distinctif.'
        ],
        'image' => 'images/agritech-article-1.jpeg',
        'is_active' => true,
    ],
    [
        'title' => [
            'ar' => 'الفوائد الصحية: لماذا زيت الزيتون التونسي أغنى بالبوليفينول',
            'en' => 'Health Benefits: Why Tunisian Olive Oil is Richer in Polyphenols',
            'fr' => 'Bienfaits pour la santé : Pourquoi l\'huile d\'olive tunisienne est plus riche en polyphénols'
        ],
        'category' => [
            'ar' => 'الصحة',
            'en' => 'Health',
            'fr' => 'Santé'
        ],
        'content' => [
            'ar' => 'أظهرت الدراسات العلمية أن أشجار الزيتون المزروعة في المناخات القاحلة والمشمسة مثل مناخ تونس تنتج زيتوناً بمستويات أعلى بكثير من البوليفينول ومضادات الأكسدة. التعرض المكثف للشمس يجبر شجرة الزيتون على إنتاج هذه المركبات المفيدة كآلية دفاعية. عندما تستهلك زيت الزيتون البكر الممتاز التونسي، فإنك تستفيد من هذه التركيزات العالية، والمعروفة بتقليل الالتهابات، وحماية صحة القلب، وتوفير خصائص قوية مضادة للشيخوخة.',
            'en' => 'Scientific studies have shown that olive trees grown in arid, sunny climates like Tunisia\'s produce olives with significantly higher levels of polyphenols and antioxidants. The intense sun exposure forces the olive tree to produce these beneficial compounds as a defense mechanism. When you consume Tunisian extra virgin olive oil, you are benefiting from these high concentrations, which are known to reduce inflammation, protect heart health, and provide powerful anti-aging properties.',
            'fr' => 'Des études scientifiques ont montré que les oliviers cultivés dans des climats arides et ensoleillés comme celui de la Tunisie produisent des olives avec des niveaux nettement plus élevés de polyphénols et d\'antioxydants. L\'exposition intense au soleil oblige l\'olivier à produire ces composés bénéfiques comme mécanisme de défense. Lorsque vous consommez de l\'huile d\'olive extra vierge tunisienne, vous bénéficiez de ces fortes concentrations, connues pour réduire l\'inflammation, protéger la santé cardiaque et offrir de puissantes propriétés anti-âge.'
        ],
        'image' => 'images/agritech-article-2.jpeg',
        'is_active' => true,
    ],
    [
        'title' => [
            'ar' => 'القطرة الذهبية: كيف تخلق التربة والغلاف الجوي جودة حائزة على جوائز',
            'en' => 'The Golden Drop: How Soil and Atmosphere Create Award-Winning Quality',
            'fr' => 'La goutte d\'or : Comment le sol et l\'atmosphère créent une qualité primée'
        ],
        'category' => [
            'ar' => 'الجودة',
            'en' => 'Quality',
            'fr' => 'Qualité'
        ],
        'content' => [
            'ar' => 'الأمر لا يقتصر على الشمس فحسب؛ بل التربة أيضاً. غالباً ما تزدهر بساتين الزيتون التونسية في تربة رملية جيدة التصريف تجبر الجذور على الحفر عميقاً بحثاً عن العناصر الغذائية والماء. هذا الكفاح يخلق زيتوناً أكثر كثافة وألذ طعماً. بالاقتران مع الغلاف الجوي الخالي من التلوث في المناطق الزراعية الريفية، فإن الزيت الناتج هو تعبير نقي عن الطبيعة. هذا هو السبب في أن زيوت الزيتون التونسية تفوز باستمرار بالميداليات الذهبية في مسابقات التذوق الدولية في جميع أنحاء العالم.',
            'en' => 'It\'s not just the sun; it\'s the soil. Tunisian olive groves often thrive in sandy, well-drained soils that force the roots to dig deep for nutrients and water. This struggle creates a denser, more flavorful olive. Combined with the pollution-free atmosphere of rural agricultural regions, the resulting oil is a pure expression of nature. This is why Tunisian olive oils consistently win gold medals at international tasting competitions across the globe.',
            'fr' => 'Il n\'y a pas que le soleil ; il y a aussi le sol. Les oliveraies tunisiennes prospèrent souvent dans des sols sablonneux et bien drainés qui obligent les racines à creuser profondément pour trouver des nutriments et de l\'eau. Cette lutte crée une olive plus dense et plus savoureuse. Combinée à l\'atmosphère sans pollution des régions agricoles rurales, l\'huile qui en résulte est une pure expression de la nature. C\'est pourquoi les huiles d\'olive tunisiennes remportent régulièrement des médailles d\'or lors de concours internationaux de dégustation à travers le monde.'
        ],
        'image' => 'images/agritech-article-3.jpeg',
        'is_active' => true,
    ],
];

foreach ($articles as $a) {
    Article::create($a);
}

echo "Articles seeded.\n";
