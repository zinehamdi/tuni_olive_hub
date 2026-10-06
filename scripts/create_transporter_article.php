<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;

$title = [
    'ar' => 'يا صاحب الإيسيزو والكميون: ما عادش ترجع فارغ! دليلك الشامل لشبكة النقل الذكية على منصة ZinToop وجدول التسعيرة العادلة 🚚💨',
    'fr' => 'Chauffeurs & Transporteurs en Tunisie : Ne roulez plus jamais à vide ! Guide complet du service de transport ZinToop et barème des tarifs',
    'en' => 'Tunisian Truckers & Transporters: Never Drive Empty Again! Complete Guide to ZinToop Instant Transport & Fair Pricing Scale',
];

$category = [
    'ar' => 'لوجستيك ونقل الزيت والزيتون',
    'fr' => 'Logistique & Transport',
    'en' => 'Logistics & Transport',
];

$contentAr = <<<MARKDOWN
في كل موسم جني وعصر زيتون في تونس، يتكرر نفس السيناريو المقلق لأصحاب الشاحنات والبيك-آب: 
* تقطع مئات الكيلومترات من سيدي بوزيد، القيروان، أو جرجيس باش توصل شحنة زيتون ولا زيت لصفاقس، الساحل أو تونس العاصمة...
* وبمجرد ما تفرغ حمولتك، **تلقى روحك مجبور ترجع فارغ**، تخسر حق المازوط وتضيع نهار كامل بلا مردود!
* وإذا حبيت تلوج على كورسة ترجع بيها، تطيح في قبضة **السماسرة والوسطاء** اللي ينزلوا عليك في السوم وياخذوا نسبة كبيرة من تعبك وعرق جبينك.

من هنا، ولدت فكرة **منظومة النقل الفوري والذكي على منصة ZinToop (منصة الزين)**: شبكة لوجستية وطنية تربط أصحاب الشاحنات والناقلين مباشرة بالفلاحين، أصحاب المعاصر، وتجار الزيت في كافة ولايات الجمهورية، **بـ 0% وساطة وبتسعيرة عادلة وشفافة تضمن حق الناقل وحق الفلاح**.

---

![دليل وحلول نقل الزيت والزيتون على منصة ZinToop](/images/articles/zintoop_transporter_pricing_guide.jpg)

---

### 1. كيفاش يخدم سيستام النقل على منصة ZinToop؟

المبدأ بسيط، رقمي، وعصري يسهل الخدمة على السائق والحريف:

1. **الطلب المباشر من الحريف:** الفلاح أو صاحب المعصرة أو المشتري يدخل للمنصة، يحدد مكان التحميل (Pickup) ومكان التسليم (Dropoff)، نوع الحمولة والوزن.
2. **تحديد وسيلة النقل والتسعيرة الفورية:** خوارزمية ZinToop تحسب المسافة الدقيقة عبر الأقمار الاصطناعية (GPS)، وتحدد تلقائياً صنف الشاحنة المناسب، وتعطي السعر التقديري العادل.
3. **إشعار فوري للناقلين القريبين:** يوصلك إشعار فوري على هاتفك وعلى تطبيق الواتساب (WhatsApp) فيه كل معطيات الكورسة (المسار، الوزن، السعر المقترح).
4. **تواصل وتنسيق مباشر:** تقبل الكورسة بكبسة زر، يظهرلك رقم هاتف الحريف ومحادثة فورية (Direct Chat) للتنسيق ومباشرة النقل بدون أي وسيط!

---

### 2. جدول التسعيرة العادلة حسب صنف الشاحنة ونوع الحمولة

وضعت منصة ZinToop سلم تسعير شفاف وواضح يراعي تكاليف الوقود (المازوط)، الاستهلاك الميكانيكي، والمسافة المقطوعة، مع التفريق بين نقل صابة الزيتون ونقل الزيت السائب في الصهاريج الغذائية والبراميل:

<div class="table-scroll-wrapper">
<table>
  <thead>
    <tr>
      <th>صنف وسيلة النقل</th>
      <th>الحمولة القصوى</th>
      <th>تكييف صهاريج الزيت (Citerne)</th>
      <th>المعلوم القار</th>
      <th>تسعيرة الكيلومتر</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>🚐 فورغونات / ستافات</strong></td>
      <td>حتى 1 طن (1000 كغ)</td>
      <td>نقل براميل زيت (Fûts) وبيدونات حتى 1000 لتر</td>
      <td><strong>25 د.ت</strong></td>
      <td><strong>0.750 د.ت / كم</strong></td>
    </tr>
    <tr>
      <td><strong>🛻 شاحنة خفيفة / إيسيزو / ديماكس</strong></td>
      <td>من 1 إلى 2.5 طن</td>
      <td><strong>صهريج صغير غذائي (Isuzu Mini-Citerne)</strong> أو صناديق زيتون</td>
      <td><strong>40 د.ت</strong></td>
      <td><strong>1.000 د.ت / كم</strong></td>
    </tr>
    <tr>
      <td><strong>🚚 شاحنة متوسطة (Camion Moyen)</strong></td>
      <td>من 2.5 إلى 5 طن</td>
      <td>صهريج زيت متوسط أو نقل أكياس وصناديق زيتون</td>
      <td><strong>70 د.ت</strong></td>
      <td><strong>1.500 د.ت / كم</strong></td>
    </tr>
    <tr>
      <td><strong>🚛 شاحنة صهريج ثقيلة (Poids Lourd)</strong></td>
      <td>من 5 إلى 12 طن</td>
      <td>صهريج زيت زيتون مخصص مع مضخة تفريغ (Pompe)</td>
      <td><strong>120 د.ت</strong></td>
      <td><strong>2.000 د.ت / كم</strong></td>
    </tr>
    <tr>
      <td><strong>🚜 سيمي-رمورك / مقطورة كبرى</strong></td>
      <td>أكثر من 12 طن</td>
      <td>صهريج تصدير عملاق أو مقطورات حمولة كبرى</td>
      <td><strong>220 د.ت</strong></td>
      <td><strong>2.800 د.ت / كم</strong></td>
    </tr>
  </tbody>
</table>
</div>

> **💡 معادلة حساب الكورسة:**
> `السعر الإجمالي = المعلوم القار + (المسافة بالكيلومتر × تسعيرة الكيلومتر لوسيلة النقل)`

---

### 3. أمثلة حية لكورسات من الواقع التونسي

باش تفهم الحسبة بالضبط، هذي أمثلة لكورسات يومية:

* **مثال 1: إيسيزو مع صهريج صغير (2 طن زيت زيتون) من سيدي بوزيد إلى صفاقس (~135 كم):**
  * المعلوم القار: **40 د.ت**
  * معلوم المسافة: `135 كم × 1.000 د.ت = 135 د.ت`
  * **المجموع التقديري: 175 د.ت** 
* **مثال 2: شاحنة متوسطة (4 طن زيتون) من القيروان إلى سوسة (~60 كم):**
  * المعلوم القار: **70 د.ت**
  * معلوم المسافة: `60 كم × 1.500 د.ت = 90 د.ت`
  * **المجموع التقديري: 160 د.ت**
* **مثال 3: شاحنة صهريج كبرى (10 طن زيت) من جرجيس إلى ميناء رادس تونس (~520 كم):**
  * المعلوم القار: **120 د.ت**
  * معلوم المسافة: `520 كم × 2.000 د.ت = 1040 د.ت`
  * **المجموع التقديري: 1160 د.ت**

---

### 4. الميزات الحصرية للناقل على منصة ZinToop

علاش تخدم كناقل رسمي في منصة ZinToop؟

* 🎯 **ما عادش ترجع فارغ (كورسات العودة):** تفتح التطبيق في الولاية اللي فرغت فيها، تلقى طلبات نقل قريبة منك تستنى في شاحنة متجهة لولايتك الأصلية.
* 💰 **0% عمولة واقتطاعات:** ما نقتطعوش حتى مليم من تسعيرة الكورسة متاعك، تفاهمك ودفعك مباشرة بينك وبين صاحب البضاعة.
* 📲 **إشعارات واتساب ذكية:** يوصلك تنبيه مع رابط واتساب مباشر للحريف فيه تفاصيل المسار بنقرة واحدة.
* ⭐ **تقييم وسمعة احترافية:** كل ما تخدم بجدية ونظافة، الفلاحة والمعاصر يقيموك بنجوم عالية، وتولي الناقل المفضل والمطلوب بالاسم في منطقتك.

---

### 5. شروط السلامة ونقل الزيت والزيتون

لضمان جودة الزيت التونسي والحفاظ على ثقة الحرفاء:
1. **صهاريج الاينوكس الغذائية (Inox Alimentaire):** لنقل الزيت السائب، يجب أن تكون الصهاريج نظيفة ومطابقة للمواصفات الغذائية ولا تترك أي طعم أو رائحة.
2. **التهوية السليمة للزيتون الفلاحي:** عند نقل حب الزيتون، يفضل استخدام الصناديق البلاستيكية المهواة (Caisses ajourées) أو الفرش النظيف لتفادي تخمر الزيتون أثناء السفر.
3. **الالتزام بالمواعيد:** الحفاظ على توقيت التحميل والتفريغ خصوصاً عند التوجه للمعاصر في أوقات الذروة.

---

### 🚀 كيفاش تنخرط وتبدأ تخدم كناقل على ZinToop؟

الانخراط مجاني تماماً وما ياخوش أكثر من دقيقتين من وقتك:

1. ادخل وسجل حسابك مجاناً عبر الرابط: **[إنشاء حساب على ZinToop](https://zintoop.com/register)**.
2. في ملفك الشخصي أو عند اختيار صفتك، اختر صفة **ناقل / خدمات لوجستية (Transporter)**.
3. حدد نوع وسيلة النقل متاعك (إيسيزو صهريج، ستافات، كماين صهريج، إلخ) ورقم هاتفك والولاية اللي متمركز فيها.
4. وفعّل استقبال إشعارات الواتساب باش تكون أول من يتلقى الكورسات الفورية في كامل تراب الجمهورية!

**الزين توب.. طريقك معمر، وكورستك مضمونة!**
MARKDOWN;

$contentFr = <<<MARKDOWN
Chaque saison oléicole en Tunisie, les propriétaires de camionnettes, Isuzu et poids lourds font face au même défi : acheminer une cargaison vers une autre région et **revenir à vide**, perdant temps et carburant, ou subir la pression des intermédiaires informels.

**ZinToop Transport** transforme cette réalité grâce à la première infrastructure numérique de transport oléicole direct en Tunisie : **0% de commission, mise en relation directe via WhatsApp/Appel, et un barème tarifaire transparent et équitable.**

---

![Guide Logistique et Transport Huile d'Olive ZinToop](/images/articles/zintoop_transporter_pricing_guide.jpg)

---

### 1. Comment fonctionne le service de transport ZinToop ?

1. **Demande en ligne instantanée :** Le client (agriculteur, moulinier, acheteur) renseigne le point de départ, la destination, le poids et le type de marchandise.
2. **Calcul kilométrique précis & barème officiel :** L'algorithme ZinToop calcule la distance exacte et propose un tarif équitable basé sur des critères objectifs.
3. **Notification en temps réel :** Les transporteurs à proximité reçoivent une alerte immédiate (Web & WhatsApp) avec les détails de la mission.
4. **Prise de contact directe :** Le transporteur accepte la course et contacte directement le client sans aucun frais d'intermédiaire.

---

### 2. Barème officiel des tarifs de transport ZinToop

<div class="table-scroll-wrapper">
<table>
  <thead>
    <tr>
      <th>Catégorie de Véhicule</th>
      <th>Charge Maximale</th>
      <th>Spécificité Huile d'Olive</th>
      <th>Frais Fixes de Base</th>
      <th>Tarif au Kilomètre</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>🚐 Fourgonnette / Stafette</strong></td>
      <td>Jusqu'à 1 Tonne (1000 kg)</td>
      <td>Fûts & bidons jusqu'à 1000 L</td>
      <td><strong>25 DT</strong></td>
      <td><strong>0.750 DT / km</strong></td>
    </tr>
    <tr>
      <td><strong>🛻 Camionnette légère / Isuzu / D-Max</strong></td>
      <td>1 à 2.5 Tonnes</td>
      <td><strong>Petite citerne inox (Isuzu Mini-Citerne)</strong> ou caisses d'olives</td>
      <td><strong>40 DT</strong></td>
      <td><strong>1.000 DT / km</strong></td>
    </tr>
    <tr>
      <td><strong>🚚 Camion moyen</strong></td>
      <td>2.5 à 5 Tonnes</td>
      <td>Citerne moyenne ou chargement palettes/caisses</td>
      <td><strong>70 DT</strong></td>
      <td><strong>1.500 DT / km</strong></td>
    </tr>
    <tr>
      <td><strong>🚛 Camion citerne lourd</strong></td>
      <td>5 à 12 Tonnes</td>
      <td>Camion citerne spécialisé avec pompe de dépotage</td>
      <td><strong>120 DT</strong></td>
      <td><strong>2.000 DT / km</strong></td>
    </tr>
    <tr>
      <td><strong>🚜 Semi-remorque grand volume</strong></td>
      <td>Plus de 12 Tonnes</td>
      <td>Citerne industrielle grand export ou remorque vrac</td>
      <td><strong>220 DT</strong></td>
      <td><strong>2.800 DT / km</strong></td>
    </tr>
  </tbody>
</table>
</div>

> **💡 Formule de calcul :**
> `Coût Estimé = Frais Fixes + (Distance en km × Tarif au km du véhicule)`

---

### 3. Exemples concrets de trajets en Tunisie

* **Isuzu Mini-citerne (2T huile) de Sidi Bouzid à Sfax (~135 km) :**
  `40 DT + (135 × 1.000 DT) = 175 DT`
* **Camion moyen (4T olives) de Kairouan à Sousse (~60 km) :**
  `70 DT + (60 × 1.500 DT) = 160 DT`
* **Camion citerne (10T huile) de Zarzis au Port de Radès Tunis (~520 km) :**
  `120 DT + (520 × 2.000 DT) = 1 160 DT`

---

### 4. Vos avantages en tant que transporteur ZinToop

* **Éliminez les retours à vide :** Trouvez des chargements de retour immédiats là où vous déchargez.
* **0% de prélèvement :** 100% du montant convenu va directement dans votre poche.
* **Alertes WhatsApp directes :** Recevez les courses instantanément sur votre téléphone.
* **Réputation & visibilité :** Développez votre portefeuille de clients réguliers parmi les moulins et producteurs.

---

### 🚀 Rejoignez le réseau des transporteurs ZinToop

L'inscription est simple et gratuite :
👉 **Créez votre compte sur ZinToop :** [https://zintoop.com/register](https://zintoop.com/register)  
Activez votre profil **Transporteur / Logistique**, renseignez votre type de véhicule et commencez à recevoir des courses dès aujourd'hui !
MARKDOWN;

$contentEn = <<<MARKDOWN
Every olive season in Tunisia, truck, pickup, and cistern drivers face the costly challenge of driving back empty after long trips, or falling victim to informal brokers taking large cuts.

**ZinToop Transport Network** solves this by connecting certified truckers directly with olive farmers, millers, and bulk oil traders across all Tunisian governorates with **0% commission and a fair, standardized pricing scale.**

---

![ZinToop Olive Oil Transport Logistics Guide](/images/articles/zintoop_transporter_pricing_guide.jpg)

---

### 1. How the ZinToop Transport System Works

1. **Instant Client Request:** The shipper enters pickup location, destination, cargo type (oil cistern/drums vs olive fruit/crates), and weight.
2. **Fair GPS Rate Calculation:** ZinToop automatically calculates route distance and matching vehicle tier with standardized transparent pricing.
3. **Instant Mobile & WhatsApp Alerts:** Nearby transporters receive an immediate dispatch alert with full trip details.
4. **Direct Contact:** The driver accepts the ride and connects directly with the customer via call or chat with zero intermediaries.

---

### 2. Fair Pricing Scale by Vehicle Tier & Cargo

<div class="table-scroll-wrapper">
<table>
  <thead>
    <tr>
      <th>Vehicle Tier</th>
      <th>Max Capacity</th>
      <th>Olive Oil Adaptation</th>
      <th>Base Fee</th>
      <th>Per-KM Rate</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>🚐 Van / Stafette</strong></td>
      <td>Up to 1 Ton (1000 kg)</td>
      <td>Oil drums (fûts) & cans up to 1000 L</td>
      <td><strong>25 TND</strong></td>
      <td><strong>0.750 TND / km</strong></td>
    </tr>
    <tr>
      <td><strong>🛻 Light Truck / Isuzu / D-Max</strong></td>
      <td>1 to 2.5 Tons</td>
      <td><strong>Isuzu Mini-Cistern</strong> or olive harvest crates</td>
      <td><strong>40 TND</strong></td>
      <td><strong>1.000 TND / km</strong></td>
    </tr>
    <tr>
      <td><strong>🚚 Medium Truck</strong></td>
      <td>2.5 to 5 Tons</td>
      <td>Medium oil cistern or bulk olive crates</td>
      <td><strong>70 TND</strong></td>
      <td><strong>1.500 TND / km</strong></td>
    </tr>
    <tr>
      <td><strong>🚛 Heavy Tanker Truck</strong></td>
      <td>5 to 12 Tons</td>
      <td>Dedicated food-grade stainless tanker with pump</td>
      <td><strong>120 TND</strong></td>
      <td><strong>2.000 TND / km</strong></td>
    </tr>
    <tr>
      <td><strong>🚜 Semi-Trailer / Large Tanker</strong></td>
      <td>Over 12 Tons</td>
      <td>Industrial bulk export cistern or heavy trailer</td>
      <td><strong>220 TND</strong></td>
      <td><strong>2.800 TND / km</strong></td>
    </tr>
  </tbody>
</table>
</div>

> **💡 Pricing Formula:**
> `Total Estimated Cost = Base Fee + (Distance in km × Vehicle Per-KM Rate)`

---

### 3. Practical Route Examples in Tunisia

* **Isuzu Mini-Cistern (2T Olive Oil) from Sidi Bouzid to Sfax (~135 km):**
  `40 TND + (135 × 1.000 TND) = 175 TND`
* **Medium Truck (4T Olives) from Kairouan to Sousse (~60 km):**
  `70 TND + (60 × 1.500 TND) = 160 TND`
* **Heavy Tanker (10T Oil) from Zarzis to Radès Port Tunis (~520 km):**
  `120 TND + (520 × 2.000 TND) = 1,160 TND`

---

### 4. Why Join ZinToop as a Transporter?

* **No More Empty Return Trips:** Pick up return loads on your way back and maximize your daily profit.
* **0% Commission:** Keep 100% of your earnings; deal directly with verified farmers and mills.
* **Smart WhatsApp Notifications:** Direct alerts with customer contact info in one tap.
* **Build Your Reputation:** High customer ratings make you the preferred carrier in your region.

---

### 🚀 How to Register as a ZinToop Transporter

Registration is fast and 100% free:
👉 **Sign up on ZinToop:** [https://zintoop.com/register](https://zintoop.com/register)  
Select **Transporter / Logistics** profile, add your vehicle details, and start receiving instant transport requests today!
MARKDOWN;

$article = Article::updateOrCreate(
    ['image' => 'images/articles/zintoop_transporter_pricing_guide.jpg'],
    [
        'title' => $title,
        'category' => $category,
        'content' => [
            'ar' => $contentAr,
            'fr' => $contentFr,
            'en' => $contentEn,
        ],
        'is_active' => true,
    ]
);

echo "SUCCESS: Article #{$article->id} created/updated.\n";
echo "Title AR: {$article->title['ar']}\n";
echo "URL: https://zintoop.com/ar/articles/{$article->id}\n";
