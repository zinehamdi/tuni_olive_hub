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

        // ============================================
        // 1. REGISTRATION FLOW
        // ============================================
        if ($state === 'register_step_1') {
            $role = 'normal';
            if (str_contains($message, 'فلاح') || str_contains($message, 'farmer')) $role = 'farmer';
            if (str_contains($message, 'ناقل') || str_contains($message, 'carrier')) $role = 'carrier';
            if (str_contains($message, 'معصرة') || str_contains($message, 'mill')) $role = 'mill';
            if (str_contains($message, 'معبئ') || str_contains($message, 'packer')) $role = 'packer';
            
            session(['chatbot_register_role' => $role]);
            session(['chatbot_state' => 'register_step_2']);
            
            $roleNames = ['farmer' => 'فلاح', 'carrier' => 'ناقل', 'mill' => 'معصرة', 'packer' => 'مُعبئ', 'normal' => 'مستخدم عادي'];
            return 'ممتاز! لقد اخترت (' . $roleNames[$role] . ').<br><br><b>الرجاء كتابة اسمك الكامل الآن:</b>';
        }

        if ($state === 'register_step_2') {
            session(['chatbot_register_name' => $message]);
            session(['chatbot_state' => 'register_step_3']);
            return 'شكراً لك ' . htmlspecialchars($message) . '.<br><br><b>الرجاء كتابة رقم هاتفك للتواصل:</b>';
        }

        if ($state === 'register_step_3') {
            session(['chatbot_register_phone' => $message]);
            $role = session('chatbot_register_role', 'normal');
            
            if ($role === 'farmer') {
                session(['chatbot_state' => 'register_farmer_step_4']);
                return 'حسناً. <b>ما هو نوع الزيتون الذي تنتجه؟ (مثال: شتوي، ساحلي، الخ)</b>';
            } elseif ($role === 'carrier') {
                session(['chatbot_state' => 'register_carrier_step_4']);
                return 'حسناً. <b>ما هي سعة شاحنتك (بالطن)؟</b>';
            } elseif ($role === 'mill') {
                session(['chatbot_state' => 'register_mill_step_4']);
                return 'حسناً. <b>ما هو اسم المعصرة الخاصة بك؟</b>';
            } else {
                return $this->finishRegistrationFlow();
            }
        }

        if ($state === 'register_farmer_step_4') {
            session(['chatbot_register_olive_type' => $message]);
            session(['chatbot_state' => 'register_farmer_step_5']);
            return '<b>أين تقع ضيعتك (الموقع)؟</b>';
        }

        if ($state === 'register_farmer_step_5') {
            session(['chatbot_register_farm_location' => $message]);
            session(['chatbot_state' => 'register_farmer_step_6']);
            return '<b>كم عدد أشجار الزيتون لديك تقريباً؟</b>';
        }

        if ($state === 'register_farmer_step_6') {
            session(['chatbot_register_tree_number' => $message]);
            return $this->finishRegistrationFlow();
        }

        if ($state === 'register_carrier_step_4') {
            session(['chatbot_register_camion_capacity' => $message]);
            return $this->finishRegistrationFlow();
        }

        if ($state === 'register_mill_step_4') {
            session(['chatbot_register_mill_name' => $message]);
            return $this->finishRegistrationFlow();
        }

        // ============================================
        // 2. PRODUCT LISTING FLOW
        // ============================================
        if ($state === 'listing_step_1') {
            $cat = (str_contains($message, 'زيتون') || str_contains($message, 'olive') && !str_contains($message, 'oil')) ? 'olive' : 'oil';
            session(['chatbot_listing_category' => $cat]);
            session(['chatbot_state' => 'listing_step_2']);
            return 'ممتاز! <b>ما هو الصنف (Variety)؟ (مثال: شتوي، شملاوي، الخ)</b>';
        }

        if ($state === 'listing_step_2') {
            session(['chatbot_listing_variety' => $message]);
            session(['chatbot_state' => 'listing_step_3']);
            return '<b>ما هي الكمية المتوفرة للبيع؟ (مثال: 500 لتر أو 2 طن)</b>';
        }

        if ($state === 'listing_step_3') {
            session(['chatbot_listing_quantity' => $message]);
            session(['chatbot_state' => 'listing_step_4']);
            return '<b>ما هو السعر المقترح؟ (اكتب السعر أو "حسب السوق")</b>';
        }

        if ($state === 'listing_step_4') {
            $price = $message;
            $cat = session('chatbot_listing_category', 'oil');
            $variety = session('chatbot_listing_variety', '');
            $quantity = session('chatbot_listing_quantity', '');
            
            session()->forget(['chatbot_state', 'chatbot_listing_category', 'chatbot_listing_variety', 'chatbot_listing_quantity']);
            
            $url = "/listings/create?category={$cat}&variety=" . urlencode($variety) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price);
            
            return 'تم جمع بيانات المنتج! 🎉<br><br>الاستمارة جاهزة، اضغط أدناه لنشر إعلانك:<br>' .
                   '<a href="' . $url . '" class="bg-[#6A8F3B] text-white p-3 rounded-xl text-sm font-bold inline-block text-center w-full mt-3 shadow-md hover:bg-[#5a7a2f] transition">إكمال وإضافة المنتج</a>';
        }

        // ============================================
        // 3. APPOINTMENT / CONTACT FLOW
        // ============================================
        if ($state === 'appointment_step_1') {
            session(['chatbot_apt_name' => $message]);
            session(['chatbot_state' => 'appointment_step_2']);
            return 'شكراً لك ' . htmlspecialchars($message) . '.<br><br><b>الرجاء كتابة رقم هاتفك:</b>';
        }

        if ($state === 'appointment_step_2') {
            $name = session('chatbot_apt_name', '');
            $phone = $message;
            session()->forget(['chatbot_state', 'chatbot_apt_name']);
            
            $url = "/services/appointment/consultation?name=" . urlencode($name) . "&phone=" . urlencode($phone);
            
            return 'معلوماتك جاهزة لحجز الموعد! 📅<br><br>اضغط أدناه لتأكيد حجزك عبر الواتساب مباشرة:<br>' .
                   '<a href="' . $url . '" target="_blank" class="bg-[#2563eb] text-white p-3 rounded-xl text-sm font-bold inline-block text-center w-full mt-3 shadow-md hover:bg-[#1d4ed8] transition">تأكيد الحجز</a>';
        }

        // ============================================
        // INTENT DETECTION (Starting the flows)
        // ============================================
        
        // 1. Registration Intent
        if (preg_match('/(تسجيل|حساب|انضمام|اشتراك|نعمل كونط|نحب نقيد|نسجل|قيدني|nsajel|compte|n7eb n9ayed|n9ayed|na3mel compte|inscrire|inscription|register|signup|join)/ui', $message)) {
            session(['chatbot_state' => 'register_step_1']);
            return 'يسعدنا انضمامك لمنصة ZinToop!<br><br><b>أولاً، ما هو دورك في المنصة؟ (اضغط على خيار أو اكتبه)</b><br>' .
                   '<div class="flex flex-col gap-2 mt-3">' .
                   '<button type="button" onclick="const i = document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] input[type=\\\'text\\\']\'); i.value=\'فلاح\'; i.dispatchEvent(new Event(\'input\', { bubbles: true })); document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] button[type=\\\'submit\\\']\').click();" class="bg-gray-100 border border-gray-200 text-gray-800 p-2 rounded-xl text-sm hover:bg-gray-200 transition text-right">👨‍🌾 فلاح (منتج)</button>' .
                   '<button type="button" onclick="const i = document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] input[type=\\\'text\\\']\'); i.value=\'ناقل\'; i.dispatchEvent(new Event(\'input\', { bubbles: true })); document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] button[type=\\\'submit\\\']\').click();" class="bg-gray-100 border border-gray-200 text-gray-800 p-2 rounded-xl text-sm hover:bg-gray-200 transition text-right">🚚 ناقل</button>' .
                   '<button type="button" onclick="const i = document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] input[type=\\\'text\\\']\'); i.value=\'معصرة\'; i.dispatchEvent(new Event(\'input\', { bubbles: true })); document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] button[type=\\\'submit\\\']\').click();" class="bg-gray-100 border border-gray-200 text-gray-800 p-2 rounded-xl text-sm hover:bg-gray-200 transition text-right">🏭 معصرة</button>' .
                   '<button type="button" onclick="const i = document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] input[type=\\\'text\\\']\'); i.value=\'مستخدم عادي\'; i.dispatchEvent(new Event(\'input\', { bubbles: true })); document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] button[type=\\\'submit\\\']\').click();" class="bg-gray-100 border border-gray-200 text-gray-800 p-2 rounded-xl text-sm hover:bg-gray-200 transition text-right">👤 مستخدم عادي</button>' .
                   '</div>';
        }
        
        // 2. Product Listing Intent
        if (preg_match('/(بيع|إضافة منتج|زيتون|زيت|نحب نبيع|نهبط سلعة|نصب زيت|nbi3|nhebb nbi3|nhabbat sel3a|zite|zitoun|bi3|vendre|ajouter|produit|sell|add product|olive oil)/ui', $message)) {
            session(['chatbot_state' => 'listing_step_1']);
            return 'تريد بيع منتجاتك؟ ممتاز! لمساعدتك في إنشاء الإعلان بسرعة:<br><br><b>هل تريد بيع (زيتون) أم (زيت)؟</b><br>' .
                   '<div class="flex flex-col gap-2 mt-3">' .
                   '<button type="button" onclick="const i = document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] input[type=\\\'text\\\']\'); i.value=\'زيتون\'; i.dispatchEvent(new Event(\'input\', { bubbles: true })); document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] button[type=\\\'submit\\\']\').click();" class="bg-gray-100 border border-gray-200 text-gray-800 p-2 rounded-xl text-sm hover:bg-gray-200 transition text-right">🫒 زيتون</button>' .
                   '<button type="button" onclick="const i = document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] input[type=\\\'text\\\']\'); i.value=\'زيت\'; i.dispatchEvent(new Event(\'input\', { bubbles: true })); document.querySelector(\'div[x-data=\\\'ezzitouniChat()\\\'] button[type=\\\'submit\\\']\').click();" class="bg-gray-100 border border-gray-200 text-gray-800 p-2 rounded-xl text-sm hover:bg-gray-200 transition text-right">🛢️ زيت</button>' .
                   '</div>';
        }
        
        // 3. Appointment / Contact Intent
        if (preg_match('/(اتصال|تواصل|مساعدة|نكلمكم|عاونوني|مساعدة|مشكلة|nkalmkom|mouchkla|help|contact|aide|problème|appeler|موعد|استشارة|rendez-vous|appointment|consultation)/ui', $message)) {
            session(['chatbot_state' => 'appointment_step_1']);
            return 'يسعدنا تواصلك معنا لطلب استشارة أو موعد.<br><br><b>الرجاء كتابة اسمك الكريم أو اسم شركتك:</b>';
        }

        // Export (Tasdir) Intent - Keep as direct link
        if (preg_match('/(تصدير|ديوانة|لبرة|نصدر|tasdir|nsader|lbarra|export|diwana|exporter|exportation|customs|كراس الشروط|كيفاش|الشروط|القوانين|كيفاه|korraset chourout|koraset|korras|chorout|chourout|kifech|kiféh|kifeh|cahier des charges|comment|conditions|how|requirements|pdf)/ui', $message)) {
            return 'مهتم بالتصدير (Export)؟ عملية التصدير تتطلب الالتزام بكراس الشروط الخاص بالديوانة التونسية.' .
                   '<br><br><a href="/downloads/cahier_des_charges_export.pdf" download="cahier_des_charges_export.pdf" class="bg-[#6A8F3B] text-white p-2 rounded text-sm inline-block text-center w-full hover:bg-[#5a7a2f]">📄 تحميل كراس الشروط للزيت (PDF)</a>';
        }
        
        return 'عذراً، الخادم الخاص بالذكاء الاصطناعي مشغول حالياً. يمكنك استخدام الخيارات الأساسية مثل: بيع منتج، تصدير، أو الاتصال بالدعم.';
    }

    private function finishRegistrationFlow()
    {
        $role = session('chatbot_register_role', 'normal');
        $name = session('chatbot_register_name', '');
        $phone = session('chatbot_register_phone', '');
        
        $params = [
            'role' => $role,
            'name' => $name,
            'phone' => $phone,
        ];
        
        if ($role === 'farmer') {
            $params['olive_type'] = session('chatbot_register_olive_type', '');
            $params['farm_location'] = session('chatbot_register_farm_location', '');
            $params['tree_number'] = session('chatbot_register_tree_number', '');
        } elseif ($role === 'carrier') {
            $params['camion_capacity'] = session('chatbot_register_camion_capacity', '');
        } elseif ($role === 'mill') {
            $params['mill_name'] = session('chatbot_register_mill_name', '');
        }
        
        // End of flow: clear session state
        session()->forget([
            'chatbot_state', 'chatbot_register_role', 'chatbot_register_name', 'chatbot_register_phone',
            'chatbot_register_olive_type', 'chatbot_register_farm_location', 'chatbot_register_tree_number',
            'chatbot_register_camion_capacity', 'chatbot_register_mill_name'
        ]);
        
        $queryString = http_build_query($params);
        $url = "/register/role?" . $queryString;
        
        return 'لقد جمعت كل المعلومات المطلوبة! 🎉<br><br>الاستمارة الخاصة بك جاهزة. اضغط على الزر أدناه لإكمال التسجيل وإضافة صورتك:<br>' .
               '<a href="' . $url . '" class="bg-[#6A8F3B] text-white p-3 rounded-xl text-sm font-bold inline-block text-center w-full mt-3 shadow-md hover:bg-[#5a7a2f] transition">إكمال التسجيل واستكمال الاستمارة</a>';
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
