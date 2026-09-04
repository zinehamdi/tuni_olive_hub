<?php

namespace App\Services\Bot;

use App\Models\BotConversation;
use App\Models\BotCustomRule;
use App\Models\BotMessageLog;
use App\Models\BotSetting;
use App\Models\FacebookPostDirective;
use App\Models\SoukPrice;
use App\Models\WorldOlivePrice;
use App\Models\Listing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EzzitouniSocialEngine
{
    protected string $geminiApiKey;
    protected string $geminiModel;

    public function __construct()
    {
        $this->geminiApiKey = (string) config('services.gemini.key', env('GEMINI_API_KEY', ''));
        $this->geminiModel = (string) config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash'));
    }

    /**
     * Process an incoming message or comment and generate a contextual, human-like response
     */
    public function generateResponse(
        string $userMessage,
        string $channel = 'whatsapp',
        string $externalId = '',
        ?string $userName = null,
        ?string $postId = null,
        ?string $postText = null
    ): array {
        // 1. Check if global bot is enabled
        $botEnabled = BotSetting::get('bot_enabled', '1');
        if ($botEnabled === '0' || $botEnabled === 'false') {
            return [
                'reply' => null,
                'intent' => 'disabled',
                'escalate' => false,
            ];
        }

        // 2. Find or create conversation
        $conversation = null;
        if (!empty($externalId)) {
            $conversation = BotConversation::firstOrCreate(
                ['channel' => $channel, 'external_id' => $externalId],
                ['user_name' => $userName, 'status' => 'automated']
            );

            // If under human takeover, bot stays silent
            if ($conversation->status === 'human_takeover') {
                Log::info("Bot silenced: Conversation {$conversation->id} is in human_takeover mode.");
                return [
                    'reply' => null,
                    'intent' => 'human_in_progress',
                    'escalate' => false,
                ];
            }
        }

        // 3. Fast Action Keyword Rules
        $ruleReply = $this->checkCustomRules($userMessage);
        if ($ruleReply !== null) {
            $this->logMessage($conversation, $userMessage, $ruleReply, $channel);
            return [
                'reply' => $ruleReply,
                'intent' => 'rule_match',
                'escalate' => false,
            ];
        }

        // 4. First-time Greeting Micro-Discovery logic for 1-on-1 chats (WhatsApp / Messenger DM)
        if ($channel !== 'facebook_comment' && $conversation && $conversation->messages()->count() === 0) {
            $initialGreeting = $this->getInitialGreeting($userMessage);
            $this->logMessage($conversation, $userMessage, $initialGreeting, $channel);
            return [
                'reply' => $initialGreeting,
                'intent' => 'initial_greeting',
                'escalate' => false,
            ];
        }

        // 5. Gather Live Context (Prices, Certified Mills, Platform Stats)
        $liveContext = $this->buildLiveContext($postId, $postText);

        // 6. Build Conversation History
        $history = [];
        if ($conversation) {
            $recentMessages = $conversation->messages()->latest('created_at')->take(6)->get()->reverse();
            foreach ($recentMessages as $msg) {
                $history[] = [
                    'role' => $msg->sender === 'user' ? 'user' : 'model',
                    'text' => $msg->message_text,
                ];
            }
        }

        // 7. Call Gemini AI Brain
        $geminiResult = $this->callGemini($userMessage, $liveContext, $history);
        $replyText = $geminiResult['reply'];
        $intent = $geminiResult['intent'];
        $shouldEscalate = $geminiResult['escalate'];

        // 8. Handle Human Takeover Escalation
        if ($shouldEscalate && $conversation) {
            $conversation->update([
                'status' => 'human_takeover',
                'intent' => $intent,
            ]);

            // Notify Admin WhatsApp
            $waService = app(WhatsAppCloudApiService::class);
            $waService->notifyAdmin(
                $userName ?? 'عميل مهتم',
                $conversation->phone_number ?? $externalId,
                $userMessage
            );
        }

        // 9. Log and Save
        $this->logMessage($conversation, $userMessage, $replyText, $channel);

        return [
            'reply' => $replyText,
            'intent' => $intent,
            'escalate' => $shouldEscalate,
        ];
    }

    /**
     * Generate dynamic, highly-contextual 1-line public comment reply
     */
    public function generatePublicCommentReply(string $userComment): string
    {
        $lower = mb_strtolower($userComment);

        // 1. If mentioning specific regions (North / South / Center)
        if (preg_match('/(شمال|باجة|جندوبة|الكاف|بنزرت|سليانة|زغوان)/u', $lower)) {
            return "عسلامة ومرحباً بيك وبأهل الشمال الغاليين 🫒! تواصلنا معاك في رسالة خاصة بالماسنجر بخصوص تفاصيل طلبك.";
        }
        if (preg_match('/(جنوب|صفاقس|سيدي بوزيد|القيروان|الساحل|سوسة|المنستير|المهدية|قابس|مدنين|تطاوين|قفصة|جرجيس|الجم)/u', $lower)) {
            return "عسلامة ومرحباً بيك وبأهلنا الكرام 🫒! بعثنالك التفاصيل الدقيقة في رسالة خاصة على الماسنجر.";
        }

        // 2. If selling olive or oil (Farmer / Mill harvest)
        if (preg_match('/(نبيع|عندي|طن|ترانط|محصول|صابة|معصرة)/u', $lower)) {
            return "ربي يباركلك في الصابة والمحصول 🫒! تواصلنا معاك في الخاص باش نعاونوك تحط عرضك في سوق زين توب.";
        }

        // 3. If buying / export / Europe diaspora
        if (preg_match('/(نشري|شراء|تصدير|ألمانيا|فرنسا|إيطاليا|اوروبا|أوروبا|سويسرا|eu|germany|france)/ui', $lower)) {
            return "مرحباً بيك وبأولاد بلادنا في كل مكان 🫒! تواصلنا معاك في رسالة خاصة على الماسنجر بخصوص الأسعار وطريقة التوصيل.";
        }

        // 4. If asking about prices
        if (preg_match('/(سوم|أسعار|اسعار|prix|price|بقداش|قداش)/u', $lower)) {
            return "على عيني وراسي ومرحباً بيك 🫒! بعثنالك آخر تحيين للأسعار وروابط المتابعة في رسالة خاصة على الماسنجر.";
        }

        // 5. Default natural contextual greeting
        return "عسلامة ومرحباً بك في زين توب 🫒! جاوبناك في رسالة خاصة على الماسنجر باش نسهلولك طلبك بالتفصيل.";
    }

    /**
     * Initial short, friendly discovery greeting
     */
    protected function getInitialGreeting(string $message): string
    {
        $trimmed = trim(strtolower($message));

        // If English
        if (preg_match('/^(hi|hello|hey|good morning|good evening|dear)/i', $trimmed)) {
            return "Hello and welcome to ZinToop 🫒! How can I help you today?";
        }

        // If French
        if (preg_match('/^(bonjour|bonsoir|salut|coucou)/i', $trimmed)) {
            return "Bonjour et bienvenue sur ZinToop 🫒 ! Comment puis-je vous aider aujourd'hui ?";
        }

        // Default Tunisian Darja
        return "عسلامة ومرحباً بك في زين توب 🫒! كيفاش نجم نعاونك؟";
    }

    /**
     * Check custom keyword rules from database
     */
    protected function checkCustomRules(string $message): ?string
    {
        $rules = BotCustomRule::where('is_active', true)->get();

        foreach ($rules as $rule) {
            $keyword = trim($rule->keyword);
            if (empty($keyword)) continue;

            $matched = false;
            if ($rule->match_type === 'exact') {
                $matched = (mb_strtolower(trim($message)) === mb_strtolower($keyword));
            } else {
                $matched = (mb_stripos($message, $keyword) !== false);
            }

            if ($matched) {
                if ($rule->action_type === 'reply_text' || $rule->action_type === 'send_link') {
                    return $rule->action_payload;
                }
            }
        }

        return null;
    }

    /**
     * Build live database context: today's prices, active listings, and post directives
     */
    protected function buildLiveContext(?string $postId = null, ?string $postText = null): string
    {
        $context = "";

        // Latest Tunisian Souk Prices (Periodic/Weekly update)
        try {
            $latestPrices = SoukPrice::latest('recorded_at')->take(4)->get();
            if ($latestPrices->isNotEmpty()) {
                $context .= "\n[آخر تحيين متاح لأسعار أسواق الزيتون بتونس (تحيين دوري أسبوعي)]: (ملاحظة: الأسعار يتم تحيينها أسبوعياً، قل دائماً للعميل: 'حسب آخر تحيين متوفر في المنصة')\n";
                foreach ($latestPrices as $p) {
                    $dateStr = $p->recorded_at ? $p->recorded_at->format('Y-m-d') : '';
                    $context .= "- {$p->market_name} ({$p->region}): {$p->price_min} - {$p->price_max} د.ت/كغ ({$p->variety})" . ($dateStr ? " (تحيين: {$dateStr})" : "") . "\n";
                }
            }
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // Global export reference prices
        try {
            $worldPrice = WorldOlivePrice::latest('recorded_at')->first();
            if ($worldPrice) {
                $context .= "\n[السعر المرجعي العالمي للزيت البكر الممتاز]: {$worldPrice->price_extra_virgin} يورو/طن ({$worldPrice->country})\n";
            }
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // Platform Links (Real ZinToop Services)
        $context .= "\n[روابط وخدمات منصة زين توب الرسمية الحقيقية المعتمدة في الموقع]:\n"
            . "- رابط دليل تسجيل وحماية العلامة التجارية لدى معهد المواصفات والملكية الصناعية (INNORPI): https://zintoop.com/ar/articles/11\n"
            . "- رابط خدمات العلامة الخاصة والتعليب: https://zintoop.com/ar/علامة-خاصة-زيت-زيتون-تونس\n"
            . "- رابط جدول أسعار أسواق الزيتون التونسية: https://zintoop.com/ar/prices (أو https://zintoop.com/ar/souks)\n"
            . "- رابط سوق وعروض الزيت الحالية (المنتجات والعروض): https://zintoop.com/ar/#products (أو https://zintoop.com/ar)\n"
            . "- رابط خدماتنا وباقات التسويق والحلول الرقمية: https://zintoop.com/ar/services/pricing\n"
            . "- رابط مجمع الخدمات (دليل المعاصر، النقل، والخدمات الفلاحية): https://zintoop.com/ar/servicehub\n"
            . "- رابط تسجيل حساب جديد عام: https://zintoop.com/ar/register\n"
            . "- رابط تسجيل فلاح: https://zintoop.com/ar/register/role?role=farmer\n"
            . "- رابط تسجيل معصرة: https://zintoop.com/ar/register/role?role=mill\n"
            . "- رابط تسجيل ناقل: https://zintoop.com/ar/register/role?role=carrier\n"
            . "- رابط إضافة عرض بيع جديد: https://zintoop.com/ar/listings/create\n"
            . "- رابط كيف تعمل المنصة: https://zintoop.com/ar/how-it-works\n"
            . "- رابط دليل كراس الشروط وإجراءات التصدير 2026: https://zintoop.com/ar/articles/15\n"
            . "- رابط دليل التبويز وحساب نسبة الاستخراج: https://zintoop.com/ar/articles/16\n"
            . "- رابط دليل الزيت البيولوجي (العضوي) والمعايير: https://zintoop.com/ar/articles/5\n"
            . "- رابط مقارنة أصناف الزيتون التونسي (الشملالي والشتوي): https://zintoop.com/ar/olive-varieties (أو https://zintoop.com/ar/articles/13)\n"
            . "- رابط عقود الشحن والتصدير (FOB و CIF وفليكسي تانك): https://zintoop.com/ar/articles/14\n"
            . "- رابط الصفحة الرئيسية: https://zintoop.com/ar\n";

        // If this is a specific Facebook Post with a marketing directive
        if ($postId) {
            $directive = FacebookPostDirective::where(function($q) use ($postId) {
                $q->where('post_id', $postId)
                  ->orWhere('post_url', 'like', "%{$postId}%");
            })->where('is_active', true)->first();

            if ($directive) {
                $context .= "\n[توجيه المدير التسويقي الخاص بهذا المنشور]:\n"
                    . "الهدف من المنشور: {$directive->hook_goal}\n"
                    . ($directive->custom_prompt ? "تعليمات إضافية للبوت: {$directive->custom_prompt}\n" : "")
                    . ($directive->target_action_link ? "الرابط المطلوب تضمينه بالرد: {$directive->target_action_link}\n" : "");
            }
        }

        if ($postText) {
            $context .= "\n[نص المنشور المنشور على الصفحة]:\n{$postText}\n";
        }

        return $context;
    }

    /**
     * Call Google Gemini API (Universal Flash Model)
     */
    protected function callGemini(string $userMessage, string $liveContext, array $history = []): array
    {
        if (empty($this->geminiApiKey)) {
            return [
                'reply' => "عسلامة ومرحباً بك في منصة زيت الزيتون التونسي 🫒. تفضل بزيارة موقعنا للاطلاع على كامل الأسعار والعروض: https://zintoop.com/ar",
                'intent' => 'general',
                'escalate' => false,
            ];
        }

        $systemPrompt = BotSetting::get(
            'bot_system_prompt',
            'أنت "الزيتوني" - الخبير والمستشار التقني والتجاري الأول لمنصة "زين توب" (ZinToop.com) وصفحتها الرسمية على فيسبوك «منصة زيت الزيتون التونسي».
أنت لست مجرد روبوت ردود، بل أنت مهندس فلاحي وخبير معاصر ومستشار تجارة وتصدير تونسي أصيل ومحترف.

[هويتك وأسلوبك الحواري]:
- تتحدث بالدارجة التونسية الحية، الودودة، المهذبة والمحايدة تماماً (عسلامة، ربي يباركلك، مرحباً بيك في منصة زيت الزيتون التونسي، يعيشك، تفضل، على عيني وراسي).
- الترحيب لمرة واحدة فقط: يظهر الترحيب مرة واحدة في أول تواصل للعميل الجديد فقط. وفي جميع الرسائل التالية (الرسالة 2، 3، 4...) ممنوع نهائياً تكرار الترحيب، بل ادخل في صلب الجواب مباشرة وبشكل سلس.
- المرونة اللغوية التامة: إذا بدأ العميل بالإنجليزية أو الفرنسية ثم تحول للدارجة، جاريه فوراً بالدارجة التونسية بدون أي جمود. وإذا واصل بالفرنسية أو الإنجليزية (مشترين دوليين) أجب بلغته باحترافية تسويقية وتصديرية عالية.

[دليل تسجيل وحماية العلامة التجارية لدى معهد المواصفات والملكية الصناعية INNORPI]:
- القانون المنظم: القانون عدد 36 لسنة 2001 المؤرخ في 17 أفريل 2001.
- القاعدة الأساسية: الملكية تكتسب حصرياً بالإيداع والتسجيل لدى INNORPI وليس بمجرد الاستعمال التجاري (La propriété s\'acquiert par le dépôt, non par l\'usage).
- مدة الحماية: 10 سنوات قابلة للتجديد بصفة غير محدودة.
- الوثائق المطلوبة: 3 نظائر من شعار/لوغو العلامة (أبعاد أقصاها 10×6 صم)، وصل خلاص المعاليم بالقباضة، قائمة المنتجات حسب تصنيف نيس، ونسخة RNE للمسير أو توكيل.
- جدول المعاليم الرسمية المحينة (2026 TTC بالدينار التونسي):
  * البحث المسبق عن الأسبقية (Recherche d\'antériorité): 36,700 د.ت.
  * إيداع ملف علامة لصنف واحد: 596,000 د.ت (التجديد: 774,500 د.ت).
  * تسجيل كل صنف إضافي عند الإيداع: 119,000 د.ت (عند التجديد: 178,500 د.ت).
  * تسليم شهادة تسجيل العلامة عند الجاهزية: 96,200 د.ت.
- أهم أصناف نيس لزيت الزيتون: الصنف 29 (الزيوت والمصبرات)، الصنف 31 (الزيتون الطازج)، الصنف 35 (التجارة والتسويق)، الصنف 39 (النقل والتعبئة)، الصنف 40 (خدمات العصر بالمعاصر).
- مقرات INNORPI: تونس (حي الخضراء 71806758)، صفاقس (نهج بجاية 74298223)، سوسة (نهج المنجي بالي 73226566).
- رابط المقال الكامل والدليل المفصل: https://zintoop.com/ar/articles/11

[خبرتك الموسوعية في زيت الزيتون التونسي]:
1. الأصناف التونسية ومميزاتها:
   - الشملالي (Chemlali): الصنف الرئيسي في الوسط والجنوب (صفاقس، الساحل، القيروان، سيدي بوزيد، الجنوب). إنتاجية زيت عالية (تبويز 20%-28%)، طعم متوازن وفواكهي، هو الأكثر طلباً للتصدير والمزج العالمي (Blending).
   - الشتوي (Chetoui): صنف الشمال التونسي (باجة، جندوبة، الكاف، بنزرت، زغوان، سليانة). غني جداً بمضادات الأكسدة والبوليفينول، يتميز بمرارة وحرارة ونكهة خضراء فريدة ومطلوب جداً للصحة.
2. كراس الشروط الرسمي لتصدير زيت الزيتون التونسي (الرائد الرسمي عدد 147 - ديسمبر 2023):
   - الإجراءات: إيداع نسختين ممضاة لدى الإدارة العامة للدراسات والتنمية الفلاحية بوزارة الفلاحة (30 نهج ألان سافاري 1002 تونس).
   - الشروط: RNE، معرف ديواني وجبائي، محلات خزن معزولة (Inox غذائي أو خزف أبيض)، ومخبر تحاليل معتمد.
   - رابط تحميل كراس الشروط PDF مباشرة: https://zintoop.com/downloads/cahier_des_charges_export.pdf ورابط المقال: https://zintoop.com/ar/articles/15.
3. طبيعة تحديث الأسعار: يتم تحيين أسعار أسواق الزيتون دورياً وبشكل أسبوعي. عند الإجابة عن الأسعار قل دائماً: «حسب آخر تحيين متوفر في المنصة» ووجهه لرابط جدول الأسعار: https://zintoop.com/ar/prices.'
        );

        $systemInstruction = $systemPrompt . "\n\n" . $liveContext . "\n\n"
            . "[قواعد الرد الإلزامية]:\n"
            . "1. متابعة سياق المحادثة بدقة: افهم ما طلبه العميل في الرسائل السابقة وابنِ إجابتك عليه مباشرة وبذكاء.\n"
            . "2. منع تكرار الترحيب: يمنع منعاً باتاً تكرار الترحيب أو قول 'عسلامة ومرحباً بك' في الرسائل المتتالية إذا كان الحوار مستمراً مع العميل. ادخل في صلب الجواب والشرح مباشرة.\n"
            . "3. النقاء اللغوي التام وعدم خلط الحروف: التزم باللغة الواحدة في الرد كاملاً. إذا كانت الإجابة بالعربية، اكتب كل الكلمات بحروف عربية صافية (اكتب 'زين توب' وليس 'ZinToop'، واكتب 'فليكسي تانك' وليس 'Flexitank'). يمنع تماماً خلط كلمات إنجليزية أو فرنسية وسط الجمل العربية لتفادي تشوه النص. الاستثناء الوحيد هو الرابط نفسه (URL) الذي يوضع في سطر مستقل بمفرده.\n"
            . "4. اللباقة والحياد التام: استخدم عبارات تونسية راقية ومحايدة تناسب النساء والرجال معاً (يعيشك، ربي يباركلك، على عيني وراسي، تفضل).\n"
            . "5. أسلوب الشرح: اشرح للعميل بوضوح ولباقة بالدارجة التونسية حسب موضوعه، دون إطالة مفرطة ودون قطع غير طبيعي.\n"
            . "6. رابط واحد محدد وكامل: عند الحاجة لتوجيه العميل، ضع رابطاً واحداً فقط يخدم طلبه، واكتب الرابط كاملاً في سطر مستقل:\n"
            . "   - سوق العروض والمنتجات: https://zintoop.com/ar/#products\n"
            . "   - جدول أسعار الأسواق: https://zintoop.com/ar/prices\n"
            . "   - تسجيل حساب جديد: https://zintoop.com/ar/register\n"
            . "   - إضافة عرض بيع جديد: https://zintoop.com/ar/listings/create\n"
            . "   - كراس شروط التصدير: https://zintoop.com/ar/articles/15\n"
            . "7. في صفقات الجملة والحاويات الكبرى: اطلب رقم الواتساب واختم بـ [ESCALATE_ADMIN].";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiApiKey}";

        $contents = [];
        $lastRole = null;

        foreach ($history as $h) {
            $role = ($h['role'] === 'user') ? 'user' : 'model';
            if ($role === $lastRole) {
                continue;
            }
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $h['text']]],
            ];
            $lastRole = $role;
        }

        if ($lastRole === 'user' && !empty($contents)) {
            $contents[count($contents) - 1]['parts'][0]['text'] .= "\n" . $userMessage;
        } else {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]],
            ];
        }

        $modelsToTry = array_unique([$this->geminiModel, 'gemini-1.5-flash', 'gemini-2.0-flash']);

        foreach ($modelsToTry as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->geminiApiKey}";

                $response = Http::timeout(25)->retry(2, 600)->post($url, [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 1000,
                    ],
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                    $shouldEscalate = false;
                    if (str_contains($reply, '[ESCALATE_ADMIN]')) {
                        $shouldEscalate = true;
                        $reply = str_replace('[ESCALATE_ADMIN]', '', $reply);
                    }

                    $reply = trim($reply);

                    return [
                        'reply' => $reply,
                        'intent' => $this->detectIntent($userMessage),
                        'escalate' => $shouldEscalate,
                    ];
                }

                Log::warning("Gemini model {$model} returned status {$response->status()}, trying fallback...");
            } catch (\Throwable $e) {
                Log::warning("Gemini exception on {$model}: " . $e->getMessage());
            }
        }

        return [
            'reply' => "عسلامة ومرحباً بك في زين توب 🫒. تنجم تتبع أحدث الأسعار والعروض مباشرة على الرابط التالي:\nhttps://zintoop.com/ar",
            'intent' => 'fallback',
            'escalate' => false,
        ];
    }

    /**
     * Helper to detect rough user intent
     */
    protected function detectIntent(string $message): string
    {
        $m = mb_strtolower($message);
        if (str_contains($m, 'سوم') || str_contains($m, 'أسعار') || str_contains($m, 'سعر') || str_contains($m, 'prix')) return 'prices';
        if (str_contains($m, 'نشري') || str_contains($m, 'شراء') || str_contains($m, 'acheter')) return 'buy';
        if (str_contains($m, 'نبيع') || str_contains($m, 'بيع') || str_contains($m, 'vendre')) return 'sell';
        if (str_contains($m, 'معصرة') || str_contains($m, 'طحن') || str_contains($m, 'عصر')) return 'mill';
        if (str_contains($m, 'تصدير') || str_contains($m, 'export') || str_contains($m, 'flexitank')) return 'export';
        if (str_contains($m, 'تحليل') || str_contains($m, 'مخبر') || str_contains($m, 'onh')) return 'lab';
        return 'general';
    }

    /**
     * Helper to log message to DB
     */
    protected function logMessage(?BotConversation $conversation, string $userMessage, ?string $botReply, string $channel): void
    {
        if (!$conversation || empty($botReply)) return;

        $conversation->update([
            'last_user_message' => $userMessage,
            'last_bot_reply' => $botReply,
        ]);

        BotMessageLog::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message_text' => $userMessage,
            'channel' => $channel,
        ]);

        BotMessageLog::create([
            'conversation_id' => $conversation->id,
            'sender' => 'bot',
            'message_text' => $botReply,
            'channel' => $channel,
        ]);
    }
}
