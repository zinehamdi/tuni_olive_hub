<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;

$updates = [
    2 => [
        'en' => [
            'title' => "Historical Depth, National Pride",
            'content' => "🇹🇳 The Tunisian Olive Tree: The Legacy of Prophets, the Trust of Ancestors, and the Bet of the Future\r\nIt is not just a tree, it is the living memory of a land that has known no defeat. From the \"Akkari Olive Tree\" in Zarzis, defying time for over 2,500 years, to the majestic northern forests, Tunisia tells a story of civilization kneaded with oil and soil.\r\n### 📜 The Historical Plot: A Land Named After Its Tree\r\nThe name of Tunisia is closely linked to the **\"Olive Tree\" (Zitouna)**. Naming the great **\"Zitouna Mosque\"** was not just a coincidence or a blessing of the place; it was an acknowledgment that this land is the \"Land of the Olive Tree\" par excellence.\r\n * **In the Carthaginian Era:** \"Hannibal\" made planting olives a national duty for his soldiers to populate the land, believing that economic power is the pillar of military power.\r\n * **In the Roman Era:** Tunisia was nicknamed the \"Granary of Rome\", not only for wheat, but thanks to the oil of \"Sbeitla\" and \"Thysdrus\" (El Jem) that illuminated the palaces of the empire.\r\nOur ancestors believed that \"everyone has a share of their name\", so our share of glory was to be the guardians of this blessed tree mentioned by God in His book.\r\n### 🥈 Tunisia.. The Giant Reclaiming Its Place\r\nToday, Tunisia is not just a secondary player; we are the **second largest global producer of olive oil**. This number is not just a statistic, it is a \"cry of alarm\" to competitors and a proof of merit to the world.\r\nWe own more than **100 million olive trees**, and each tree is like a renewable oil well, inexhaustible and non-polluting, but rather life-giving.\r\n### 💰 Green Gold: The New Engine of the Dinar\r\nIn modern numbers, a barrel of Tunisian olive oil is considered **\"Green Gold\"**. Valuing this product is not an option, but a battle for economic independence:\r\n * **World-Class Quality:** Tunisian oil annually wins hundreds of gold medals in international quality competitions (from New York to Tokyo).\r\n * **Export Power:** The barrel of oil is the real bond that will make the Tunisian Dinar soar. When we move from exporting bulk oil in large quantities to exporting it packaged with luxury Tunisian brands, we are selling the \"prestige of a state\", not just a liquid.\r\n### 🌿 Our Call to You\r\nThe presence of the olive tree on our land is a \"certificate of blessing\". Our role today, each from their position, is to value this wealth. Whether you are a farmer who realizes the value of \"pure oil\", an exporter knocking on the doors of global markets, or a consumer proud of their country's product.\r\nWe are great, our history is a witness, and our future is written by this golden liquid flowing from the heart of our sands and our north.\r\n**Tunisia is the Olive Tree, and the Olive Tree is Tunisia.. have we realized the size of the treasure in our hands?**"
        ],
        'fr' => [
            'title' => "Profondeur historique, fierté nationale",
            'content' => "🇹🇳 L'Olivier Tunisien : L'héritage des prophètes, la confiance des ancêtres et le pari de l'avenir\r\nCe n'est pas seulement un arbre, c'est la mémoire vivante d'une terre qui n'a connu aucune défaite. De \"l'olivier Akkari\" à Zarzis, défiant le temps depuis plus de 2 500 ans, aux majestueuses forêts du nord, la Tunisie raconte une histoire de civilisation pétrie d'huile et de terre.\r\n### 📜 L'intrigue historique : Une terre nommée d'après son arbre\r\nLe nom de la Tunisie est étroitement lié à **\"l'Olivier\" (Zitouna)**. Nommer la grande **\"Mosquée Zitouna\"** n'était pas une simple coïncidence ou une bénédiction du lieu ; c'était la reconnaissance que cette terre est la \"Terre de l'Olivier\" par excellence.\r\n * **À l'époque carthaginoise :** \"Hannibal\" a fait de la plantation d'oliviers un devoir national pour ses soldats afin de peupler la terre, convaincu que la puissance économique est le pilier de la puissance militaire.\r\n * **À l'époque romaine :** La Tunisie était surnommée le \"Grenier de Rome\", non seulement pour le blé, mais grâce à l'huile de \"Sbeïtla\" et de \"Thysdrus\" (El Jem) qui illuminait les palais de l'empire.\r\nNos ancêtres croyaient que \"chacun a une part de son nom\", donc notre part de gloire était d'être les gardiens de cet arbre béni mentionné par Dieu dans Son livre.\r\n### 🥈 La Tunisie.. Le géant qui reprend sa place\r\nAujourd'hui, la Tunisie n'est pas seulement un acteur secondaire ; nous sommes le **deuxième producteur mondial d'huile d'olive**. Ce chiffre n'est pas qu'une statistique, c'est un \"cri d'alarme\" pour les concurrents et une preuve de mérite pour le monde.\r\nNous possédons plus de **100 millions d'oliviers**, et chaque arbre est comme un puits de pétrole renouvelable, inépuisable et non polluant, mais plutôt source de vie.\r\n### 💰 L'Or Vert : Le nouveau moteur du Dinar\r\nDans les chiffres modernes, un baril d'huile d'olive tunisienne est considéré comme **\"l'Or Vert\"**. Valoriser ce produit n'est pas une option, mais une bataille pour l'indépendance économique :\r\n * **Une qualité de classe mondiale :** L'huile tunisienne remporte chaque année des centaines de médailles d'or lors de concours de qualité internationaux (de New York à Tokyo).\r\n * **Puissance d'exportation :** Le baril d'huile est le véritable lien qui fera s'envoler le dinar tunisien. Lorsque nous passons de l'exportation d'huile en vrac en grandes quantités à son exportation emballée avec des marques tunisiennes de luxe, nous vendons le \"prestige d'un État\", pas seulement un liquide.\r\n### 🌿 Notre appel à vous\r\nLa présence de l'olivier sur notre terre est un \"certificat de bénédiction\". Notre rôle aujourd'hui, chacun depuis sa position, est de valoriser cette richesse. Que vous soyez un agriculteur qui réalise la valeur de \"l'huile pure\", un exportateur frappant aux portes des marchés mondiaux, ou un consommateur fier du produit de son pays.\r\nNous sommes grands, notre histoire en est témoin, et notre avenir est écrit par ce liquide doré coulant du cœur de nos sables et de notre nord.\r\n**La Tunisie est l'Olivier, et l'Olivier est la Tunisie.. avons-nous réalisé la taille du trésor entre nos mains ?**"
        ]
    ]
];

$count = 0;
foreach ($updates as $id => $data) {
    $article = Article::find($id);
    if ($article) {
        $title = is_string($article->title) ? json_decode($article->title, true) : $article->title;
        $content = is_string($article->content) ? json_decode($article->content, true) : $article->content;
        
        $title['en'] = $data['en']['title'];
        $title['fr'] = $data['fr']['title'];
        $content['en'] = $data['en']['content'];
        $content['fr'] = $data['fr']['content'];

        $article->title = $title;
        $article->content = $content;
        $article->save();
        $count++;
    }
}
echo "Updated $count articles successfully.\n";
