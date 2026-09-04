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
            $initialGreeting = $this->getInitialGreeting($userMessage, $postText);
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
    public function generatePublicCommentReply(string $userComment, ?string $postText = null): string
    {
        $lower = mb_strtolower($userComment);

        if (preg_match('/(مارك|علامة|innorpi)/u', $lower)) {
            return "عسلامة ومرحباً بيك في منصة زيت الزيتون التونسي 🫒! جاوبناك في رسالة خاصة على الماسنجر بخصوص تفاصيل تسجيل العلامة.";
        }
        if (preg_match('/(سوم|أسعار|اسعار|prix|price|بقداش|قداش)/u', $lower)) {
            return "على عيني وراسي ومرحباً بيك 🫒! بعثنالك التفاصيل في رسالة خاصة على الماسنجر.";
        }
        if (preg_match('/(نبيع|عندي|طن|محصول|صابة)/u', $lower)) {
            return "ربي يباركلك في الصابة 🫒! تواصلنا معاك في رسالة خاصة بالماسنجر.";
        }
        if (preg_match('/(نشري|شراء|تصدير)/u', $lower)) {
            return "مرحباً بيك 🫒! تواصلنا معاك في رسالة خاصة على الماسنجر بخصوص طلبك.";
        }

        return "عسلامة ومرحباً بيك في منصة زيت الزيتون التونسي 🫒! تفضل جاوبناك في رسالة خاصة على الماسنجر.";
    }

    /**
     * Initial short, friendly discovery greeting tailored with ONE single relevant question
     */
    public function getInitialGreeting(string $message, ?string $postText = null): string
    {
        $lowerMsg = mb_strtolower($message);
        $lowerPost = mb_strtolower($postText ?? '');

        // 1. If English
        if (preg_match('/^(hi|hello|hey|good morning|dear)/i', trim($message))) {
            return "Hello and welcome to ZinToop 🫒! How can I assist you today?";
        }

        // 2. If French
        if (preg_match('/^(bonjour|bonsoir|salut|coucou)/i', trim($message))) {
            return "Bonjour et bienvenue sur ZinToop 🫒 ! Comment puis-je vous aider aujourd'hui ?";
        }

        // 3. Scenario: Trademark / INNORPI / Private Label
        if (preg_match('/(مارك|علامة|innorpi|brevet|depose|dépôt|تعبئة|قوارير|تعليب|خاصة)/u', $lowerMsg) || 
            preg_match('/(مارك|علامة|innorpi|brevet|depose|dépôt|تعبئة|قوارير|تعليب|خاصة)/u', $lowerPost)) {
            return "عسلامة ومرحباً بيك في منصة زيت الزيتون التونسي 🫒! تواصلنا معاك بخصوص حماية وتسجيل علامتك الخاصة لزيت الزيتون.. تحب نفسرلك الوثائق المطلوبة ولا معاليم التسجيل لدى معهد المواصفات؟";
        }

        // 4. Scenario: Prices / Market
        if (preg_match('/(سوم|أسعار|اسعار|prix|price|بقداش|قداش|بورصة)/u', $lowerMsg) || 
            preg_match('/(أسعار|اسعار|سعر|سوق|بورصة)/u', $lowerPost)) {
            return "عسلامة ومرحباً بيك في منصة زيت الزيتون التونسي 🫒! تواصلنا معاك بخصوص الأسعار.. شنية الولاية أو السوق اللي تحب تعرف سومها اليوم؟";
        }

        // 5. Scenario: Buying / Sourcing / Export (checked before selling)
        if (preg_match('/(نشري|شراء|شاري|تصدير|شحن|فليكسي|توزيع|استيراد|export|achat|buy)/u', $lowerMsg) || 
            preg_match('/(تصدير|توزيع|شراء|استيراد)/u', $lowerPost)) {
            return "مرحباً بيك في منصة زيت الزيتون التونسي 🫒! قداش الكمية ونوعية الزيت (بكر ممتاز، معلب، أو صب) اللي تلوج عليها؟";
        }

        // 6. Scenario: Selling harvest / Farmer / Mill
        if (preg_match('/(نبيع|بيع|عندي|محصول|صابة|معصرة|فلاح|حب)/u', $lowerMsg) || 
            preg_match('/(بيع|عروض|فلاحين|صابة)/u', $lowerPost)) {
            return "ربي يباركلك في الصابة والمحصول 🫒! قداش تقريباً الكمية اللي عندك ومن أي ولاية؟";
        }

        // 7. Default friendly single open question
        return "عسلامة ومرحباً بيك في منصة زيت الزيتون التونسي 🫒! تفضل، كيفاش نجم نعاونك اليوم؟";
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
            'أنت "الزيتوني" (Ezzitouni) - الخبير والمستشار التقني والتجاري الأول لمنصة "زين توب" (ZinToop.com) وصفحتها الرسمية على فيسبوك «منصة زيت الزيتون التونسي».

[القواعد الإلزامية الصارمة للردود]:
1. الاختصار الشديد والتركيز (Brevity & Focus):
   - الرد يجب أن يكون قصيراً، ذكياً، ومباشراً (من 2 إلى 4 أسطر كحد أقصى).
   - ممنوع نهائياً كتابة مقالات أو تفريغ كل المعلومات في تعليق واحد.

2. الالتزام الحصري بسؤال العميل وسياق المنشور:
   - أجب فقط وفقط عما سأله العميل أو ما يتناوله المنشور المحدد.
   - إذا كان المنشور أو السؤال عن الأسعار -> أجب عن الأسعار باختصار مع رابط جدول الأسعار: https://zintoop.com/ar/prices
   - إذا كان المنشور أو السؤال عن بيع/شراء الزيت أو الصابة -> وجهه لعروض المنصة أو إضافة عرض: https://zintoop.com/ar/#products
   - إذا سأل العميل صراحة عن تسجيل علامة تجارية أو INNORPI -> أجب باختصار عن معاليم INNORPI والوثائق مع رابط المقال: https://zintoop.com/ar/articles/11
   - إذا سأل العميل صراحة عن كراس شروط التصدير -> أجب باختصار مع رابط كراس الشروط: https://zintoop.com/ar/articles/15
   - إذا كان التعليق عاماً أو تحية (سلام، مرحباً، يعيشك) -> رحب به بلباقة وسطرين فقط مع رابط المنصة: https://zintoop.com/ar
   - يمنع منعاً باتاً ذكر INNORPI أو التصدير إذا كان موضوع المنشور أو السؤال عن الأسعار أو موضوع آخر!

3. الأسلوب والنقاء اللغوي:
   - تحدث بالدارجة التونسية الحية، المهذبة، والمحايدة (عسلامة، ربي يباركلك، مرحباً بيك في منصة زيت الزيتون التونسي، يعيشك).
   - الترحيب لمرة واحدة فقط وبدون تكرار ممل.
   - اكتب الكلمات العربية بحروف عربية صافية (زين توب، فليكسي تانك)، وضع الرابط دائماً في سطر مستقل بمفرده.

[قاعدة المعارف المرجعية - تجيب منها فقط عند السؤال الصريح]:
- معاليم تسجيل العلامة لدى INNORPI (القانون 36 لسنة 2001): 596 د.ت لصنف واحد، 119 د.ت لكل صنف إضافي، 96.200 د.ت شهادة التسجيل، و36.700 د.ت فحص الأسبقية. الرابط: https://zintoop.com/ar/articles/11
- كراس شروط التصدير (الرائد الرسمي 147): التسجيل بقائمة المصدرين بوزارة الفلاحة، RNE، معرف ديواني وجبائي، ومخازن معزولة ومخبر معتمد. الرابط: https://zintoop.com/ar/articles/15
- أسعار أسواق الزيتون: يتم تحيينها دورياً أسبوعياً. قل دائماً: «حسب آخر تحيين متوفر في المنصة» ووجهه للرابط: https://zintoop.com/ar/prices'
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
