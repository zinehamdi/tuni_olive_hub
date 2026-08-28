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
                "id" => 1,
                "title" => [
                    "ar" => "لماذا يُعتبر زيت الزيتون التونسي \"الفلتر\" الطبيعي لصحتك وجمالك؟",
                    "en" => "Tunisian Olive",
                    "fr" => "Tunisia Huile d’olive",
                ],
                "category" => [
                    "ar" => "صحة",
                    "en" => "Health",
                    "fr" => "Sante",
                ],
                "content" => [
                    "ar" => "هل تساءلت يوماً لماذا كان أجدادنا يتمتعون بصحة حديدية وبشرة صافية رغم قسوة الحياة؟ \r\nالسر لم يكن في الصيدليات، بل في \"الخوابي\" الفخارية المملوءة بزيت الزيتون. اليوم، ومع صعود صيحة \r\n \"الأكل الصحي\" (Healthy Food)*\r\n، يعود العالم ليركع أمام معجزة \"الذهب الأخضر\"، بينما لا يزال البعض يستهلك زيوتًا نباتية مصنعة هي في الحقيقة \"سموم مقنعة\".\r\nالمعجزة الغذائية: العلم يتحدث\r\nزيت الزيتون ليس مجرد دهون، بل هو صيدلية بيولوجية. يحتوي على نسبة تصل إلى **80% من حمض الأوليك** (Oleic Acid)، وهو دهون أحادية غير مشبعة تعمل كمكنسة لشرايين القلب.\r\n * **العدو الأول للالتهابات:** يحتوي زيت الزيتون البكر الممتاز على مادة **\"الأوليوكانثال\" (Oleocanthal)**، وهي مضاد تأكسد طبيعي يعمل في الجسم تماماً مثل الـ \"إيبوبروفين\"، حيث يقتل الالتهابات الصامتة التي تسبب الأمراض المزمنة.\r\n * **وداعاً للكوليسترول الضار:** أثبتت الدراسات أن استبدال الزيوت النباتية بزيت الزيتون يخفض نسبة الكوليسترول الضار (LDL) بنسبة تصل إلى **15%** في أسابيع قليلة، مما يحمي من الجلطات.\r\n سرّ البشرة المنيرة: \"بوتوكس\" من الطبيعة\r\nإذا كنتِ تبحثين عن النضارة، فتوقفي عن شراء الكريمات الكيميائية الباهظة، والتفتي إلى زيت الزيتون. إنه \"الغذاء الملكي\" للبشرة بفضل تركيبته الفريدة:\r\n 1. **مضادات التأكسد (فيتامين E و A):** زيت الزيتون غني بـ **البوليفينول**، وهي جزيئات تحارب \"الجذور الحرة\" المسؤولة عن التجاعيد. دهن الوجه بزيت الزيتون (أو استعماله في الأكل) يمنح البشرة مرونة تمنع ظهور الخطوط الدقيقة.\r\n 2. **التفتيح والنضارة:** بفضل قدرته العالية على التغلغل، يقوم الزيت بتنظيف المسام وترطيبها بعمق، مما يعطي الوجه ذلك \"اللمعان الصحي\" (Glow) الذي تحاول مستحضرات التجميل تقليده.\r\n 3. **إصلاح الخلايا:** يحتوي على مادة **السqualene**، وهي مادة موجودة طبيعياً في جلد الإنسان وتقل مع تقدم السن. زيت الزيتون يعوض هذا النقص، مما يجعل الوجه يبدو أكثر شباباً وإشراقاً.\r\n### 🚫 الزيوت النباتية vs الذهب الأخضر: لماذا المقاطعة واجبة؟\r\nالزيوت النباتية (صويا، عباد شمس، ذرة) تخضع لعمليات تكرير كيميائية وحرارة عالية جداً تجعلها تفقد أي قيمة غذائية وتتحول إلى دهون مشبعة تسبب الالتهابات السمنة.\r\n * **زيت الزيتون:** يُعصر بارداً، يحتفظ بإنزيماته، وهو الوحيد الذي يتعرف عليه جسمك كصديق.\r\n * **المعادلة بسيطة:** قطرة زيت زيتون هي \"بناء\" لخلية سليمة، وقطرة زيت نباتي هي \"عبء\" على الكبد والقلب.\r\n تونس: جودة عالمية في متناول يدك\r\nنحن في تونس ننتج زيتاً يُصنف من الأجود عالمياً بفضل المناخ المشمس الذي يزيد من تركيز **البوليفينول**. التوجه نحو \"الهلثي فود\" الحقيقي يبدأ من تغيير الزيت في مطبخك. هو ليس مجرد طعام، هو قرار بالاستثمار في جسدك ليعيش طويلاً دون أوجاع.\r\n**نصيحة ذهبية:** ملعقة زيت زيتون على الريق يومياً ليست مجرد عادة ريفية، بل هي عملية \"غسيل\" شاملة للجهاز الهضمي وتنشيط فوري لخلايا الدماغ.\r\n> \r\n**هل مازلت تفكر؟ استبدل الزجاجة البلاستيكية بالذهب التونسي اليوم، واجعل وجهك وصحتك ينطقان بالفرق!**",
                    "en" => "content",
                    "fr" => "Content",
                ],
                "image" => "storage/articles/b2c42100-37d6-4654-af94-29f187a68dd0.webp",
                "is_active" => true,
                "created_at" => "2026-05-07T13:40:27.000000Z",
                "updated_at" => "2026-05-08T00:40:27.000000Z",
            ],
            [
                "id" => 2,
                "title" => [
                    "ar" => "العمق التاريخي، العزة الوطنية",
                    "en" => "Digital Transformation in Olive Farming",
                    "fr" => "Transformation numérique de l'oléiculture",
                ],
                "category" => [
                    "ar" => "مقال",
                    "en" => "Article",
                    "fr" => "Article",
                ],
                "content" => [
                    "ar" => "🇹🇳 زيتونة تونس: إرثُ الأنبياء، أمانةُ الأجداد، ورهانُ المستقبل\r\nليست مجرد شجرة، بل هي الذاكرة الحية لأرضٍ لم تعرف الانكسار. من \"زيتونة العكاري\" بجرجيس التي تتحدى الزمن منذ أكثر من 2500 سنة، إلى غابات الشمال الشامخة، تروي تونس قصة حضارة عُجنت بالزيت والتراب.\r\n### 📜 الحبكة التاريخية: أرضٌ سُميت على اسم شجرتها\r\nيرتبط اسم تونس ارتباطاً وثيقاً بـ **\"الزيتونة\"**. لم يكن اختيار اسم **\"جامع الزيتونة\"** المعمور مجرد صدفة أو تيمناً بمكان؛ بل كان إقراراً بأن هذه الأرض هي \"أرض الزيتونة\" بامتياز.\r\n * **في العهد القرطاجي:** جعل \"حنبعل\" من غرس الزيتون واجباً وطنياً لجنوده لإعمار الأرض، إيماناً منه بأن القوة الاقتصادية هي عماد القوة العسكرية.\r\n * **في العهد الروماني:** كانت تونس تُلقب بـ \"مطمورة روما\"، ليس فقط للقمح، بل بفضل زيت \"سبيطلة\" و\"تيسدروس\" (الجم) الذي كان يُضيء قصور الإمبراطورية.\r\nلقد آمن الأجداد بأن \"لكل من اسمه نصيب\"، فكان نصيبنا من المجد أن نكون حراس هذه الشجرة المباركة التي ذكرها الله في كتابه.\r\n### 🥈 تونس.. العملاق الذي يستعيد مكانته\r\nاليوم، تونس ليست مجرد لاعب ثانوي؛ نحن **ثاني منتج عالمي لزيت الزيتون**. هذا الرقم ليس مجرد إحصائية، بل هو \"صيحة فزع\" للمنافسين وإثبات جدارة للعالم.\r\nنحن نمتلك أكثر من **100 مليون شجرة زيتون**، وكل شجرة هي بمثابة بئر نفط متجدد، لا ينضب ولا يلوث، بل يمنح الحياة.\r\n### 💰 الذهب الأخضر: المحرك الجديد للدينار\r\nفي لغة الأرقام الحديثة، يُعتبر برميل زيت الزيتون التونسي **\"الذهب الأخضر\"**. وتثمين هذا المنتج ليس خياراً، بل هو معركة استقلال اقتصادي:\r\n * **الجودة العالمية:** الزيت التونسي يحصد سنوياً مئات الميداليات ذهبية في مسابقات الجودة العالمية (من نيويورك إلى طوكيو).\r\n * **القوة التصديرية:** برميل الزيت هو السند الحقيقي الذي سيحلق بالدينار التونسي. عندما ننتقل من تصدير الزيت \"صبّ\" (بكميات كبرى) إلى تصديره معلباً بعلامات تجارية تونسية فاخرة، فنحن نبيع \"هيبة دولة\" لا مجرد سائل.\r\n### 🌿 دعوتنا لكم\r\nإن وجود الزيتون على أرضنا هو \"صك بركة\". ودورنا اليوم، كلّ من موقعه، أن نثمن هذه الثروة. سواء كنت فلاحاً يدرك قيمة \"الزيت النضوح\"، أو مصدراً يطرق أبواب الأسواق العالمية، أو مستهلكاً يفتخر بمنتج بلاده.\r\nنحن كبار، وتاريخنا شاهد، ومستقبلنا يكتبه هذا السائل الذهبي الذي يتدفق من قلب رمالنا وشمالنا.\r\n**تونس هي الزيتونة، والزيتونة هي تونس.. فهل أدركنا حجم الكنز الذي بين أيدينا؟**",
                    "en" => "How IoT and AI are revolutionizing Tunisian olive oil production and quality control.",
                    "fr" => "Comment l'IdO et l'IA révolutionnent la production et le contrôle qualité de l'huile d'olive tunisienne.",
                ],
                "image" => "storage/articles/d8cf4e5a-47da-4d70-8fc2-d2c783571646.webp",
                "is_active" => true,
                "created_at" => "2026-05-07T14:21:53.000000Z",
                "updated_at" => "2026-05-08T00:05:22.000000Z",
            ],
            [
                "id" => 5,
                "title" => [
                    "ar" => "دليل منتجي الزيت زيتون العضوي في تونس ودقة المعايير",
                    "en" => "Article",
                    "fr" => "Article",
                ],
                "category" => [
                    "ar" => "مقال",
                    "en" => "Article",
                    "fr" => "Article",
                ],
                "content" => [
                    "ar" => "شنوة معناه زيت زيتون عضوي (بيولوجي)؟\r\nالفلاحة البيولوجية في تونس موش مجرد غياب الدواء والكيمياوي في التحاليل، هي منظومة إنتاج كاملة تحترم الطبيعة والتوازن البيئي من أول نهار في الحقل حتى لنهار لّي يتباع فيه الزيت. المنظومة هذه تمنع منعاً باتاً استعمال الأسمدة الكيمياوية المصنعة والمبيدات الهرمونية، وترتكز على حماية صحة المستهلك والتربة وتضمن الإنصاف في استغلال الثروات المائية والطبيعية.\r\nتونس اليوم تعتبر من الرواد في العالم في مساحات الزيتون البيولوجي وحجم التصدير، وباش نحافظوا على السمعة هذه، الدولة حطت قوانين صارمة وكراسات شروط دقيقة يلزم كل فلاح يحترمها باش ياخذ الشهادة ويصنّف زيته عضوي ممتاز. وقاعدة أساسية يلزم نعرفوها هي أنو الزيت البيولوجي يلزمو تتبّع كامل وواضح، يعني الفلاح يلزمو يقيد كل شاردة وواردة في سجل الضيعة، من نوع الغبار لّي استعملو حتى لتاريخ التقليع والعصر.\r\nأولاً: الشروط الأساسية في الضيعة (مرحلة الإنتاج الفلاحي)\r\n * فترة التحول: بش تحول ضيعة من فلاحة عادية لفلاحة بيولوجية، ما تجمش تبيع زيتها بصفة بيولوجي من العام الأول. يلزم تقضي فترة تحول تدوم 3 سنين كاملين للزيتون. في السنين هذه، الضيعة تقعد تحت المراقبة باش التربة والأشجار تتنظف تماماً من رواسب الدوايات القديمة، والمنتوج يتباع وقتها تحت اسم زيت في طور التحول.\r\n * مصدات الرياح وحماية الحوزة: يلزم الضيعة البيولوجية تكون محمية من التلوث الجاري من عند الجيران لّي يستعملوا في الكيمياوي. الفلاح يلزمو يزرع مصدات رياح طبيعية (كيف الهندي، السرو، أو أشجار كثيفة) أو يخلي حزام أمان كافي باش رذاذ المداواة متاع الجار ما يوصلش لأشجار الزيتون العضوي.\r\n * وقاية النباتات ومكافحة الأمراض: المكافحة هنا تعتمد على الوقاية الطبيعية كيف تقوية الشجرة بالتقليم الصحيح، تنظيف الأرض، وتشجيع الحشرات النافعة لّي تاكل الحشرات الضارة (كيف ذبابة الزيتون). وإذا لزم الأمر، يستعمل الفلاح كان المواد المرخص فيها في كراس الشروط كيف محلول النحاس أو الكبريت والمصايد الغذائية والجنسية، ويمنع أي دواء كيمياوي تركيبي.\r\nثانياً: الجزء التقني المفصل – بروتوكول التسميد العضوي وتغذية التربة\r\nبما أنو الأسمدة الكيمياوية (كيف الأمونياك، اليوريا، والفسفاط الاصطناعي) ممنوعة تماماً، تغذية شجرة الزيتون تعتمد على المادة العضوية والدورة الطبيعية للتربة وحسب البروتوكول التالي:\r\n * استعمال الغبار المخمر: موش أي غبار يتحط في الأرض. الغبار الفرش يجم يجيب أمراض وفطريات وبذور أعشاب طفيلية ويحرق عروق الشجرة خاطر فيه نسبة أمونياك عالية. يلزم الفلاح يستعمل غبار حيوانات (أبقار، أغنام، أو دواجن) يكون مخمر بالكامل لمدة لا تقل عن 4 إلى 6 أشهر مع التقليب والترطيب الدقيق. عملية التخمير ترفع الحرارة لـ 60 درجة لّي تقتل الجراثيم وتخلي المواد المغذية (الآزوت، الفسفور، البوتاسيوم) سهلة ومستقرة للامتصاص من طرف عروق الشجرة.\r\n * التسميد الأخضر: هذه تقنية ممتازة وتعتمد على زراعة البقوليات (كيف الفول المصري، القرط، أو الجلبانة) بين سطور الزيتون في الخريف (أكتوبر ونوفمبر). البقوليات عندها خاصية فريدة وهي تثبيت الآزوت الموجود في الهواء في التربة عن طريق عروقها. في وقت التزهير (فيفري ومارس)، يقوم الفلاح بحرث الأرض وقلب النباتات هذه وسط التربة، الشيء لّي يعطي مادة عضوية غنية جداً تحسن خصوبة وهيكلة الأرض وتزيد من قدرتها على حبس الرطوبة.\r\n * تثمين المرجين: يجم الفلاح يرش المرجين في الضيعة كسماد طبيعي ممتاز غني بالبوتاسيوم والمادة العضوية، لكن بشروط دقيقة جداً وهي أنو الكمية ما تفوتش 50 متر مكعب في الهكتار الواحد في العام، ويلزم يترش بطريقة متجانسة وبعيد على جذع الشجرة بـ 1 متر على الأقل، ويمنع رشه في الأراضي لّي فيها مائدة مائية قريبة أو منحدرة برشا، وبشرط يكون مرجين خام وموش مخلط بمواد كيميائية.\r\n * تثمين الفيتورة: تخلط الفيتورة مع الغبار الحيواني أثناء عملية التخمير لإنتاج كومبوست ممتاز غني بالكربون لّي يحسن قدرة التربة على حفظ الماء ويوكل الكائنات الحية الدقيقة النافعة في الأرض.\r\n * استعمال المستخلصات الطبيعية والأعشاب البحرية: لتحسين نمو الأوراق وتزهير ممتاز، يجم المنتج يستعمل رش ورقي بمستخلصات الأعشاب البحرية أو الأحماض الأمينية ذات الأصل الطبيعي المرخص فيها، لّي تعطي دفع قوي للشجرة في الأوقات الحساسة (كيف عقد الثمار وعند الشياح ونقص الأمطار).\r\nثالثاً: معايير التحويل داخل المعصرة (مرحلة العصر والتعبئة)\r\nدقة المعايير تواصل حتى وسط المعصرة باش ما يتخلطش الزيت البيولوجي مع العادي:\r\n * نظافة خطوط الإنتاج: يلزم المعصرة تتغسل وتتطهر بالكامل بمواد تنظيف مصادق عليها قبل ما تبدا تعصر في الزيتون البيولوجي. برشا معاصر يخصصوا الأيام الأولى من الأسبوع أو الساعات الأولى من النهار للزيت البيولوجي فقط.\r\n * العصر البارد: درجة حرارة عجينة الزيتون والماء المضاف يلزمها ما تفوتش 27 درجة مئوية باش نحافظوا على المنافع والروائح الطيارة.\r\n * خزانات التخزين: الزيت يتخزن في خزانات مصنوعة من الإينوكس الغذائي معزولة تماماً ومكتوب عليها بوضوح زيت زيتون بيولوجي مع رقم الدفعة.\r\nرابعاً: منظومة المراقبة والشهادات (كيفاش تاخذ البيو؟)\r\nالمراقبة في تونس تعملها شركات عالمية معتمدة ومسجلة لدى وزارة الفلاحة (كيف إيكوسيرت، سي سي بي بي، وغيرها) والمنظومة تمشي حسب المراحل التالية:\r\n * مرحلة الانخراط والعقد: يقدم الفلاح مطلب لهيكل المراقبة مع كشف كامل لمساحة الضيعة وموقعها وتاريخها، وهوني تبدا احتساب فترة التحول القانونية لّي تدوم 3 سنين.\r\n * مرحلة التفتيش الميداني: يجي المفتش للضيعة (مرة في العام إجباري، وزيارات فجئية) يثبت في السجلات والمخازن والأرض باش يتأكد لّي الفلاح قاعد يطبق في كراس الشروط حرفياً.\r\n * مرحلة التحاليل المخبرية: أخذ عينات من التربة، الأوراق، والزيت وتتعدى للمخبر للبحث عن أي أثر للمبيدات وإثبات نظافة المنتج علمياً بنسبة 100%.\r\n * مرحلة الشهادة والرمز: إصدار شهادة المطابقة البيولوجية وتجدد كل عام، وهي لّي ترخص لطباعة الرمز التجاري بيو والتصدير للخارج.\r\nخامساً: الامتيازات والتشجيعات المالية لّي تعطيهم الدولة التونسية\r\nالدولة التونسية بش تشجع الفلاحة البيولوجية، حطت حزمة من المنح والامتيازات المالية الهامة للفلاحة والشركات التعاونية:\r\n * منحة المعدات والتجهيزات بنسبة 70%: ياخذ الفلاح منحة توصل لـ 70% من حق الماكينات والتجهيزات الخاصة بنمط الإنتاج البيولوجي (كيف ماكينات الكومبوست، معدات الحش، إلخ) بسقف يوصل لـ 200 ألف دينار. والنسبة هذه تكون 50% للشركات التعاونية ومجامع التنمية.\r\n * تغطية مصاريف المراقبة بنسبة 50%: الدولة ترجع للفلاح 50% من المصاريف لّي يدفعها كل عام لشركات المراقبة باش تاخذ الشهادة، وهذا باش تعاون صغار الفلاحة وتخفف عليهم العبء المالي الدوري.\r\n * الإعفاءات الجبائية: تسهيلات وإعفاءات من الأداءات عند توريد بعض المواد البيولوجية المرخص فيها، ومسالك ديوانية تفضيلية وسريعة عند تصدير الزيت.\r\nسادساً: الهياكل الجهوية لّي تعاون الفلاح (مثال صفاقس والوسط)\r\nوزارة الفلاحة حطت في كل مندوبية جهوية قسم خاص بالفلاحة البيولوجية. في الإدارة الجهوية للتنمية الفلاحية بصفاقس (طريق المطار كم 5.5) مثلاً، فما إحاطة كاملة مقسمة على الدوائر التالية:\r\n * دائرة الإرشاد والإحاطة والبرمجة: هذه الدائرة لّي تعمل التكوين المستمر، الأيام الإعلامية، والمدارس الحقلية باش تعلم الفلاح كيفاش يخدم الكومبوست ويداوي بالمرخص.\r\n * دائرة التنمية والبحث والتحاليل وتثبيت النمط: تخرج للميدان، تتابع الفلاحة في فترة التحول، وتعمل المعاينات الفنية وتنسق التحاليل المخبرية.\r\n * دائرة تنشيط الهياكل المهنية والتسويق والتصدير: تعاون الفلاحة والمجامع باش يشاركوا في المعارض الكبرى (كيف معرض سياماب ومعرض ميدأويل) وتسهل ربطهم بالشركات المصدرة والأسواق العالمية.\r\nخلاصة وتوصيات\r\nمشروع زيت الزيتون العضوي في تونس هو مشروع ناجح وعنده مستقبل كبير ومربح برشا، خاطر الأسعار متاعو في العالم دايماً طالعة والمستهلك ولى يلوج على الحاجة الصحية. السر الكامل للنجاح هو دقة المعايير والانضباط التام؛ قيد كل شيء في السجلات، اخدم التسميد العضوي بطرق علمية ومخمرة، واقترب دايماً من خلايا الإرشاد الفلاحي باش تستفاد من المنح والخبرات التقنية وتضمن أعلى جودة لزيوتنا التونسية.",
                    "en" => "Soon…",
                    "fr" => "Prochainement",
                ],
                "image" => "storage/articles/08be1800-4261-4b80-ad47-cf736b332900.webp",
                "is_active" => true,
                "created_at" => "2026-05-16T10:23:09.000000Z",
                "updated_at" => "2026-05-16T10:26:16.000000Z",
            ],
            [
                "id" => 6,
                "title" => [
                    "ar" => "📢 حقيقة الـ 1.2 مليار شجرة زيتون",
                    "en" => "Article",
                    "fr" => "Article",
                ],
                "category" => [
                    "ar" => "مقال",
                    "en" => "Article",
                    "fr" => "Article",
                ],
                "content" => [
                    "ar" => "علاش الاستثمار في الزيت التونسي يطير طيران رغم \"وفرة\" الأرقام؟ 🌿 حِسبة واقعية لأول مرة!\r\nيا فلاح، ويا مستثمر، برشة ناس تسمع الأرقام الرسمية متاع المجلس الدولي للزيتون (IOC) لعام 2026، وتقول: \"العالم فيه 1.2 مليار شجرة زيتون، وتونس وحدها فيها قريب 100 مليون شجرة.. مالا السوق باش يتعبى والأسعار تطيح!\"  \r\nاليوم جينا نحسبوها معكم بالمنطق، بالعلم، وبواقع الميدان الي ما تكتبوش التقارير الرسمية، وباش نكتشفوا علاش قطاع زيت الزيتون يبقى أقوى وأضمن استثمار للمستقبل.\r\n🔍 1. الأرقام الرسمية.. والـمخفي أعظم! (الزرع غير المحصى)\r\nالتقارير تقول 1.2 مليار شجرة في العالم، لكن في الواقع، الرقم الحقيقي أكبر بكثير!  \r\n الأشجار المنسية: هناك ملايين الأشجار في جبال تونس وأرياف حوض المتوسط مزروعة ومنتجة، لكنها خارج الإحصائيات الرسمية والمنظومات العقارية.\r\n الاستهلاك خارج التعداد (زيت العولة): الإحصائيات الدولية تحسب فقط الزيت الموجه للشركات والتصدير. لكن في تونس وبقية دول المتوسط، هناك كميات هائلة من الزيت تعبر مباشرة من \"المعصرة إلى دار الفلاح والمواطن\" (زيت العولة السنوية) دون أن تدخل في أي تّعداد رسمي.\r\n📉 2. صدمة الـ 3%: كثرة الزيتون.. وندرة الزيت!\r\nرغم كل هذا الزرع غير المحصى والوفرة في الغابات، خلينا نعطيكم الصدمة الرقمية:\r\nإنتاج زيت الزيتون في العالم (حوالي 3 مليون طن) لا يمثل سوى 3% فقط من إجمالي الزيوت النباتية الي يستهلكها البشر (كيف زيت الصويا والنخيل)!\r\nزيت الزيتون يبقى منتجاً نادراً ونخبوياً (Luxury Product) في العالم، والوفرة هذه ما هي إلا وهم، لأن السوق العالمي جائع دائماً للزيت.\r\nالانفجار العالمي نحو \"الغذاء الصحي\" (زيتك مش للماكلة.. زيتك دوا!) 🔬\r\nالعالم اليوم في 2026 مرعوب من الأمراض والمواد المصنعة، وهناك توجه عالمي غير مسبوق نحو التغذية الصحية. أحدث المخابر والدراسات الطبية أثبتت أن الزيتون الذي يتحمل شمس وجفاف تونس القاسي، يفرز أعلى نسب من مضادات الأكسدة والـ \"بوليفينول\" لحماية نفسه.\r\nالمستهلك في الخليج، العراق، أمريكا، وأوروبا ما عادش يشري في زيت زيتون لمجرد الطبخ؛ هو مستعد يدفع بالدولار واليورو لأنه يعتبره \"دواء وقائياً\" وصحة لعائلته! الطلب العالمي على الزيت البكر الممتاز والمعصور على البارد (EVOO) قاعدة تزيد أرقامه بجنون يفوق بكثير حجم الإنتاج الفعلي.  \r\n🛠️ كيفاش ZinToop ترجع الوفرة المخفية هذي فلوس في جيبك؟\r\nالمشكلة مش في كثرة الزيتون، المشكلة في \"السمسار العشوائي\" الي يستغل عدم إحصاء صابتك ويشريها منك برخص التراب.\r\nهنا يجي دور ZinToop (منصة الزين لزيت الزيتون التونسي):\r\nإحنا ما يهمناتش السمسار شنوة يقول. إحنا نحسبوا جودة زيتك علمياً. إذا زيتك بكر ممتاز، معصور على البارد، وحموضته خارقة، إحنا نربطوك مباشرة بالطلب العالمي المستعد يدفع السوم العالي. نخرجوا بمنتجك مباشرة للمستورد الدولي بعقود قانونية مضمونة وتسليم من أرض معصرتك (EXW)، والربح يدخل كامل ليك بالعملة الصعبة.  \r\nيا فلاح تونس، أرضك عاطية الذهب، والعالم يلوج على صحته في زيتك.. اخرج من الحسبات الضيقة وفرض سومك في السوق العالمية!",
                    "en" => "Soon",
                    "fr" => "Bientot",
                ],
                "image" => "storage/articles/994f679d-a719-4010-9c97-933a7e1ce1c0.webp",
                "is_active" => true,
                "created_at" => "2026-05-27T23:22:27.000000Z",
                "updated_at" => "2026-05-27T23:22:27.000000Z",
            ],
            [
                "id" => 7,
                "title" => [
                    "ar" => "الاكتشاف الذي سيغير وجه الطب.. كيف يعيد زيت الزيتون برمجة الحمض النووي البشري؟",
                    "en" => "Article",
                    "fr" => "Article",
                ],
                "category" => [
                    "ar" => "دراسة",
                    "en" => "Article",
                    "fr" => "Article",
                ],
                "content" => [
                    "ar" => "انسَ كل ما تعلمته عن السعرات الحرارية، والدهون الصحية، ومضادات الأكسدة التقليدية. ماذا لو كان العلم طوال العقود الماضية ينظر إلى زيت الزيتون من الزاوية الخطأ تماماً؟ ماذا لو أنك عندما تبتلع قطرات من زيت الزيتون البكر الممتاز، فأنت لا تستهلك مجرد \"غذاء\"، بل تقوم فعلياً بـ \"تحميل تحديث بيولوجي\" مباشر لبرنامجك الجيني؟\r\nهذا ليس خيالاً علمياً، بل هو الاكتشاف الأعظم الذي يربط بين علم التخلق الجيني (Epigenetics) وأسرار الطبيعة، ليكشف لنا عن نظرية \"النقل اللاجيني لشيفرة الصمود\" (Epigenetic Survival Transfer). نظرية تنسف الموروث القديم، وتثبت أن زيت الزيتون هو في الحقيقة \"ناقل بيانات حيوي\" (Data-Carrying Biomolecule) وليس مجرد سائل دهني.\r\nإليك السر الذي ظل مخفياً داخل الخلايا:\r\n🌳 الشجرة لا تنتج زيتاً.. بل تكتب \"كوداً\" برمجياً\r\nانظر إلى شجرة الزيتون وهي تصارع الجفاف القاسي، والتربة الجافة، والشمس الحارقة. الشجرة لا تستسلم للذبول، بل تقوم بتفعيل \"جينات البقاء\" القصوى لديها. استجابةً لهذا الخطر، تفرز الشجرة مركبات كيميائية معقدة للغاية مثل \"الأولوكانثال\" (Oleocanthal) و\"الأولوروبين\" (Oleuropein).\r\nنحن كنا نعتقد أن هذه المركبات هي التي تمنح الزيت طعمه اللاذع وحرارته المعهودة، لكن الحقيقة المعملية العميقة أثبتت أن هذه الجزيئات هي \"شيفرة كيميائية\" مشفرة، تحمل داخلها ذاكرة الشجرة الكاملة في قهر الموت ومقاومة الإجهاد البيئي.\r\n🦠 أمعاؤك هي \"وحدة فك التشفير\" الأعظم\r\nحينما تتناول هذا الزيت الفائق، فإن معدتك لا تتعامل معه كمصدر للطاقة. هنا يتدخل جيش من تريليونات البكتيريا الصديقة في جهازك الهضمي (الميكروبيوم). تعمل هذه البكتيريا كـ \"قراصنة بيولوجيين\" (Bio-Hackers)، لتقوم بفك تشفير مركبات الفينول وتفتيتها إلى جزيئات نانوية بالغة الدقة (مثل الهيدروكسي تيروسول).\r\nهذه الجزيئات المستخلصة تمتلك قدرة خارقة على اختراق حاجز الدم في الدماغ (Blood-Brain Barrier)، والتسلل مباشرة إلى النواة الخلوية البشرية.\r\n⚙️ عملية إعادة التشغيل (The Human Reboot)\r\nهنا تتجلى الصدمة العلمية الكبرى: هذه الجزيئات المشفّرة لا تقوم بتغيير حمضك النووي (DNA)، بل تعمل كـ \"مفاتيح تشغيل لاجينية\" (Epigenetic Switches) تتفاعل مع ما يُعرف بالـ MicroRNAs. بمجرد التصاقها بالجينوم البشري، تقوم بتنفيذ أمرين في غاية الخطورة والتعقيد:\r\n إطفاء (Turn OFF) جينات التدمير: تقوم بإسكات الجينات المسؤولة عن إشعال الالتهابات المزمنة، وتوقف زحف الشيخوخة الخلوية وتلف الأنسجة.\r\n تشغيل (Turn ON) جينات الخلود الخلوي: تعيد إيقاظ الجينات النائمة المسؤولة عن الإصلاح الذاتي للـ DNA، وتحفز الخلايا على التهام سمومها (Autophagy)، وتبني درعاً فولاذياً يمنع الانقسامات الخلوية العشوائية المسببة للأورام.\r\n🥇 الخلاصة التي ستغير المراجع الطبية\r\nنحن أمام حقيقة بيولوجية مذهلة: أنت لا تأكل الغذاء، بل تندمج مع الطبيعة. شجرة الزيتون واجهت الموت فابتكرت ترياقاً للبقاء، وعندما تستهلك أنت زيتها الفاخر، فإنك تقوم فعلياً بنسخ ولصق \"قدرة الشجرة على الخلود\" داخل خلاياك البشرية الضعيفة.\r\nزيت الزيتون العالي الفينول ليس إضافة لوجبتك.. إنه \"تحديث جذري لبرنامج التشغيل الخلوي البشري\"، ودرعك اللاجيني ضد فناء الجسد.",
                    "en" => "Soon",
                    "fr" => "Bientot",
                ],
                "image" => "storage/articles/ced5f90e-df53-430b-b7b0-d3cdaf1091f7.webp",
                "is_active" => true,
                "created_at" => "2026-06-02T20:30:52.000000Z",
                "updated_at" => "2026-06-02T20:30:52.000000Z",
            ],
            [
                "id" => 10,
                "title" => [
                    "en" => "How to Import Olive Oil from Tunisia: A Complete B2B Sourcing Guide",
                    "fr" => "Guide Complet : Comment Importer de l'Huile d'Olive de Tunisie (Sourcing B2B)",
                    "ar" => "الدليل الشامل لاستيراد وتصدير زيت الزيتون التونسي: شروط وخطوات الشراء B2B",
                ],
                "category" => [
                    "en" => "Export & Trade",
                    "fr" => "Export & Commerce",
                    "ar" => "التصدير والتجارة",
                ],
                "image" => "images/articles/import_b2b_sourcing.jpg",
                "is_active" => true,
                "content" => [
                    "en" => "Tunisia stands as one of the world's leading producers and the foremost exporter of organic olive oil globally. For international importers, distributors, restaurant chains, and food manufacturers, sourcing Tunisian extra virgin olive oil (EVOO) represents an outstanding strategic advantage in terms of cost-efficiency, chemical purity, and high polyphenol content.\n\n### 1. Understanding Bulk vs Bottled Sourcing\nInternational buyers can choose between two main supply models on ZinToop:\n- **Bulk Shipments (Vrac)**: Loaded in 24,000-liter Flexitanks, 1,000-liter IBC containers, or 200L food-grade drums from the major commercial ports of Sfax, Rades, or Sousse.\n- **Bottled & Private Label**: Fully packaged in dark glass bottles (Marasca, Dorica) or metallic tins under your custom brand with international barcode compliance.\n\n### 2. Export Compliance and ONH Inspection\nEvery export batch from Tunisia undergoes strict chemical and organoleptic analysis conducted by accredited laboratories under the supervision of the National Oil Board (Office National de l'Huile - ONH). Required export documentation includes:\n- Official Certificate of Analysis (Acidity, Peroxide value, K232/K270 coefficients).\n- Certificate of Origin & EUR.1 Movement Certificate (for tariff preferences).\n- Phytosanitary and health certificates issued by the Ministry of Agriculture.\n\n### 3. How ZinToop Streamlines Direct B2B Purchasing\nThrough the ZinToop ecosystem, buyers can bypass unnecessary broker intermediaries, review verified producer profiles, access real-time regional mill prices, and request verified laboratory certificates directly from local producers.\n\nAccess our live marketplace: https://zintoop.com/en/bulk-tunisian-olive-oil",
                    "fr" => "La Tunisie figure parmi les leaders mondiaux de la production oléicole et se classe premier exportateur mondial d'huile d'olive biologique hors Union Européenne. Pour les importateurs, négociants et chaînes de distribution, l'huile d'olive extra vierge tunisienne offre une exceptionnelle stabilité et une richesse remarquable en antioxydants.\n\n### 1. Sourcing en Vrac ou Bouteilles Conditionnées\nLes acheteurs internationaux peuvent structurer leurs commandes selon deux formats principaux :\n- **Expéditions en Vrac (Bulk)** : Flexitanks de 24 000 litres, conteneurs IBC de 1 000 litres ou fûts métalliques au départ des ports de Sfax et Radès.\n- **Conditionnement sous Marque Privée** : Bouteilles en verre anti-UV (Marasca, Dorica) ou bidons 5L étiquetés selon les normes de votre marché de destination.\n\n### 2. Contrôles Qualité et Certification ONH\nChaque lot destiné à l'exportation est soumis à des analyses physico-chimiques et sensorielles strictes encadrées par l'Office National de l'Huile (ONH) et les laboratoires agréés.\n\nDécouvrez les offres en vrac disponibles : https://zintoop.com/fr/huile-olive-tunisienne-en-vrac",
                    "ar" => "تعتبر تونس من أبرز رواد إنتاج وتصدير زيت الزيتون في العالم، والمصدر الأول عالمياً لزيت الزيتون البيولوجي خارج الاتحاد الأوروبي. يوفر زيت الزيتون البكر الممتاز التونسي للمستوردين والتجار الدوليين ميزة تنافسية كبرى بفضل نسب البوليفينول العالية وجودة الطعم.\n\n### 1. خيارات الشراء: السائب (Bulk) أو المعبأ (Bottled)\n- **الشحن السائب**: في صهاريج Flexitanks سعة 24,000 لتر أو حاويات IBC سعة 1,000 لتر عبر موانئ صفاقس ورادس.\n- **التعبئة بالعلامة الخاصة**: عبوات زجاجية معتمة أو صفائح 5 لتر مطابقة لمعايير الأسواق المستهدفة.\n\n### 2. الفحص والمطابقة الجمركية\nتخضع جميع الشحنات لتحاليل مخبرية دقيقة تحت إشراف ديوان الزيت لضمان نسب الحموضة والمطابقة الدولية.\n\nتصفح عروض الجملة المباشرة: https://zintoop.com/ar/زيت-الزيتون-التونسي-بالجملة",
                ],
            ],
            [
                "id" => 11,
                "title" => [
                    "en" => "Private Label & Contract Bottling Olive Oil in Tunisia: Complete Guide",
                    "fr" => "Marque Privée et Conditionnement d'Huile d'Olive en Tunisie : Guide B2B",
                    "ar" => "العلامة الخاصة والتعبئة لزيت الزيتون التونسي: دليل الشركات والموزعين",
                ],
                "category" => [
                    "en" => "Private Label",
                    "fr" => "Marque Privée",
                    "ar" => "العلامة الخاصة",
                ],
                "image" => "images/articles/private_label_bottles.jpg",
                "is_active" => true,
                "content" => [
                    "en" => "Private labeling (OEM / White Label) allows international retailers, gourmet brands, and supermarket distributors to bottle authentic Tunisian Extra Virgin Olive Oil under their own brand identity.\n\n### Packaging Options\n- **Marasca & Dorica Dark Glass**: 250ml, 500ml, 750ml, 1L.\n- **Lithographed Tin Cans**: 1L, 3L, 5L with tamper-evident pourers.\n- **Bag-in-Box (BiB)**: Modern 3L and 5L oxygen-barrier packaging.\n\nLearn more about our certified packaging partners: https://zintoop.com/en/private-label-olive-oil-tunisia",
                    "fr" => "Le conditionnement sous marque privée (Private Label) permet aux distributeurs et marques internationales de commercialiser de l'huile d'olive extra vierge tunisienne d'excellence sous leur propre étiquette.",
                    "ar" => "تتيح خدمات التعبئة بالعلامة الخاصة (White Label / Private Label) للموزعين والشركات العالمية تعبئة أجود أنواع زيت الزيتون التونسي البكر الممتاز بعبوات مخصصة وهوية بصرية تحمل علامتهم التجارية.",
                ],
            ],
            [
                "id" => 12,
                "title" => [
                    "en" => "Tunisian Olive Oil Price Outlook 2026: Trends, Data & Market Factors",
                    "fr" => "Perspectives et Prix de l'Huile d'Olive en Tunisie 2026 : Analyse du Marché",
                    "ar" => "توقعات أسعار زيت الزيتون في تونس 2026: تحليل السوق والإنتاج والأسعار",
                ],
                "category" => [
                    "en" => "Market Analysis",
                    "fr" => "Analyse du Marché",
                    "ar" => "تحليل السوق",
                ],
                "image" => "images/articles/market_price_charts.jpg",
                "is_active" => true,
                "content" => [
                    "en" => "The 2025/2026 Tunisian olive oil campaign is characterized by strong export momentum, favorable climatic conditions in central regions, and steady international demand across European and North American markets.\n\nTrack daily live souk prices across all Tunisian governorates: https://zintoop.com/en/prices",
                    "fr" => "La campagne oléicole 2025/2026 en Tunisie s'annonce prometteuse avec une reprise des volumes et une forte attractivité sur les marchés d'exportation.",
                    "ar" => "تحليل شامل لاتجاهات أسعار زيت الزيتون في تونس للموسم الجديد، وعلاقة الأسعار المحلية ببورصة إسبانيا (Jaén) وإيطاليا والطلب العالمي على التصدير.",
                ],
            ],
            [
                "id" => 13,
                "title" => [
                    "en" => "Chemlali vs Chetoui: Sourcing the Right Tunisian Olive Oil Variety",
                    "fr" => "Chemlali vs Chétoui : Guide des Variétés d'Huile d'Olive Tunisienne",
                    "ar" => "مقارنة الشملالي والشتوي: دليلك لاختيار صنف زيت الزيتون التونسي المناسب",
                ],
                "category" => [
                    "en" => "Cultivars & Taste",
                    "fr" => "Variétés & Terroir",
                    "ar" => "الأصناف والجودة",
                ],
                "image" => "images/articles/chemlali_vs_chetoui.jpg",
                "is_active" => true,
                "content" => [
                    "en" => "Tunisia boasts two primary native olive varieties that account for over 90% of the national olive forest:\n\n- **Chemlali (الوسط والجنوب)**: Dominates Sfax, Sahel, and southern regions. Produces a golden, balanced, fruity oil with subtle almond notes, perfect for international blending.\n- **Chetoui (الشمال)**: Grown in northern valleys. Renowned for its intense green color, robust herbal aroma, and exceptionally high polyphenol count (antioxidants).\n\nExplore varieties guide: https://zintoop.com/en/olive-varieties",
                    "fr" => "Le Chemlali (Centre et Sud) et le Chétoui (Nord) constituent les deux piliers de l'oléiculture tunisienne, offrant des profils aromatiques uniques adaptés à tous les besoins culinaires et industriels.",
                    "ar" => "مقارنة دقيقة بين الشملالي (الأكثر انتشاراً بالوسط والجنوب ويمتاز بطعمه المتوازن المناسب للمزج) والشتوي (شمال تونس، غني جداً بمضادات الأكسدة والبوليفينول والحدة المحبوبة).",
                ],
            ],
            [
                "id" => 14,
                "title" => [
                    "en" => "FOB vs CIF Terms: Shipping Olive Oil from Tunisian Commercial Ports",
                    "fr" => "Incoterms FOB vs CIF : Logistique Maritime d'Export d'Huile d'Olive en Tunisie",
                    "ar" => "شحن زيت الزيتون من موانئ تونس: الفارق بين عقود FOB و CIF وشروط Flexitank",
                ],
                "category" => [
                    "en" => "Logistics & Shipping",
                    "fr" => "Logistique & Ports",
                    "ar" => "اللوجستيك والشحن",
                ],
                "image" => "images/articles/shipping_flexitank_ports.jpg",
                "is_active" => true,
                "content" => [
                    "en" => "When contracting olive oil shipments from Tunisian ports (Sfax, Rades, Bizerte, Sousse), international buyers must clearly establish Incoterms:\n\n- **FOB (Free on Board)**: Seller handles customs clearance, ONH certificate, and loads onto the vessel. Buyer manages sea freight.\n- **CIF (Cost, Insurance & Freight)**: Seller covers shipping and marine insurance to the destination port.\n\nConnect with logistics partners on ZinToop: https://zintoop.com/en/servicehub",
                    "fr" => "Comprendre les Incoterms FOB et CIF pour sécuriser vos importations maritimes d'huile d'olive tunisienne depuis les ports de Sfax et Radès.",
                    "ar" => "دليل المصدر والمستورد لفهم شروط الشحن البحري Incoterms والفرق بين تسليم الميناء التونسي (FOB) والتسليم مع التأمين والشحن حتى ميناء الوصول (CIF).",
                ],
            ],
            [
                "id" => 15,
                "title" => [
                    "en" => "Tunisian Olive Oil Export Regulations & ONH Guidelines 2026",
                    "fr" => "Guide Réglementaire : Procédures et Cahier des Charges Export ONH 2026",
                    "ar" => "كراس الشروط والإجراءات الرسمية لتصدير زيت الزيتون التونسي 2026",
                ],
                "category" => [
                    "en" => "Regulations & Legal",
                    "fr" => "Réglementation",
                    "ar" => "القوانين والإجراءات",
                ],
                "image" => "images/articles/onh_lab_analysis_inspection.jpg",
                "is_active" => true,
                "content" => [
                    "en" => "Exporting olive oil from Tunisia requires adherence to the national export regulatory framework established by the Ministry of Trade and the National Oil Board (ONH).\n\nConsult our legal and export advisory desk: https://zintoop.com/en/services/pricing",
                    "fr" => "L'exportation d'huile d'olive depuis la Tunisie est encadrée par un cahier des charges officiel garantissant la traçabilité et la qualité des lots exportés.",
                    "ar" => "دليل شامل للمصدرين وأصحاب المعاصر حول كراس شروط تصدير زيت الزيتون والتحاليل الإلزامية لدى ديوان الزيت والديوانة التونسية.",
                ],
            ],
            [
                "id" => 16,
                "title" => [
                    "en" => "Understanding Tabouiz: Olive Oil Extraction Yield Calculation in Tunisia",
                    "fr" => "Comprendre le Tabouiz : Calcul du Rendement d'Extraction d'Huile d'Olive en Tunisie",
                    "ar" => "دليل التبويز وحساب نسبة استخراج ومردودية زيت الزيتون في تونس",
                ],
                "category" => [
                    "en" => "Farming & Milling",
                    "fr" => "Production & Moulin",
                    "ar" => "الفلاحة والمعاصر",
                ],
                "image" => "images/articles/tabouiz_yield_calculation.jpg",
                "is_active" => true,
                "content" => [
                    "en" => "In Tunisia, olive oil extraction yield is traditionally measured in 'Tabouiz' (التبويز) — the amount of olive fruit (in kg or wiba) required to produce a standard measure of olive oil.\n\nCalculate your harvest extraction return and list your oil directly on ZinToop: https://zintoop.com/en/catalog",
                    "fr" => "Le 'Tabouiz' désigne en Tunisie le taux de rendement de l'extraction de l'huile d'olive à partir du poids d'olives triturées au moulin.",
                    "ar" => "التبويز هو المصطلح التونسي التقليدي لحساب نسبة استخراج الزيت من الزيتون المعصور، وهو المقياس الأساسي لتحديد مردودية الصابة وأرباح الفلاحين والمعاصر.",
                ],
            ],
            [
                "id" => 17,
                "title" => [
                    "en" => "The Ultimate B2B Sourcing Marketplace for Tunisian Olive Oil",
                    "fr" => "La Marketplace B2B de Référence pour l'Huile d'Olive Tunisienne",
                    "ar" => "سوق المعاملات المباشرة B2B لزيت الزيتون التونسي: الموردون والمطاعم والفنادق",
                ],
                "category" => [
                    "en" => "B2B Marketplace",
                    "fr" => "Marketplace B2B",
                    "ar" => "سوق المعاملات",
                ],
                "image" => "images/articles/b2b_marketplace_trading.jpg",
                "is_active" => true,
                "content" => [
                    "en" => "How ZinToop connects certified Tunisian olive oil mills, packaging units, and organic farmers directly with international buyers, HORECA chains, and food distributors worldwide.\n\nJoin our B2B trade network today: https://zintoop.com/en/catalog",
                    "fr" => "Découvrez comment ZinToop digitalise la filière oléicole tunisienne en connectant directement moulins, conditionneurs et acheteurs internationaux.",
                    "ar" => "كيف تساهم منصة ZinToop في ربط معاصر الزيتون ووحدات التعبئة والفلاحين مباشرة بكبرى الفنادق وسلاسل المطاعم والمشترين الدوليين دون وسطاء.",
                ],
            ],
        ];

        foreach ($articles as $data) {
            Article::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
