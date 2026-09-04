import json
import re

# Read StrategicSeoArticlesSeeder.php
seeder_path = '/Users/zinehamdi/Sites/localhost/tuni-olive-hub/database/seeders/StrategicSeoArticlesSeeder.php'
with open(seeder_path, 'r', encoding='utf-8') as f:
    content = f.read()

article_11_ar_title = 'دليل تسجيل وحماية العلامة التجارية لزيت الزيتون في تونس (INNORPI): الإجراءات، الوثائق، والأسعار 2026'
article_11_en_title = 'How to Register and Protect an Olive Oil Trademark in Tunisia (INNORPI): Complete Guide, Fees & Procedures'
article_11_fr_title = 'Guide d\'Enregistrement et de Protection d\'une Marque d\'Huile d\'Olive en Tunisie (INNORPI) : Démarches, Tarifs et Classes'

article_11_ar_cat = 'الملكية الصناعية والعلامات'
article_11_en_cat = 'Trademarks & IP'
article_11_fr_cat = 'Propriété Industrielle'

article_11_image = 'images/articles/innorpi_trademark_olive_oil.jpg'

article_11_ar_content = """تعتبر تونس من أبرز القوى العالمية في إنتاج وتصدير زيت الزيتون البكر الممتاز، لكن الرهان الحقيقي اليوم لم يعد يقتصر على بيع الزيت سائباً (Bulk / Vrac)، بل في الانتقال الاستراتيجي نحو **التعليب وبناء علامات تجارية تونسية خاصة ومسجلة (Marque Déposée / Private Label)** تقتحم رفوف المتاجر العالمية وتضاعف القيمة المضافة لصالح الفلاح وصاحب المعصرة والمصدر التونسي.

"علامتك التجارية هي إمضاؤك وهويتك في السوق.. احمِها بتسجيلها قانونياً!"

---

### 1. الإطار القانوني ومبدأ الحماية في تونس

يخضع تسجيل وحماية العلامات التجارية في الجمهورية التونسية لأحكام **القانون عدد 36 لسنة 2001 المؤرخ في 17 أفريل 2001 المتعلق بعلامات الصنع والتجارة والخدمات**، بإشراف وزارة الصناعة عبر **المعهد الوطني للمواصفات والملكية الصناعية (INNORPI)**:

* **القاعدة القانونية الذهبية**: ملكية العلامة التجارية في تونس **تُكتسب بالإيداع والتسجيل الرسمي لدى INNORPI وليس بمجرد الاستعمال التجاري في السوق** (La propriété s'acquiert par le dépôt, non par l'usage).
* **الحقوق المكتسبة**: يمنحك التسجيل الحق الحصري في استغلال العلامة، بيعها، أو منح تراخيص استغلال تجارية، مع الحماية القانونية التامة ضد المقلدين والمنافسين غير الشرعيين.
* **مدة الحماية**: يوفر الإيداع حماية قانونية نافذة لمدة **10 سنوات كاملة**، وهي قابلة للتجديد بصفة غير محدودة عبر إيداع جديد.

---

### 2. الوثائق المطلوبة لملف إيداع العلامة التجارية

لإيداع ملف تسجيل علامتك التجارية الخاصة بزيت الزيتون، يتوجب تقديم الوثائق التالية:

1. **3 نظائر مطابقة للأصل من شعار / لوغو العلامة التجارية** (Design / Logo)، على ألا تتجاوز أبعاد التصميم **10 صم في الطول على 6 صم في العرض**.
2. **وصل خلاص معاليم الإيداع** لدى المعهد الوطني للمواصفات والملكية الصناعية (يتم الدفع نقداً أو ببطاقة بنكية على عين المكان بالقباضة).
3. **قائمة المنتجات و/أو الخدمات** المطلوب حمايتها وتغطيتها بالعلامة التجارية، محررة بدقة وفق التصنيف الدولي (تصنيف نيس - Classification de Nice).
4. **توكيل رسمي قانوني** في صورة تكليف وكيل أو محامٍ مفوض، أو **نسخة حديثة من السجل الوطني للمؤسسات (RNE)** بالنسبة للمسير والوكيل القانوني للشركة.

---

### 3. جدول المعاليم والرسوم الرسمية المحينة (INNORPI 2026)

| نوع الإجراء والخدمة | معلوم الإيداع الأول (TND TTC) | معلوم التجديد الدوري (TND TTC) |
|---|:---:|:---:|
| **البحث المسبق عن الأسبقية (Recherche d'antériorité)** *(اختياري وموصى به)* | **36,700 د.ت** | — |
| **إيداع ملف علامة تجارية (يغطي صنفاً واحداً)** | **596,000 د.ت** | **774,500 د.ت** |
| **تسجيل كل صنف إضافي للمنتجات أو الخدمات (Par classe supplémentaire)** | **119,000 د.ت** | **178,500 د.ت** |
| **تسليم الشهادة الرسمية لتسجيل العلامة (عند استكمال الإجراءات)** | **96,200 د.ت** | — |

> 💡 **ملاحظة هامة حول فحص الأسبقية**: المعهد الوطني للمواصفات والملكية الصناعية يسجل العلامات دون إجراء فحص تلقائي للجدة (Examen de nouveauté). لذلك، يُنصح دائماً بطلب "البحث عن الأسبقية" بمعلوم 36.700 د.ت للتأكد مسبقاً من عدم وجود علامة مسجلة مطابقة أو مشابهة تجنباً لأي نزاع قضائي.

---

### 4. دليل أصناف تصنيف «نيس» الدولي الخاص بزيت الزيتون (Classification de Nice)

يتضمن التصنيف الدولي 45 صنفاً، وتعتبر الأصناف التالية هي الأكثر حيوية واستراتيجية لمشروع زيت الزيتون في تونس:

* **الصنف 29 (Classe 29 - الصنف الإجباري والرئيسي)**:
  * يشمل **الزيوت والشحوم الغذائية (بما فيها زيت الزيتون البكر والبكر الممتاز بجميع أنواعه)**، المصبرات، ثمار الزيتون المصبرة والمخللة، والخضروات المحفوظة.
* **الصنف 31 (Classe 31)**:
  * يشمل المنتجات الفلاحية الخام غير المحولة، حبات الزيتون الطازجة، والبذور والنباتات الطبيعية.
* **الصنف 35 (Classe 35 - صنف التجارة والتوزيع)**:
  * يشمل الدعاية والإشهار، إدارة الأعمال التجارية، التسويق، التجارة الإلكترونية، وخدمات بيع وتوزيع زيت الزيتون بالجملة والتفصيل.
* **الصنف 39 (Classe 39 - اللوجستيك والشحن)**:
  * يشمل خدمات النقل، الشحن البحري، التعبئة، وتخزين الزيوت والمواد الغذائية.
* **الصنف 40 (Classe 40 - خدمات المعاصر والتحويل)**:
  * يشمل معالجة المواد، خدمات العصر الصناعي، التكسير، واستخراج الزيت لفائدة الغير.

---

### 5. عناوين ومقرات إيداع الملفات لدى INNORPI

يمكن إيداع الملفات ومتابعة التسجيل مباشرة في المراكز التالية:

* **المقر المركزي بتونس العاصمة**:
  * **العنوان**: نهج المعهد الوطني للمواصفات والملكية الصناعية، حي الخضراء، 1003 تونس.
  * **الهاتف**: 71806758 | **الفاكس**: 71807071
  * **الموقع الإلكتروني الرسمي**: www.innorpi.tn
* **المركز الجهوي بصفاقس**:
  * **العنوان**: 01 نهج بجاية، 3000 صفاقس.
  * **الهاتف**: 74298223 | **الفاكس**: 74211356
* **المركز الجهوي بسوسة**:
  * **العنوان**: نهج المنجي بالي، عمارة الهاجري، الطابق الثاني، شقة B24، سوسة.
  * **الهاتف**: 73226566 | **الفاكس**: 73214518

---

### 6. كيف ترافقك منصة زين توب في إطلاق وتصدير علامتك التجارية؟

بعد تسجيل علامتك التجارية لدى INNORPI، تفتح لك منصة **ZinToop** أبواب الأسواق العالمية من خلال:
* ربطك بأفضل وحدات التعبئة والتعليب المعتمدة في تونس المطابقة لمعايير ISO 22000 و IFS و BRC.
* المساعدة في استخراج الترقيم الدولي للباركود (GS1 / EAN-13) وتصميم الملصقات المطابقة للمواصفات الأوروبية والأمريكية.
* عرض علامتك التجارية أمام شبكة دولية من الموردين والمشترين وسلاسل المتاجر الكبرى.

ابدأ مشروع تعليب علامتك التجارية الخاصة اليوم:
https://zintoop.com/ar/علامة-خاصة-زيت-زيتون-تونس"""

article_11_en_content = """Tunisia is one of the world's leading producers of extra virgin olive oil. However, the true economic breakthrough lies in shifting from bulk commodity exports (vrac) to **bottling and building proprietary registered trademarks (Marque Déposée / Private Label)** that command premium margins on global supermarket shelves.

"Your brand is your signature in the marketplace. Protect it by registering it officially!"

---

### 1. Legal Framework & Protection Principle in Tunisia

Trademark registration and industrial property protection in the Republic of Tunisia are governed by **Law No. 2001-36 of April 17, 2001 on Trademarks, Trade Names and Service Marks**, administered by the **National Institute of Standardization and Industrial Property (INNORPI)** under the Ministry of Industry:

* **The Golden Legal Rule**: In Tunisia, trademark ownership is **acquired through official filing and registration with INNORPI, not through commercial use alone** (*La propriété s'acquiert par le dépôt, non par l'usage*).
* **Exclusive Rights**: Official registration grants exclusive commercial exploitation rights, licensing authority, and comprehensive judicial protection against counterfeiters and unfair imitators.
* **Duration of Protection**: A registered trademark provides **10 years of legal protection**, renewable indefinitely upon subsequent filings.

---

### 2. Mandatory Filing Requirements & Documents

To file a trademark application for your olive oil brand at INNORPI, you must submit:

1. **Three (03) identical prints of the trademark logo/design**, not exceeding **10 cm in length by 6 cm in width**.
2. **Official receipt of payment** for filing fees paid directly at the INNORPI cashier desk.
3. **List of goods and/or services** to be protected, drafted according to the international **Nice Classification**.
4. **Power of Attorney (Pouvoir)** if filing through a legal representative/mandatary, or a recent copy of the **National Business Registry (RNE)** for company managers.

---

### 3. Official Fee Structure (INNORPI Rates in TND TTC)

| Procedure / Service | Initial Filing Fee (TND TTC) | Renewal Fee (TND TTC) |
|---|:---:|:---:|
| **Prior Art Search (Recherche d'antériorité)** *(Recommended)* | **36.700 TND** | — |
| **Trademark Application Filing (Single Class)** | **596.000 TND** | **774.500 TND** |
| **Additional Product / Service Class (Par classe supplémentaire)** | **119.000 TND** | **178.500 TND** |
| **Issuance of Trademark Registration Certificate (Délivrance)** | **96.200 TND** | — |

> 💡 **Important Note on Novelty Examination**: INNORPI registers trademarks without performing an automatic novelty examination. Applicants are strongly advised to conduct a prior art search (**36.700 TND**) prior to filing to verify that no identical or confusingly similar mark already exists.

---

### 4. Nice Classification Guide for the Olive Oil Industry

The international Nice Classification system comprises 45 classes. For olive oil producers, mills, and brand owners, the essential classes are:

* **Class 29 (Primary & Mandatory)**:
  * Includes **Edible oils and fats (including all extra virgin, virgin, and organic olive oils)**, preserved olives, table olives, and preserved vegetables.
* **Class 31**:
  * Raw and unprocessed agricultural products, fresh whole olives, seeds, and live plants.
* **Class 35 (Commercial & Retail)**:
  * Advertising, business management, commercial administration, wholesale, retail, and e-commerce distribution of olive oil.
* **Class 39 (Logistics & Transport)**:
  * Transport, marine freight, packaging, and warehousing of goods.
* **Class 40 (Milling & Processing Services)**:
  * Material treatment, custom olive milling, extraction, and pressing services for third parties.

---

### 5. INNORPI Submission Offices & Regional Centers

Applications can be submitted directly at the following official locations:

* **Tunis Headquarters**:
  * **Address**: INNORPI, Rue de l'INNORPI, Cité El Khadhra, 1003 Tunis.
  * **Phone**: (+216) 71 806 758 | **Fax**: (+216) 71 807 071
  * **Website**: www.innorpi.tn
* **Sfax Regional Center**:
  * **Address**: 01 Rue Béjaïa, 3000 Sfax.
  * **Phone**: (+216) 74 298 223 | **Fax**: (+216) 74 211 356
* **Sousse Regional Center**:
  * **Address**: Rue Mongi Bali, Immeuble Hajri, 2nd Floor, Apt B24, Sousse.
  * **Phone**: (+216) 73 226 566 | **Fax**: (+216) 73 214 518

---

### 6. How ZinToop Accelerates Your Brand Launch & Export Growth

Once your trademark is registered, **ZinToop** connects your private label with international buyers:
* Seamless matching with ISO 22000, IFS, and BRC certified contract packaging units in Tunisia.
* Guidance for international GS1 / EAN-13 barcodes and export-compliant labeling.
* Direct showcase to global retail chains, gourmet distributors, and overseas importers.

Start your private label olive oil project today:
https://zintoop.com/en/private-label-olive-oil-tunisia"""

article_11_fr_content = """La Tunisie s'affirme comme l'un des leaders mondiaux de la production d'huile d'olive extra vierge. Cependant, la véritable valeur ajoutée réside aujourd'hui dans l'abandon progressif des exportations brutes en vrac au profit du **conditionnement et de la création de marques déposées tunisiennes propres**, capables de conquérir les rayons de la grande distribution internationale.

« Votre marque, c'est votre signature. Protégez-la en la déposant ! »

---

### 1. Cadre Réglementaire et Principe de Protection en Tunisie

Le dépôt et la protection des marques en République Tunisienne sont régis par la **Loi N° 2001-36 du 17 avril 2001 relative aux marques de fabrique, de commerce ou de services**, sous l'égide du Ministère de l'Industrie via l'**Institut National de la Normalisation et de la Propriété Industrielle (INNORPI)** :

* **Règle Juridique Fondamentale** : La propriété d'une marque commerciale en Tunisie **s'acquiert exclusivement par le dépôt auprès de l'INNORPI, et non par le simple usage commercial** (*La propriété s'acquiert par le dépôt, non par l'usage*).
* **Droits Exclusifs** : Le dépôt confère un droit exclusif d'exploitation commerciale (vente, concession de licences d'exploitation) et une protection judiciaire totale contre les contrefacteurs et imitateurs.
* **Durée de Protection** : Le dépôt d'une marque assure une protection juridique de **10 ans**, renouvelable indéfiniment par nouveau dépôt.

---

### 2. Pièces et Documents Requis pour le Dépôt

Pour constituer et déposer votre dossier de marque auprès de l'INNORPI, les pièces suivantes sont exigées :

1. **Trois (03) exemplaires identiques du logo / emblème de la marque**, ne dépassant pas **10 cm de longueur sur 6 cm de largeur**.
2. **Une quittance de paiement des redevances de dépôt** effectuée auprès de la régie de recettes de l'INNORPI (paiement sur place).
3. **La liste des produits et/ou services** pour lesquels la marque est ou sera utilisée, libellée conformément à la **Classification de Nice**.
4. **Un pouvoir régulier** en cas de recours à un mandataire, ou une copie récente du **Registre National des Entreprises (RNE)** pour le gérant de société.

---

### 3. Grille Tarifaire Officielle (Taxes INNORPI 2026 en Dinars Tunisiens TTC)

| Nature de l'Acte / Service | Tarif au Dépôt Initial (TND TTC) | Tarif au Renouvellement (TND TTC) |
|---|:---:|:---:|
| **Recherche d'antériorité** *(sur demande, recommandée)* | **36,700 DT** | — |
| **Dépôt d'une marque (avec une seule classe)** | **596,000 DT** | **774,500 DT** |
| **Enregistrement par classe supplémentaire** | **119,000 DT** | **178,500 DT** |
| **Délivrance du certificat d'enregistrement de marque** | **96,200 DT** | — |

> 💡 **Avertissement sur la Recherche d'Antériorité** : L'INNORPI enregistre les marques sans procéder à un examen de nouveauté. Il appartient au déposant de s'assurer de l'absence de dépôts antérieurs identiques ou similaires via une recherche préalable (**36,700 DT**).

---

### 4. Guide des Classes de la Classification de Nice pour la Filière Oléicole

La Classification de Nice comporte 45 classes. Pour les producteurs, moulins et conditionneurs d'huile d'olive, les classes stratégiques sont :

* **Classe 29 (Classe Principale et Obligatoire)** :
  * Comprend les **huiles et graisses comestibles (dont l'huile d'olive extra vierge, vierge et biologique)**, les olives de table conservées et les produits dérivés.
* **Classe 31** :
  * Produits agricoles bruts non transformés, olives fraîches, semences et plants d'oliviers.
* **Classe 35 (Commerce et Distribution)** :
  * Publicité, gestion des affaires commerciales, distribution commerciale, vente en gros/détail et e-commerce d'huile d'olive.
* **Classe 39 (Logistique et Transport)** :
  * Transport maritime, emballage, mise en bouteille et entreposage de marchandises.
* **Classe 40 (Trituration et Transformation)** :
  * Traitement de matériaux, services de trituration, extraction et pressage d'olives au moulin pour le compte de tiers.

---

### 5. Adresses et Centres Régionaux de Dépôt de l'INNORPI

Vous pouvez déposer votre dossier dans les centres officiels suivants :

* **Siège Central de Tunis** :
  * **Adresse** : Rue de l'INNORPI, Cité El Khadhra, 1003 Tunis.
  * **Téléphone** : (+216) 71 806 758 | **Fax** : (+216) 71 807 071
  * **Site Web Officiel** : www.innorpi.tn
* **Centre Régional de Sfax** :
  * **Adresse** : 01 Rue Béjaïa, 3000 Sfax.
  * **Téléphone** : (+216) 74 298 223 | **Fax** : (+216) 74 211 356
* **Centre Régional de Sousse** :
  * **Adresse** : Rue Mongi BALI, Immeuble Hajri, 2ème étage, B24, Sousse.
  * **Téléphone** : (+216) 73 226 566 | **Fax** : (+216) 73 214 518

---

### 6. Comment ZinToop Accompagne le Lancement de Votre Marque

Une fois votre marque déposée auprès de l'INNORPI, **ZinToop** vous met en relation avec des unités d'embouteillage certifiées (ISO 22000, IFS, BRC) et facilite l'exportation vers les acheteurs internationaux.

Découvrez nos solutions de marque privée :
https://zintoop.com/fr/marque-privee-huile-olive-tunisie"""

# Construct the PHP array block for Article 11
def php_escape(s):
    # escape backslashes and double quotes
    return s.replace('\\', '\\\\').replace('"', '\\"').replace('$', '\\$')

article_11_code = f"""            [
                "id" => 11,
                "title" => [
                    "ar" => "{php_escape(article_11_ar_title)}",
                    "en" => "{php_escape(article_11_en_title)}",
                    "fr" => "{php_escape(article_11_fr_title)}",
                ],
                "category" => [
                    "ar" => "{php_escape(article_11_ar_cat)}",
                    "en" => "{php_escape(article_11_en_cat)}",
                    "fr" => "{php_escape(article_11_fr_cat)}",
                ],
                "image" => "{article_11_image}",
                "is_active" => true,
                "content" => [
                    "ar" => "{php_escape(article_11_ar_content)}",
                    "en" => "{php_escape(article_11_en_content)}",
                    "fr" => "{php_escape(article_11_fr_content)}",
                ],
            ],"""

# Replace the block for id => 11 in StrategicSeoArticlesSeeder.php
pattern = r'\[\s*"id"\s*=>\s*11,.*?\]\s*,\s*(?=\[\s*"id"\s*=>\s*12)'
new_content = re.sub(pattern, article_11_code + "\n", content, flags=re.DOTALL)

with open(seeder_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Updated StrategicSeoArticlesSeeder.php successfully!")
