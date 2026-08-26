<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chat messages for Ezzitouni AI using Google Gemini API.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'array', // previous chat messages
        ]);

        $apiKey = config('services.gemini.key');
        
        if (empty($apiKey)) {
            return response()->json([
                'reply' => $this->handleFallbackIntent($request->input('message'))
            ]);
        }

        // Get the system instructions
        $systemInstruction = config('ezzitouni.system_prompt', 'أنت مساعد ذكي لمنصة ZinToop.');

        // Prepare conversation history
        $history = $request->input('history', []);
        
        // Prepend System Instructions as a conversation setup for universal model compatibility
        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "[System Instructions]:\n" . $systemInstruction . "\n\nEnd of instructions. Acknowledge and wait for user."]]
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => "Understood. I am Ezzitouni, ready to assist."]]
        ];

        $rawMessages = [];
        
        foreach ($history as $index => $msg) {
            if ($index === 0 && $msg['role'] === 'model') continue;
            
            $text = trim($msg['content'] ?? '');
            if (empty($text)) continue;
            
            $rawMessages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'text' => $text
            ];
        }

        // Append current message
        $rawMessages[] = [
            'role' => 'user',
            'text' => trim($request->input('message'))
        ];

        // Ensure alternating roles (Gemini requirement)
        foreach ($rawMessages as $msg) {
            $role = $msg['role'];
            $text = $msg['text'];
            
            $lastIndex = count($contents) - 1;
            if ($lastIndex >= 0 && $contents[$lastIndex]['role'] === $role) {
                // Merge with previous message of the same role
                $contents[$lastIndex]['parts'][0]['text'] .= "\n\n" . $text;
            } else {
                // Add new message
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]]
                ];
            }
        }

        try {
            $model = config('services.gemini.model', 'gemini-2.0-flash');
            
            // Request Payload
            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ];

            // Google Gemini API Request
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

            // Fallback for 503 Service Unavailable (Model Overloaded)
            if ($response->status() === 503) {
                Log::warning("Gemini API 503 on {$model}, retrying with gemini-2.0-flash-lite");
                $fallbackModel = 'gemini-2.0-flash-lite-preview-02-05';
                $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$fallbackModel}:generateContent?key={$apiKey}", $payload);
                
                // Secondary Fallback to 1.5 flash if 2.0 lite also fails
                if ($response->status() === 503) {
                    $fallbackModel = 'gemini-1.5-flash';
                    $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$fallbackModel}:generateContent?key={$apiKey}", $payload);
                }
            }

            if ($response->successful()) {
                $data = $response->json();
                
                // Safely extract the text to prevent PHP ErrorExceptions
                $reply = data_get($data, 'candidates.0.content.parts.0.text');
                
                if (empty($reply)) {
                    // Handle safety block or finish reason
                    $blockReason = data_get($data, 'promptFeedback.blockReason');
                    $finishReason = data_get($data, 'candidates.0.finishReason');
                    
                    if ($blockReason) {
                        $reply = 'عذراً، لا يمكنني مناقشة هذا الموضوع لأسباب تتعلق بسياسات الأمان.';
                    } else if ($finishReason) {
                        $reply = 'توقف توليد النص. السبب: ' . $finishReason;
                    } else {
                        $reply = 'عذراً، تلقيت استجابة غير مفهومة من الخادم.';
                    }
                }
                
                // Force inject the PDF link if the user asked about export or PDF
                if (preg_match('/(تصدير|ديوانة|export|customs|كراس الشروط|korras|chorout|chourout|cahier des charges|pdf)/ui', $request->input('message'))) {
                    $pdfLink = '<br><br><a href="/downloads/cahier_des_charges_export.pdf" download="cahier_des_charges_export.pdf" class="bg-[#6A8F3B] text-white p-2 rounded text-sm inline-block text-center w-full hover:bg-[#5a7a2f] shadow-md mt-2">📄 تحميل كراس الشروط (PDF)</a>';
                    if (strpos($reply, 'cahier_des_charges_export.pdf') === false) {
                        $reply .= $pdfLink;
                    }
                }

                return response()->json([
                    'reply' => $reply
                ]);
            }

            $errorBody = $response->body();
            Log::error('Gemini API Error: ' . $errorBody);
            
            // Fallback to local logic if the AI API fails (quota, 503, invalid key, etc.)
            $reply = $this->handleFallbackIntent($request->input('message'));
            
            return response()->json([
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            $reply = $this->handleFallbackIntent($request->input('message'));
            return response()->json([
                'reply' => $reply
            ]);
        }
    }

    private function handleFallbackIntent($message)
    {
        $state = session('chatbot_state');
        $isLoggedIn = auth()->check();
        $user = auth()->user();
        $userName = $isLoggedIn ? htmlspecialchars($user->name) : '';

        // ============================================
        // 1. PRODUCT LISTING FLOW (Choice-Driven)
        // ============================================
        if ($state === 'listing_step_1') {
            $cat = (str_contains($message, 'زيتون') || str_contains($message, 'olive') && !str_contains($message, 'oil')) ? 'olive' : 'oil';
            session(['chatbot_listing_category' => $cat]);
            session(['chatbot_state' => 'listing_step_2']);

            if ($cat === 'oil') {
                return 'ممتاز، بيع <b>زيت الزيتون</b> 🛢️.<br><br><b>اختر صنف الزيت (Variety):</b><br>' .
                       '<div class="flex flex-wrap gap-2 mt-3">' .
                       $this->buildChoiceButton('شملالي (Chemlali)', 'شملالي') .
                       $this->buildChoiceButton('شتوي (Chetoui)', 'شتوي') .
                       $this->buildChoiceButton('وسلاتي (Oueslati)', 'وسلاتي') .
                       $this->buildChoiceButton('زرازي (Zarrazi)', 'زرازي') .
                       $this->buildChoiceButton('زلماطي (Zalmati)', 'زلماطي') .
                       $this->buildChoiceButton('صنف آخر', 'أخرى') .
                       '</div>';
            } else {
                return 'ممتاز، بيع <b>الزيتون (حب / سانية)</b> 🫒.<br><br><b>اختر صنف الزيتون:</b><br>' .
                       '<div class="flex flex-wrap gap-2 mt-3">' .
                       $this->buildChoiceButton('شملالي (معاصر)', 'شملالي') .
                       $this->buildChoiceButton('شتوي', 'شتوي') .
                       $this->buildChoiceButton('مسكي (طاولة)', 'مسكي') .
                       $this->buildChoiceButton('بروني', 'بروني') .
                       $this->buildChoiceButton('زرازي', 'زرازي') .
                       $this->buildChoiceButton('سانية كاملة للخضارة', 'سانية') .
                       '</div>';
            }
        }

        if ($state === 'listing_step_2') {
            $variety = $this->normalizeVariety($message);
            session(['chatbot_listing_variety' => $variety]);
            session(['chatbot_state' => 'listing_step_3']);

            $cat = session('chatbot_listing_category', 'oil');
            if ($cat === 'oil') {
                return 'أحسنت! <b>اختر درجة الجودة (Quality):</b><br>' .
                       '<div class="flex flex-wrap gap-2 mt-3">' .
                       $this->buildChoiceButton('🌟 بكر ممتاز (Extra Virgin)', 'بكر ممتاز') .
                       $this->buildChoiceButton('🛢️ بكر (Virgin)', 'بكر') .
                       $this->buildChoiceButton('🌿 بيولوجي (Bio Organic)', 'بيولوجي') .
                       $this->buildChoiceButton('💡 وقاد (Lampante)', 'وقاد') .
                       '</div>';
            } else {
                return 'أحسنت! <b>طريقة البيع والتسليم:</b><br>' .
                       '<div class="flex flex-wrap gap-2 mt-3">' .
                       $this->buildChoiceButton('⚖️ بيع بالكيلوغرام / الطن (مقطوع)', 'مقطوع') .
                       $this->buildChoiceButton('🌳 سانية كاملة على رؤوس أشجارها (خضارة)', 'خضارة') .
                       '</div>';
            }
        }

        if ($state === 'listing_step_3') {
            $quality = $this->normalizeQuality($message);
            $cat = session('chatbot_listing_category', 'oil');
            $variety = session('chatbot_listing_variety', 'chemlali');

            session()->forget(['chatbot_state', 'chatbot_listing_category', 'chatbot_listing_variety']);

            $createUrl = "/listings/create?category={$cat}&variety={$variety}" . ($quality ? "&quality={$quality}" : "");

            if (!$isLoggedIn) {
                return 'تم تجهيز بيانات إعلانك بنجاح! 🫒✨<br><br>' .
                       '<b>ملاحظة:</b> يتطلب نشر الإعلان حساباً مجانياً لتتمكن المعاصر والمشترون من الاتصال بك مباشرة:<br><br>' .
                       '<div class="flex flex-col gap-2 mt-2">' .
                       '<a href="/register/role?role=farmer&redirect=' . urlencode($createUrl) . '" class="bg-[#16a34a] text-white p-3 rounded-xl text-xs font-bold text-center hover:bg-[#15803d] transition shadow-sm">👨‍🌾 إنشاء حساب فلاح مجاناً ونشر الإعلان</a>' .
                       '<a href="/login?redirect=' . urlencode($createUrl) . '" class="bg-gray-700 text-white p-2.5 rounded-xl text-xs font-bold text-center hover:bg-gray-800 transition">🔑 لدي حساب بالفعل (تسجيل الدخول)</a>' .
                       '</div>';
            }

            return 'تم تجهيز بيانات إعلانك بنجاح يا ' . $userName . '! 🎉<br><br>' .
                   'الصنف والجودة محددين بدقة. اضغط أدناه لرفع صورة المنتج ونشر الإعلان فوراً:<br><br>' .
                   '<a href="' . $createUrl . '" class="bg-[#6A8F3B] text-white p-3.5 rounded-xl text-sm font-bold text-center block shadow-lg hover:bg-[#5a7a2f] transition transform hover:scale-[1.02]">' .
                   '📸 رفع الصورة ونشر الإعلان الآن' .
                   '</a>';
        }

        // ============================================
        // 2. DEALS & OPPORTUNITIES FLOW (Choice-Driven)
        // ============================================
        if ($state === 'deal_step_1') {
            session()->forget(['chatbot_state']);
            $dealCat = str_contains($message, 'نقل') ? 'transport' : (str_contains($message, 'زيتون') ? 'olive' : 'oil');
            
            $dealsUrl = "/home#deals";
            return 'إليك الصفقات والطلبات الكبرى المتاحة حالياً: 🤝<br><br>' .
                   '<div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 leading-relaxed mb-3">' .
                   '✨ تصفح طلبات الشراء الكبرى من المعاصر والمصدرين مباشرة بدون أي عمولات.' .
                   '</div>' .
                   '<div class="flex flex-col gap-2">' .
                   '<a href="' . $dealsUrl . '" class="bg-[#d97706] text-white p-3 rounded-xl text-xs font-bold text-center hover:bg-[#b45309] transition shadow-sm">🔍 تصفح صفقات اليوم في البورصة</a>' .
                   ($isLoggedIn ? '' : '<a href="/register?redirect=' . urlencode($dealsUrl) . '" class="bg-gray-800 text-white p-2.5 rounded-xl text-xs font-bold text-center hover:bg-gray-900 transition">📝 تسجيل حساب للمشاركة في الصفقات</a>') .
                   '</div>';
        }

        // ============================================
        // 3. REGISTRATION FLOW (Choice-Driven)
        // ============================================
        if ($state === 'register_step_1') {
            $role = 'farmer';
            if (str_contains($message, 'ناقل') || str_contains($message, 'carrier')) $role = 'carrier';
            if (str_contains($message, 'معصرة') || str_contains($message, 'mill')) $role = 'mill';
            if (str_contains($message, 'معبئ') || str_contains($message, 'packer')) $role = 'packer';
            if (str_contains($message, 'مستخدم') || str_contains($message, 'normal')) $role = 'normal';

            session()->forget(['chatbot_state']);
            $url = "/register/role?role=" . $role;

            return 'ممتاز! تم اختيار دورك. اضغط أدناه لإكمال تسجيل حسابك المجاني في دقيقة واحدة:<br><br>' .
                   '<a href="' . $url . '" class="bg-[#6A8F3B] text-white p-3 rounded-xl text-sm font-bold text-center block shadow-md hover:bg-[#5a7a2f] transition">🚀 إكمال التسجيل الآن</a>';
        }

        // ============================================
        // INTENT DETECTION
        // ============================================

        // 1. Product Listing / Selling Intent
        if (preg_match('/(بيع|إضافة منتج|زيتون|زيت|نحب نبيع|نهبط سلعة|نصب زيت|nbi3|nhebb nbi3|nhabbat sel3a|zite|zitoun|bi3|vendre|ajouter|produit|sell|add product|olive oil)/ui', $message)) {
            session(['chatbot_state' => 'listing_step_1']);
            
            $headerText = $isLoggedIn 
                ? 'مرحباً بك يا ' . $userName . '! يسعدنا مساعدتك في نشر إعلانك. 🫒' 
                : 'تريد بيع منتجاتك والوصول لآلاف المشترين والمعاصر؟ 🫒';

            return $headerText . '<br><br><b>أولاً، اختر نوع المنتج:</b><br>' .
                   '<div class="flex flex-col sm:flex-row gap-2 mt-3">' .
                   $this->buildChoiceButton('🛢️ زيت زيتون', 'زيت') .
                   $this->buildChoiceButton('🫒 زيتون حب / سانية', 'زيتون') .
                   '</div>';
        }

        // 2. Deals / Opportunities Intent
        if (preg_match('/(صفقة|صفقات|عروض كبرى|طلبات شراء|شراء كمية|تصدير بالجملة|deal|deals|bulk|opportunit|achats|offres)/ui', $message)) {
            session(['chatbot_state' => 'deal_step_1']);
            return 'مرحباً بك في قسم <b>الصفقات والفرص الكبرى</b> في ZinToop! 🤝<br><br><b>ما نوع الصفقات التي تهمك؟</b><br>' .
                   '<div class="flex flex-col gap-2 mt-3">' .
                   $this->buildChoiceButton('🛢️ صفقات زيت الزيتون (كميات كبرى)', 'صفقات زيت') .
                   $this->buildChoiceButton('🫒 صفقات الزيتون والسانية', 'صفقات زيتون') .
                   $this->buildChoiceButton('🚚 صفقات النقل واللوجستيك', 'صفقات نقل') .
                   '</div>';
        }

        // 3. Registration Intent
        if (preg_match('/(تسجيل|حساب|انضمام|اشتراك|نعمل كونط|نحب نقيد|نسجل|قيدني|nsajel|compte|n7eb n9ayed|n9ayed|na3mel compte|inscrire|inscription|register|signup|join)/ui', $message)) {
            session(['chatbot_state' => 'register_step_1']);
            return 'يسعدنا انضمامك لمنصة ZinToop! 🇹🇳<br><br><b>اختر صفتك للبدء:</b><br>' .
                   '<div class="flex flex-col gap-2 mt-3">' .
                   $this->buildChoiceButton('👨‍🌾 فلاح (منتج زيت أو زيتون)', 'فلاح') .
                   $this->buildChoiceButton('🏭 صاحب معصرة', 'معصرة') .
                   $this->buildChoiceButton('🚚 ناقل ومزود لوجستيك', 'ناقل') .
                   $this->buildChoiceButton('👤 مشتري / مستخدم عادي', 'مستخدم عادي') .
                   '</div>';
        }

        // 4. Appointment / Consultation Intent
        if (preg_match('/(اتصال|تواصل|مساعدة|نكلمكم|عاونوني|مشكلة|nkalmkom|mouchkla|help|contact|aide|problème|appeler|موعد|استشارة|rendez-vous|appointment|consultation)/ui', $message)) {
            return 'يسعدنا تقديم الاستشارة والمساعدة من خبرائنا في ZinToop! 📅<br><br>' .
                   '<div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-900 mb-3">' .
                   'يمكنك حجز موعد استشارة تجارية، قانونية، أو استفسار حول التصدير مباشرة.' .
                   '</div>' .
                   '<a href="/services/appointment/consultation" class="bg-[#2563eb] text-white p-3 rounded-xl text-xs font-bold text-center block hover:bg-[#1d4ed8] transition shadow-md">📅 حجز موعد استشارة الآن</a>';
        }

        // 5. Export / Cahier des charges Intent
        if (preg_match('/(تصدير|ديوانة|لبرة|نصدر|tasdir|nsader|lbarra|export|diwana|exporter|exportation|customs|كراس الشروط|كيفاش|الشروط|القوانين|كيفاه|korraset chourout|koraset|korras|chorout|chourout|kifech|kiféh|kifeh|cahier des charges|comment|conditions|how|requirements|pdf)/ui', $message)) {
            return 'مهتم بالتصدير (Export)؟ 🌍 عملية التصدير التونسي تخضع لكراس الشروط المعتمد لسنة 2026.<br><br>' .
                   '<a href="/downloads/cahier_des_charges_export.pdf" download="cahier_des_charges_export.pdf" class="bg-[#6A8F3B] text-white p-3 rounded-xl text-xs font-bold block text-center hover:bg-[#5a7a2f] shadow-md">📄 تحميل كراس الشروط الرسمي للتصدير (PDF)</a>';
        }

        return 'أهلاً بك! أنا «الزيتوني»، مستشارك في منصة ZinToop. كيف يمكنني مساعدتك اليوم؟<br><br>' .
               '<div class="flex flex-wrap gap-2 mt-2">' .
               $this->buildChoiceButton('🛢️ بيع زيت أو زيتون', 'نحب نبيع زيت') .
               $this->buildChoiceButton('🤝 تصفح الصفقات الكبرى', 'صفقات اليوم') .
               $this->buildChoiceButton('📄 كراس شروط التصدير', 'كراس الشروط') .
               $this->buildChoiceButton('📅 حجز استشارة', 'حجز موعد استشارة') .
               '</div>';
    }

    private function buildChoiceButton($label, $value)
    {
        $escapedVal = addslashes(htmlspecialchars($value));
        return '<button type="button" onclick="if(window.ezzitouniSendChoice){window.ezzitouniSendChoice(\'' . $escapedVal . '\');}else{const i=document.querySelector(\'div[x-data*=\\\'ezzitouniChat\\\'] input[type=\\\'text\\\']\');if(i){i.value=\'' . $escapedVal . '\';i.dispatchEvent(new Event(\'input\',{bubbles:true}));const b=document.querySelector(\'div[x-data*=\\\'ezzitouniChat\\\'] button[type=\\\'submit\\\']\');if(b)b.click();}}" class="bg-white border border-[#6A8F3B]/30 hover:border-[#6A8F3B] text-[#1B2A1B] hover:bg-[#6A8F3B] hover:text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm transform active:scale-95 text-center flex-1 min-w-[120px]">' . $label . '</button>';
    }

    private function normalizeVariety($msg)
    {
        if (str_contains($msg, 'شملالي') || str_contains($msg, 'chemlali')) return 'chemlali';
        if (str_contains($msg, 'شتوي') || str_contains($msg, 'chetoui')) return 'chetoui';
        if (str_contains($msg, 'وسلاتي') || str_contains($msg, 'oueslati')) return 'oueslati';
        if (str_contains($msg, 'زرازي') || str_contains($msg, 'zarrazi')) return 'zarrazi';
        if (str_contains($msg, 'زلماطي') || str_contains($msg, 'zalmati')) return 'zalmati';
        if (str_contains($msg, 'مسكي') || str_contains($msg, 'meski')) return 'meski';
        if (str_contains($msg, 'بروني') || str_contains($msg, 'barouni')) return 'barouni';
        if (str_contains($msg, 'شمشالي') || str_contains($msg, 'chemchali')) return 'chemchali';
        if (str_contains($msg, 'جربوي') || str_contains($msg, 'gerboui')) return 'gerboui';
        if (str_contains($msg, 'سيالي') || str_contains($msg, 'sayali')) return 'sayali';
        return 'chemlali';
    }

    private function normalizeQuality($msg)
    {
        if (str_contains($msg, 'ممتاز') || str_contains($msg, 'extra')) return 'extra_virgin';
        if (str_contains($msg, 'بكر') && !str_contains($msg, 'ممتاز')) return 'virgin';
        if (str_contains($msg, 'بيولوجي') || str_contains($msg, 'عضوي') || str_contains($msg, 'bio')) return 'organic';
        if (str_contains($msg, 'وقاد') || str_contains($msg, 'lampante')) return 'lampante';
        return null;
    }

    private function getDefaultPrompt()
    {
        return "أنت اسمك 'الزيتوني' (Ezzitouni)، وأنت المساعد الذكي والخبير التجاري والقانوني لمنصة 'ZinToop' (سوق زيت الزيتون التونسي). 
أنت خبير في:
1. أنواع الزيتون التونسي (مثل: الشملالي الذي يتميز بطعمه الخفيف والفاكهي، والشتوي الذي يتميز بطعمه القوي والمرارة المفيدة، والوسلاتي، والزلماطي).
2. قوانين التجارة الدولية وتصدير زيت الزيتون من تونس إلى العالم (الديوانة، التراخيص، شروط التصدير).
3. منصة ZinToop: هي منصة مجانية 100% تربط بين الفلاح، صاحب المعصرة، التاجر، والمصدر بدون أي عمولات خفية.
هدف المنصة: رقمنة قطاع الزيتون في تونس وتسهيل المعاملات.
دورك:
- أجب عن أسئلة المستخدمين بلهجة تونسية محترمة أو باللغة العربية الفصحى حسب لغة المستخدم.
- قدّم نصائح تقنية حول جودة زيت الزيتون (الحموضة، العصر على البارد، البيروكسيد).
- **قاعدة هامة جداً:** إذا سأل المستخدم عن 'كراس الشروط' أو 'التصدير' أو 'pdf'، يجب عليك إدراج هذا الرابط بالظبط لتحميل كراس الشروط: 
<br><a href='https://zintoop.com/downloads/cahier_des_charges_export.pdf' target='_blank'>📄 تحميل كراس الشروط (PDF)</a>
- إذا أبدى المستخدم اهتماماً بشراء أو تصدير كميات كبيرة، شجعه على ملء نموذج التواصل أو حجز موعد (Appointment) عبر المنصة. 
- كن موجزاً، ذكياً، ودقيقاً. لا تقدم معلومات كاذبة.";
    }
}
