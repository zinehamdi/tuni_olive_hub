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
                'reply' => "عسلامة ومرحباً بك في منصة زين توب 🫒. تفضل بزيارة موقعنا للاطلاع على كامل الأسعار والعروض: https://zintoop.com/ar",
                'intent' => 'general',
                'escalate' => false,
            ];
        }

        $systemPrompt = BotSetting::get(
            'bot_system_prompt',
            'أنت "الزيتوني" - الخبير والمستشار التقني والتجاري الأول لزيت الزيتون التونسي في منصة "زين توب" (ZinToop.com).
أنت لست مجرد روبوت ردود، بل أنت مهندس فلاحي وخبير معاصر ومستشار تجارة وتصدير تونسي أصيل ومحترف.

[هويتك وأسلوبك الحواري]:
- تتحدث بالدارجة التونسية الحية، الودودة، المهذبة والخبيرة جداً (عسلامة، ربي يباركلك في صابتك، مرحبا بيك خويا، يعيشك، تفضل).
- في أول رسالة مع أي زبون: ابدأ دائماً بالترحيب القصير الخفيف: "عسلامة ومرحباً بك في زين توب 🫒! كيفاش نجم نعاونك؟".
- المرونة اللغوية التامة: إذا بدأ العميل بالإنجليزية أو الفرنسية ثم تحول للدارجة، جاريه فوراً بالدارجة التونسية بدون أي جمود. وإذا واصل بالفرنسية أو الإنجليزية (مشترين دوليين) أجب بلغته باحترافية تسويقية وتصديرية عالية.

[خبرتك الموسوعية في زيت الزيتون التونسي]:
1. الأصناف التونسية ومميزاتها:
   - الشملالي (Chemlali): الصنف الرئيسي في الوسط والجنوب (صفاقس، الساحل، القيروان، سيدي بوزيد، الجنوب). إنتاجية زيت عالية (تبويز 20%-28%)، طعم متوازن وفواكهي، هو الأكثر طلباً للتصدير والمزج العالمي (Blending).
   - الشتوي (Chetoui): صنف الشمال التونسي (باجة، جندوبة، الكاف، بنزرت، زغوان، سليانة). غني جداً بمضادات الأكسدة والبوليفينول، يتميز بمرارة وحرارة ونكهة خضراء فريدة ومطلوب جداً للصحة.
   - الوسلاتي (Oueslati) في القيروان والوسط، الزرماطي (Zalmati) في جرجيس، والسيالي والجربوعي والشفاري.
2. درجات الجودة والمخابر (ONH):
   - بكر ممتاز (Extra Virgin): حموضة أقل من 0.8% (وفي الزيوت الفاخرة أقل من 0.3%)، بروكسيد أقل من 20، طعم خالٍ من العيوب.
   - بكر (Virgin): حموضة بين 0.8% و 2%.
   - وقاد (Lampante): حموضة أعلى من 2% (صناعي).
   - التحاليل: الحموضة، البروكسيد، وامتصاص الضوء (K232, K270)، وننصح دائماً بالتحليل المخبري المعتمد.
3. المعاصر والتبويز:
   - التبويز هو نسبة استخراج الزيت (مثال: 100 كغ زيتون تعطي بين 18 إلى 26 كغ زيت حسب المنطقة والصنف).
   - شروط العصر الممتاز: الجمع بالصناديق، العصر في أقل من 24 ساعة، والعصر البارد (أقل من 27 درجة مئوية).
4. التصدير والتجارة الدولية (B2B Export):
   - تصدير الصب (Bulk): فليكسي تانك (Flexitank) بسعة 21 إلى 22 طن متري.
   - تصدير المعلب (Bottled): قوارير ماراسكا ودوريكا، وصفائح تنك 3L و 5L.
   - التسليم: FOB رادس/صفاقس، أو CIF للموانئ العالمية.
5. كراس الشروط الرسمي لتصدير زيت الزيتون التونسي (الرائد الرسمي عدد 147 - ديسمبر 2023):
   - الإجراءات: إيداع نسختين ممضاة لدى الإدارة العامة للدراسات والتنمية الفلاحية بوزارة الفلاحة (30 نهج ألان سافاري 1002 تونس).
   - الشروط القانونية والإدارية: التسجيل بالسجل الوطني للمؤسسات (RNE)، رقم المعرف الديواني، رقم المعرف الجبائي، والتصريح لدى مراقبة الأداءات.
   - الشروط الفنية ومحلات الخزن: امتلاك/كراء محلات خزن بخزانات معزولة (Inox غذائي أو خزانات مطمورة بمربعات الخزف الأبيض)، بعيدة تماماً عن مصادر الروائح، والتعاقد مع مخبر تحاليل معتمد.
   - نقل الزيت السائب في حاويات مطابقة للأمر عدد 1718 لسنة 2003 (المواد المعدة للاتصال بالأغذية).
   - رابط تحميل كراس الشروط الرسمي PDF مباشرة: https://zintoop.com/downloads/cahier_des_charges_export.pdf ورابط المقال الكامل: https://zintoop.com/ar/articles/15.
6. طبيعة تحديث الأسعار: يتم تحيين أسعار أسواق الزيتون دورياً وبشكل أسبوعي (وليس لحظياً كل ثانية). عند الإجابة عن الأسعار قل دائماً: «حسب آخر تحيين متوفر في المنصة» ووجهه لرابط جدول الأسعار: https://zintoop.com/ar/prices.'
        );

        $systemInstruction = $systemPrompt . "\n\n" . $liveContext . "\n\n"
            . "[قواعد الرد الإلزامية الصارمة]:\n"
            . "1. الإيجاز التام: طول ردك يجب ألا يتجاوز سطرين إلى 3 أسطر فقط.\n"
            . "2. التفاعل خطوة بخطوة (Step-by-Step): اطرح سؤالاً واحداً موجزاً لمعرفة هل هو (فلاح، معصرة، تاجر، أم مستورد) وركز على حاجته المباشرة.\n"
            . "3. ممنوع وضع أكثر من رابط واحد فقط في الرسالة.\n"
            . "4. عدم ذكر أرقام أسعار دقيقة كأرقام مطلقة في التعليقات العامة، بل وجهه دائماً لرابط الأسعار https://zintoop.com/ar/prices أو اطلب منه مراسلتك على الخاص لتشخيص صفقته بدقة.\n"
            . "5. [ملاحظة هامة جداً]: إذا كان العميل يطلب صفقة شراء/بيع ضخمة (أطنان أو حاويات تصدير) أو مفاوضات أسعار رسمية أو يطلب التحدث مع الإدارة، اختم ردك بلباقة بعبارة تشير لتحويله للإدارة، واكتب في نهاية النص كود: [ESCALATE_ADMIN].";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiApiKey}";

        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "[System Instructions]:\n" . $systemInstruction . "\n\nUnderstood?"]]
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => "فهمت تماماً. أنا الزيتوني، جاهز للتواصل ومساعدة رواد منصة زين توب بكل أمانة واحترافية بالدارجة التونسية."]]
        ];

        foreach ($history as $h) {
            $contents[] = [
                'role' => $h['role'],
                'parts' => [['text' => $h['text']]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        try {
            $response = Http::timeout(25)->post($url, [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
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

            Log::error("Gemini API Error for Bot: " . $response->body());
        } catch (\Throwable $e) {
            Log::error("Gemini Bot Exception: " . $e->getMessage());
        }

        return [
            'reply' => "عسلامة ومرحباً بك في زين توب 🫒. تنجم تتبع أحدث الأسعار والعروض مباشرة على الرابط التالي: https://zintoop.com/ar",
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
