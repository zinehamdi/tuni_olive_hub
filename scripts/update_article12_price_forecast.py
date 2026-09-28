import json
import re

article_12_ar = """يشهد سوق زيت الزيتون في تونس وحوض البحر الأبيض المتوسط مرحلة إعادة هيكلة وتوازن اقتصادي عميق لموسم 2026/2027. فبعد موسم استثنائي سجلت فيه تونس إنتاجاً قياسياً تجاوز 460 ألف طن وصادرات نقدية فاقت 4.6 مليار دينار، يترقب المنتجون والمحولون والمصدرون بوصلة الأسعار الجديدة في ظل تغير موازين القوى الإنتاجية العالمية.

---

### 1. خارطة الإنتاج العالمي: صعود دول الجوار وتعافي الإنتاج السوري

لا تقتصر المنافسة اليوم على القوى الأوروبية التقليدية (إسبانيا وإيطاليا)، بل دخلت دول جنوب وشرق المتوسط كلاعبين رئيسيين في العرض العالمي:

* **إسبانيا (المتصدر العالمي 40-45%):** تشير التقديرات الميدانية الأولية لتعافي السدود إلى محصول واعد يتراوح بين **1.35 و 1.45 مليون طن**.
* **تونس (العملاق الجنوبي للبحر المتوسط):** بعد المحصول التاريخي للموسم الفارط، تدخل أشجار الزيتون في دورة **المعاومة الطبيعية**، وتستقر التوقعات الوطنية حول **280 إلى 320 ألف طن**، وهو حجم متوازن يضمن كفاية الاستهلاك الداخلي وقدرة تصديرية تتجاوز 240 ألف طن.
* **تركيا (المنافس المتأرجح):** إنتاج متوقع قوي بين **350 و 400 ألف طن** مع منافسة شرسة في التصدير غير المعلب.
* **المغرب (140 - 160 ألف طن):** بفضل استراتيجية الجيل الأخضر وتوسيع المساحات المروية، أصبح منافساً قوياً في الأسواق الإفريقية والأوروبية مع استهلاك محلي مرتفع.
* **الجزائر (100 - 130 ألف طن):** توسع برامج الغراسة المكثفة في الهضاب العليا والصحراء على أكثر من نصف مليون هكتار يرفع الإنتاج ويفتح منافذ تصديرية جديدة.
* **مصر (الرائدة عالمياً في زيتون المائدة):** تسيطر على سوق زيتون المائدة عالمياً، وبدأت تتوسع تدريجياً في وحدات العصر للزيوت البكر.
* **تعافي الإنتاج في سوريا (100 - 130 ألف طن):** تشهد غابات الزيتون في إدلب وعفرين والساحل السوري تعافياً ملموساً وإعادة تشغيل للمعاصر، مما يعيد تدفق الزيت السوري إلى الأسواق المجاورة والخليجية.
* **المملكة العربية السعودية (الجوف):** طفرة إنتاجية كبرى بأكثر من 25 مليون شجرة بنظام الغراسة فائقة الكثافة لتغطية جزء معتبر من الطلب الخليجي.
* **البرتغال ودول النصف الجنوبي (الشيلي والأرجنتين وأستراليا):** نمو مستمر في الإنتاج المكثف الحديث يوفر معروضاً إضافياً في فترات الركود الموسمي.

---

### 2. معادلة الإنتاج العالمي مقابل الاستهلاك: كيف يتحدد السعر الدولي؟

* **حجم الاستهلاك العالمي:** يبلغ المعدل الطبيعي لاستهلاك زيت الزيتون في العالم حوالي **3.0 إلى 3.2 مليون طن سنوياً**.
* **موقع زيت الزيتون عالمياً:** يمثل زيت الزيتون **3% فقط من مجمل الزيوت النباتية المستهلكة في العالم**؛ وهو ما يجعله سلعة ممتازة شديدة الحساسية لمرونة الأسعار.
  * **عندما يقل الإنتاج العالمي عن 2.8 مليون طن:** يحدث شح حاد وترتفع الأسعار لمستويات قياسية، لكن المستهلك يقلص مشترياته أو يلجأ للخلط مع زيوت البذور (الكانولا ودوار الشمس).
  * **عندما يتعادل الإنتاج مع الاستهلاك (3.1 - 3.3 مليون طن):** تعود الأسعار لنطاقها التوازني الطبيعي، وينتعش الطلب العالمي مجدداً بفضل استقرار القوة الشرائية للمستهلكين.

---

### 3. السؤال الحارق: لماذا يضطر الفلاح أحياناً للبيع بسعر أقل من التكلفة؟

تشير بيانات المنظمات الفلاحية إلى أن كلفة إنتاج لتر الزيت البكر تتراوح بين **12.5 و 14.0 ديناراً**، ومع ذلك يضطر بعض الفلاحين في أوقات معينة للبيع بأسعار منخفضة نتيجة 4 أسباب هيكلية:

1. **الضغط الخانق للسيولة الفورية:** تمثل مصاريف جني الصابة ونقلها نقداً أكثر من **40% إلى 50% من الكلفة الإجمالية**. الفلاح مطالب بدفع أجور عمال الجني يومياً عند نهاية كل يوم، مما يدفعه لبيع محصوله فوراً لتسديد العمال وتجنب الديون، حتى وإن كان السعر المعروض مجحفاً.
2. **الخوف من تلف الزيتون:** الزيتون ثمرة حساسة جداً؛ إذا بقيت في الأكياس لأكثر من يومين ترتفع نسبة الحموضة ويتخمر الزيتون وينزل تصنيفه من بكر ممتاز إلى وقاد. الفلاح الذي لا يملك وسائل تخزين مهواة يجد نفسه مرغماً على تفريغ محصوله في المعصرة بأي سعر متاح.
3. **غياب قروض التخزين والتسويق الموسمية:** صغار ومتوسطو الفلاحين محرومون غالباً من التمويلات الميسرة التي تمكنهم من عصر محصولهم والاحتفاظ بالزيت في صهاريج مخصصة لبيعه لاحقاً في أشهر الربيع والصيف بأسعار أفضل.
4. **المضاربة في ذروة العصر:** خلال شهري ديسمبر وجانفي، تتدفق آلاف الأطنان من الزيتون إلى المعاصر دفعة واحدة في فترة زمنية قصيرة، فيستغل بعض السماسرة حالة الاختناق لخفض الأسعار بشكل مصطنع وشراء الزيت بأقل من قيمته الحقيقية.

---

### 4. جدول "القطوع" وسعر كيلو الزيتون للفلاح

"القطوع" أو التبويز هو النسبة المئوية لكمية الزيت المستخرجة من كل مائة كيلوغرام من الزيتون (قنطار):

| مرحلة الموسم ونوع الزيتون | نسبة القطوع (التبويز) | كمية الزيت في القنطار (100 كغ) | سعر كيلو الزيتون التقديري العادل | نوع وجودة الزيت الناتج |
| :--- | :---: | :---: | :---: | :--- |
| **بداية الموسم (أكتوبر - نوفمبر)** | **13% - 15%** | 13 إلى 15 لتر | **1.70 - 2.00 دينار / كغ** | بكر ممتاز فائق أخضر ذو نكهة ثمرية مبكرة |
| **ذروة الموسم (ديسمبر - جانفي)** | **18% - 20%** | 18 إلى 20 لتر | **2.20 - 2.60 دينار / كغ** | بكر ممتاز تجاري متوازن |
| **أواخر الموسم (فيفري - مارس)** | **22% - 25%** | 22 إلى 25 لتر | **2.60 - 3.10 دينار / كغ** | زيت بكر ناضج غني بالزيوت |

---

### 5. حسابات "الباز" وأصحاب المعاصر

1. **كلفة العصر في المعصرة:**
   * **نقداً:** بين **150 و 220 مليماً للكلغ الواحد من الزيتون** (15 إلى 22 ديناراً للقنطار) حسب طاقة المعصرة وتجهيزاتها الحديثة.
   * **بالوزيعة (العينية):** خصم عيني بين **8% و 10%** من كمية الزيت الناتجة كأجرة عصر.
2. **شراء الزيتون بالباز:** اختبار نسبة الزيت الفورية في القنطار وربط سعر الشراء بسعر صهريج الزيت السائب المتداول في السوق.
3. **هامش الأمان للمعصري:** الحفاظ على فارق تغطية بين شراء الزيتون وبيع الصهريج بـ **600 إلى 800 مليم في اللتر** لتغطية الطاقة، اليد العاملة، واهتلاك المعدات وتجنب تقلبات البورصة.

---

### 6. سعر التكلفة الحقيقي حسب الجودة

| تصنيف الجودة | المواصفات الفنية | التكلفة الحقيقية للإنتاج (د/لتر) | السعر التوازني العادل (د/لتر) |
| :--- | :--- | :---: | :---: |
| **بكر ممتاز فائق** | قطفة أولى، جني بالصناديق، حموضة أقل من 0.3% | **14.50 - 16.00 دينار** | **17.00 - 22.00 دينار** |
| **بكر ممتاز تجاري** | عصر خلال 24 ساعة، حموضة أقل من 0.8% | **12.50 - 13.50 دينار** | **14.00 - 16.00 دينار** |
| **بيولوجي معتمد** | بدون أسمدة كيميائية، شهادة دولية معتمدة | **13.50 - 15.00 دينار** | **16.50 - 19.50 دينار** |
| **زيت بكر عادي** | حموضة بين 0.8% و 2% | **10.50 - 11.50 دينار** | **11.50 - 13.00 دينار** |
| **زيت وقاد** | حموضة أعلى من 2% (صناعي موجه للتكرير) | **8.50 - 9.50 دينار** | **9.50 - 10.50 دينار** |

---

### 7. سيناريوهات السوق لموسم 2026/2027

* **السيناريو الأول (المتفائل - نقص في إنتاج حوض المتوسط):**
  * في حال تأثر محاصيل إسبانيا أو اليونان بالجفاف، فإن صهاريج الزيت البكر الممتاز التونسي ستتداول بين **15.50 و 17.50 ديناراً للتر** (3.80 إلى 4.50 يورو للكلغ عند التصدير).
* **السيناريو الثاني (المرجح - توازن العرض والطلب الدولي):**
  * استقرار الإنتاج العالمي حول 3.1 مليون طن وإنتاج تونس حول 300 ألف طن يقود لتداول صهاريج الزيت البكر الممتاز بين **13.00 و 15.00 ديناراً للتر** في المعاصر وسعر زيتون بين **2.20 و 2.60 دينار للكلغ**.
* **السيناريو الثالث (المتحفظ - وفرة إنتاج إسبانية وضغط المشترين):**
  * إذا تجاوز الإنتاج الإسباني 1.55 مليون طن تتراجع الأسعار نحو خط الأمان حول **11.00 - 12.50 ديناراً للتر**. ويكون المخرج للفلاح والمعصري هو التخزين المحكم في صهاريج الفولاذ المقاوم للصدأ وعدم البيع السريع بأقل من التكلفة والرهان على الزيت البيولوجي والمعلب.

---

### 8. كيف تكسر منصة زين توب هذه المعضلات وتحمي الفلاح والمعصري؟

1. **البيع المباشر المسبق (بدون أي عمولات):** فتح قنوات التواصل المباشر بين الفلاح والمعاصر والمشترين دون وساطة تقليدية.
2. **الشفافية السعرية التامة:** نشر مؤشر الأسعار الأسبوعي المحين لتمكين الفلاح من معرفة السعر الحقيقي لقطوع محصوله قبل خروجه من حقله.
3. **توفير خدمات النقل والتحاليل المخبرية المعتمدة:** تسهيل نقل المحصول مباشرة للمعاصر وتثمين الجودة العالية للحصول على أفضل عائد مالي ممكن.

تابع مؤشر الأسعار الأسبوعي المحدث: https://zintoop.com/ar/prices"""

article_12_en = """The Mediterranean olive oil market is navigating a pivotal transition into the 2026/2027 campaign. Following a record Tunisian harvest of over 460,000 tonnes and export revenues exceeding 4.6 billion TND, producers, millers, and global traders are recalibrating pricing benchmarks amid shifting global supply dynamics.

---

### 1. Global Supply Dynamics: Rising Regional Powerhouses & Syrian Recovery

Global supply is no longer dictated solely by traditional European producers (Spain and Italy). Southern and Eastern Mediterranean nations have emerged as vital contributors:

* **Spain (Global Benchmark 40-45%)**: Early field assessments indicate a normalized production between **1.35 and 1.45 million tonnes**.
* **Tunisia (Southern Mediterranean Leader)**: Following historic volumes, the national crop enters its natural **biennial alternation cycle**, stabilizing around **280,000 to 320,000 tonnes**—providing robust domestic security and over 240,000 tonnes for export.
* **Turkey**: Anticipated volume of **350,000 to 400,000 tonnes**, driving competitive bulk flows.
* **Morocco (140,000 - 160,000 tonnes)**: Expanded modern irrigated acreage under national agro-initiatives solidifies regional supply alongside strong domestic demand.
* **Algeria (100,000 - 130,000 tonnes)**: High-density inland plantings spanning over 500,000 hectares increase export availability.
* **Egypt**: World leader in table olives, expanding mechanical extraction capacity for extra virgin bulk.
* **Syria's Agricultural Recovery (100,000 - 130,000 tonnes)**: Rejuvenated groves in Idlib, Afrin, and coastal valleys restore regional olive oil trade corridors.
* **Saudi Arabia (Al-Jouf)**: Over 25 million super-intensive trees targeting Gulf regional consumption.
* **Portugal & Southern Hemisphere (Chile, Argentina, Australia)**: Super-intensive developments providing counter-cyclical supply.

---

### 2. Global Supply vs Demand Balance: The Macro Mechanics

* **Global Annual Demand**: Baseline global olive oil consumption stands between **3.0 and 3.2 million tonnes** annually.
* **The 3% Reality**: Olive oil represents only **3% of worldwide vegetable oil consumption**, making it an elastic premium commodity.
  * **Deficit Years (< 2.8M tonnes)**: Triggers severe price spikes, forcing price-sensitive retail consumers toward blended seed oils.
  * **Balanced Years (3.1 - 3.3M tonnes)**: Restores equilibrium and stimulates steady consumption across European, North American, and Asian import markets.

---

### 3. The Economic Dilemma: Why Farmers Sometimes Sell Below Production Cost

While official farm organizations calculate average EVOO production costs between **12.5 and 14.0 TND/L**, growers are occasionally forced into distressed sales due to 4 structural hurdles:

1. **Acute Cash Flow Pressure**: Harvesting and transport labor represents **40% to 50% of total production cost**, payable in daily cash wages. Smallholders frequently liquidate fresh harvest immediately to settle labor payroll.
2. **Fruit Perishability & Oxidation Risks**: Fresh olives degrade rapidly if bagged beyond 48 hours, causing fermentation and degrading EVOO into lampante grade.
3. **Lack of Seasonal Working Capital**: Limited access to short-term harvest credit restricts smallholders from storing bulk oil in certified tanks for off-season premium sales.
4. **Peak Harvest Speculation**: During December and January, processing bottlenecks allow informal aggregators to enforce artificial price discounts on distressed farmgate sellers.

---

### 4. Extraction Yield (Tabouiz) & Farmgate Olive Pricing Matrix

| Harvest Stage & Quality | Extraction Yield (%) | Oil per Quintal (100 kg) | Fair Farmgate Olive Price | Resulting Oil Grade |
| :--- | :---: | :---: | :---: | :--- |
| **Early Harvest (Oct - Nov)** | **13% - 15%** | 13 - 15 Liters | **1.70 - 2.00 TND / kg** | Ultra Premium Early Green Fruity |
| **Peak Season (Dec - Jan)** | **18% - 20%** | 18 - 20 Liters | **2.20 - 2.60 TND / kg** | Standard Commercial Extra Virgin |
| **Late Harvest (Feb - Mar)** | **22% - 25%** | 22 - 25 Liters | **2.60 - 3.10 TND / kg** | Ripe Virgin High-Oil Yield |

---

### 5. Mill Economics (Prix de Base & Processing Fees)

1. **Trituration Fees**: Ranges between **150 and 220 millimes per kg of raw olives** (15 - 22 TND per quintal) or an in-kind retention of **8% to 10%** of extracted oil.
2. **Base Pricing (Le Baz)**: Mills calibrate live purchasing prices directly to NIR Soxhlet oil yield tests and prevailing bulk tank contracts.
3. **Millers' Safety Margin**: Maintaining a buffer of **0.600 to 0.800 TND/L** against bulk export sales covers energy, labor, and equipment depreciation.

---

### 6. True Cost of Production by Quality Grade

| Grade | Technical Specification | Real Cost of Production (TND/L) | Fair Equilibrium Price (TND/L) |
| :--- | :--- | :---: | :---: |
| **Ultra Premium EVOO** | Early harvest, cold-pressed, acidity < 0.3% | **14.50 - 16.00 TND** | **17.00 - 22.00 TND** |
| **Standard Extra Virgin** | Milled within 24h, acidity < 0.8% | **12.50 - 13.50 TND** | **14.00 - 16.00 TND** |
| **Certified Organic (Bio)** | Zero chemicals, international certification | **13.50 - 15.00 TND** | **16.50 - 19.50 TND** |
| **Virgin Olive Oil** | Acidity between 0.8% and 2.0% | **10.50 - 11.50 TND** | **11.50 - 13.00 TND** |
| **Lampante Oil** | Industrial refining grade, acidity > 2.0% | **8.50 - 9.50 TND** | **9.50 - 10.50 TND** |

---

### 7. 2026/2027 Market Outlook Scenarios

* **Bullish Scenario (Mediterranean Harvest Deficit)**: Should drought constrain European harvests, Tunisian bulk EVOO will trade at **15.50 - 17.50 TND/L** (€3.80 - €4.50/kg FOB).
* **Baseline Scenario (Balanced Supply & Demand - Most Probable)**: World production around 3.1M tonnes and Tunisian crop near 300,000 tonnes establishes bulk EVOO at **13.00 - 15.00 TND/L** at mills and farmgate olives at **2.20 - 2.60 TND/kg**.
* **Bearish Scenario (Spanish Super-Harvest & Buyer Pressure)**: Spanish output exceeding 1.55M tonnes could soften bulk prices to **11.00 - 12.50 TND/L**, reinforcing the necessity of stainless-steel storage, organic certification, and value-added retail packaging.

---

### 8. How ZinToop Protects Farmers and Millers

1. **Direct Forward Trading (0% Commission)**: Direct interaction between growers, millers, and verified buyers.
2. **Transparent Weekly Price Index**: Ground-truth price monitoring across all Tunisian governorates.
3. **Certified Logistics & Lab Verification**: Streamlined transport coordination and certified COA testing to capture maximum quality premiums.

Track live weekly market prices: https://zintoop.com/en/prices"""

article_12_fr = """Le marché de l'huile d'olive en Tunisie et en Méditerranée aborde la campagne 2026/2027 dans un contexte de rééquilibrage structurel. Après une saison 2025/2026 record marquée par une production tunisienne de plus de 460 000 tonnes et des recettes d'exportation dépassant 4,6 milliards de dinars, la filière recalibre ses repères tarifaires face aux nouvelles dynamiques mondiales.

---

### 1. Cartographie Mondiale de la Production : Concurrence Régionale et Reprise Syrienne

L'offre internationale intègre désormais de puissants acteurs du Sud et de l'Est de la Méditerranée :

* **Espagne (Leader mondial 40-45%)** : Les premières prévisions indiquent une récolte normalisée entre **1,35 et 1,45 million de tonnes**.
* **Tunisie (Pilier du Sud)** : Après le record historique, le verger entre dans son **cycle d'alternance biennale**, avec une récolte estimée entre **280 000 et 320 000 tonnes** assurant l'autosuffisance et plus de 240 000 tonnes exportables.
* **Turquie** : Volume solide anticipé entre **350 000 et 400 000 tonnes**.
* **Maroc (140 000 - 160 000 tonnes)** : L'extension des plantations irriguées renforce sa position sur les marchés africains et européens.
* **Algérie (100 000 - 130 000 tonnes)** : Plus de 500 000 hectares d'oliveraies intensives augmentent la disponibilité à l'export.
* **Égypte** : Premier producteur mondial d'olives de table, développant sa capacité d'extraction d'huile vierge extra.
* **Reprise en Syrie (100 000 - 130 000 tonnes)** : Les oliveraies d'Idlib, d'Afrin et de la côte syrienne relancent leurs flux régionaux.
* **Arabie Saoudite (Al-Jouf)** : Plus de 25 millions d'arbres en super-intensif couvrant une part croissante de la demande du Golfe.

---

### 2. Équilibre Offre vs Demande : Les Mécanismes du Marché Mondial

* **Consommation Mondiale** : Se stabilise structurellement entre **3,0 et 3,2 millions de tonnes par an**.
* **La Part des 3 %** : L'huile d'olive ne représente que **3 % de la consommation mondiale d'huiles végétales**, ce qui en fait un produit noble et élastique aux prix.
  * **En situation de déficit (< 2,8M tonnes)** : Flambée des cours et report des consommateurs vers les huiles de graines.
  * **En situation d'équilibre (3,1 - 3,3M tonnes)** : Stabilisation des prix et reprise vigoureuse de la demande sur les marchés d'importation européens et américains.

---

### 3. Le Paradoxe Économique : Pourquoi l'Agriculteur Vend-il Parfois sous le Coût de Revient ?

Alors que le coût moyen de production d'un litre d'huile d'olive extra vierge est estimé entre **12,5 et 14,0 DT**, certains producteurs se trouvent contraints de vendre à perte en raison de 4 facteurs structurels :

1. **Pression Aiguë de Trésorerie** : Les frais de cueillette et de transport représentent **40 % à 50 % du coût total**, payables en espèces chaque soir. Le producteur liquide sa récolte pour régler la main-d'œuvre.
2. **Périssabilité des Olives** : Les olives fraîches en sacs se détériorent après 48 heures, entraînant une hausse d'acidité qui dégrade l'huile vers le grade lampante.
3. **Absence de Crédits de Campagne Accessibles** : Manque de liquidités saisonnières pour stocker l'huile en cuves inox et la commercialiser au printemps aux meilleurs cours.
4. **Spéculation au Pic de Récolte** : En décembre et janvier, l'afflux massif d'olives aux moulins permet aux intermédiaires d'imposer des décotes artificielles.

---

### 4. Barème de Rendement (Tabouiz) et Prix d'Achat des Olives

| Période et Maturité | Taux d'Extraction (%) | Rendement / Quintal (100 kg) | Prix d'Achat Équitable des Olives | Qualité de l'Huile Obtenue |
| :--- | :---: | :---: | :---: | :--- |
| **Début de Saison (Oct - Nov)** | **13% - 15%** | 13 à 15 Litres | **1,70 - 2,00 DT / kg** | Vierge Extra Ultra Premium Fruité Vert |
| **Plein Pic (Déc - Jan)** | **18% - 20%** | 18 à 20 Litres | **2,20 - 2,60 DT / kg** | Vierge Extra Standard Équilibrée |
| **Fin de Saison (Fév - Mar)** | **22% - 25%** | 22 à 25 Litres | **2,60 - 3,10 DT / kg** | Vierge Douce Riche en Huile |

---

### 5. Économie des Moulins (Prix de Base et Marges de Sécurité)

1. **Frais de Trituration** : Entre **150 et 220 millimes par kg d'olives** (15 à 22 DT par quintal) ou rétention en nature de **8 % à 10 %** de l'huile extraite.
2. **Achat au Rendement (Le Baz)** : Les mouliniers indexent les prix d'achat sur les tests de laboratoire NIR et les cours des citernes de vrac.
3. **Marge de Sécurité du Moulinier** : Un différentiel de **0,600 à 0,800 DT/L** entre l'achat des olives et la vente en citerne est nécessaire pour couvrir l'énergie, la main-d'œuvre et l'amortissement du matériel.

---

### 6. Coût Réel de Production par Catégorie de Qualité

| Catégorie | Spécifications Techniques | Coût Réel de Production (DT/L) | Prix d'Équilibre Équitable (DT/L) |
| :--- | :--- | :---: | :---: |
| **Vierge Extra Ultra Premium** | Cueillette en caisses, acidité < 0,3% | **14,50 - 16,00 DT** | **17,00 - 22,00 DT** |
| **Vierge Extra Standard** | Trituration sous 24h, acidité < 0,8% | **12,50 - 13,50 DT** | **14,00 - 16,00 DT** |
| **Biologique Certifiée** | Sans intrants chimiques, certifiée export | **13,50 - 15,00 DT** | **16,50 - 19,50 DT** |
| **Vierge Standard** | Acidité entre 0,8% et 2,0% | **10,50 - 11,50 DT** | **11,50 - 13,00 DT** |
| **Huile Lampante** | Acidité > 2,0% (raffinage industriel) | **8,50 - 9,50 DT** | **9,50 - 10,50 DT** |

---

### 7. Scénarios Prévisionnels pour la Campagne 2026/2027

* **Scénario Haussier (Déficit Méditerranéen)** : En cas de sécheresse en Espagne/Grèce, la citerne de vierge extra tunisienne s'échangera entre **15,50 et 17,50 DT/L** (3,80 - 4,50 €/kg FOB).
* **Scénario Équilibré (Scénario de Référence)** : Production mondiale à 3,1M tonnes et récolte tunisienne à 300 000 tonnes -> cours des citernes entre **13,00 et 15,00 DT/L** et olives entre **2,20 et 2,60 DT/kg**.
* **Scénario Baissier (Super-Récolte Espagnole)** : Production espagnole > 1,55M tonnes -> repli des cours vers **11,00 - 12,50 DT/L**, imposant le stockage en cuves inox et la valorisation par le conditionné et le bio.

---

### 8. L'Apport de ZinToop pour la Filière Oléicole

1. **Vente Directe sans Commission (0%)** : Connexion fluide entre producteurs, moulins et acheteurs institutionnels.
2. **Baromètre Hebdomadaire des Prix** : Cotations réelles et vérifiées sur l'ensemble des gouvernorats tunisiens.
3. **Logistique et Analyses Agréées** : Accompagnement dans les analyses de conformité et le transport sécurisé.

Consultez le baromètre des prix en direct : https://zintoop.com/fr/prices"""

def php_escape(text):
    return json.dumps(text, ensure_ascii=False)

with open("database/seeders/StrategicSeoArticlesSeeder.php", "r", encoding="utf-8") as f:
    content = f.read()

# Pattern to find article with id 12
pattern = r'(\[\s*"id"\s*=>\s*12,[\s\S]*?\]\s*,\s*\]\s*,)'

new_article_code = f"""[
                "id" => 12,
                "title" => [
                    "ar" => {php_escape("المرجع الشامل لأسعار وسوق زيت الزيتون: موازين الإنتاج العالمي والاستهلاك، منافسة دول الجوار، حسابات القطوع والباز، ولماذا يضطر الفلاح للبيع تحت التكلفة؟")},
                    "en" => {php_escape("Comprehensive Guide to 2026/2027 Olive Oil Prices & Global Market Dynamics: Production vs Consumption, Regional Competitors, Extraction Yields & Mill Base Pricing")},
                    "fr" => {php_escape("Guide de Référence des Prix et Marché de l'Huile d'Olive 2026/2027 : Équilibre Mondial, Concurrence Régionale, Rendements d'Extraction et Coûts de Revient")},
                ],
                "category" => [
                    "ar" => {php_escape("تحليل السوق والأسعار")},
                    "en" => {php_escape("Market & Price Analysis")},
                    "fr" => {php_escape("Analyse du Marché et Prix")},
                ],
                "image" => "images/articles/market_price_charts.jpg",
                "is_active" => true,
                "content" => [
                    "ar" => {php_escape(article_12_ar)},
                    "en" => {php_escape(article_12_en)},
                    "fr" => {php_escape(article_12_fr)},
                ],
            ],"""

# Find block for id => 12
start_marker = '["id" => 12,'
if start_marker not in content:
    start_marker = '"id" => 12,'

idx_start = content.find(start_marker)
# Find start of array [ before idx_start
bracket_start = content.rfind('[', 0, idx_start)

# Find next article start (e.g. "id" => 13)
idx_next = content.find('"id" => 13,', idx_start)
bracket_end = content.rfind('[', idx_start, idx_next)

old_block = content[bracket_start:bracket_end]

updated_content = content[:bracket_start] + new_article_code + "\n            " + content[bracket_end:]

with open("database/seeders/StrategicSeoArticlesSeeder.php", "w", encoding="utf-8") as f:
    f.write(updated_content)

print("Successfully updated Article 12 in StrategicSeoArticlesSeeder.php!")
