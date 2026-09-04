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
            'history' => 'array',
            'locale' => 'nullable|string|in:ar,en,fr',
        ]);

        $locale = $request->input('locale', app()->getLocale());
        if (!in_array($locale, ['ar', 'en', 'fr'])) {
            $locale = 'ar';
        }
        app()->setLocale($locale);

        $apiKey = config('services.gemini.key');
        
        if (empty($apiKey)) {
            return response()->json([
                'reply' => $this->handleFallbackIntent($request->input('message'), $locale)
            ]);
        }

        $systemInstruction = \App\Models\BotSetting::get('bot_system_prompt') ?: config('ezzitouni.system_prompt', 'أنت مساعد ذكي لمنصة ZinToop.');

        $languageMap = [
            'en' => 'English',
            'fr' => 'French',
            'ar' => 'Arabic',
        ];
        $targetLanguage = $languageMap[$locale] ?? 'Arabic';

        $langPromptDirective = "\n\n[MANDATORY MULTILINGUAL RULE]:\n" .
            "- The active platform interface language is {$targetLanguage} (locale: '{$locale}').\n" .
            "- You must fully UNDERSTAND any language, dialect, or typing format the user uses (Tunisian Derja, Franco-Arab e.g. 'nheb nbi3 zit', standard Arabic, French, English).\n" .
            "- HOWEVER, you MUST FORMULATE YOUR ENTIRE RESPONSE EXCLUSIVELY IN {$targetLanguage}.\n" .
            "- Any buttons, HTML cards, or links must use text in {$targetLanguage} and point to '/{$locale}/...' routes.\n" .
            "- If responding in English, write in fluent, friendly English. If responding in French, write in fluent French.";

        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "[System Instructions]:\n" . $systemInstruction . $langPromptDirective . "\n\nEnd of instructions. Acknowledge and wait for user."]]
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => "Understood. I am Ezzitouni, ready to assist in {$targetLanguage}."]]
        ];

        $rawMessages = [];
        
        foreach ($request->input('history', []) as $index => $msg) {
            if ($index === 0 && ($msg['role'] ?? '') === 'model') continue;
            
            $text = trim($msg['content'] ?? '');
            if (empty($text)) continue;
            
            $rawMessages[] = [
                'role' => ($msg['role'] ?? 'user') === 'user' ? 'user' : 'model',
                'text' => $text
            ];
        }

        $rawMessages[] = [
            'role' => 'user',
            'text' => trim($request->input('message'))
        ];

        foreach ($rawMessages as $msg) {
            $role = $msg['role'];
            $text = $msg['text'];
            
            $lastIndex = count($contents) - 1;
            if ($lastIndex >= 0 && $contents[$lastIndex]['role'] === $role) {
                $contents[$lastIndex]['parts'][0]['text'] .= "\n\n" . $text;
            } else {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]]
                ];
            }
        }

        try {
            $model = config('services.gemini.model', 'gemini-2.0-flash');
            
            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ];

            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

            if ($response->status() === 503) {
                Log::warning("Gemini API 503 on {$model}, retrying with gemini-2.0-flash-lite");
                $fallbackModel = 'gemini-2.0-flash-lite-preview-02-05';
                $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$fallbackModel}:generateContent?key={$apiKey}", $payload);
                
                if ($response->status() === 503) {
                    $fallbackModel = 'gemini-1.5-flash';
                    $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$fallbackModel}:generateContent?key={$apiKey}", $payload);
                }
            }

            if ($response->successful()) {
                $data = $response->json();
                
                $reply = data_get($data, 'candidates.0.content.parts.0.text');
                
                if (empty($reply)) {
                    $reply = $locale === 'en' 
                        ? 'Sorry, I could not generate a response right now.' 
                        : ($locale === 'fr' ? 'Désolé, je ne peux pas générer de réponse pour le moment.' : 'عذراً، لا يمكنني توليد إجابة في الوقت الحالي.');
                }
                
                if (preg_match('/(تصدير|ديوانة|export|customs|كراس الشروط|korras|chorout|chourout|cahier des charges|pdf)/ui', $request->input('message'))) {
                    $pdfTitle = $locale === 'en' ? '📄 Download Export Specifications (PDF)' : ($locale === 'fr' ? '📄 Télécharger le cahier des charges (PDF)' : '📄 تحميل كراس الشروط الرسمي للتصدير (PDF)');
                    $pdfLink = '<br><br><a href="/downloads/cahier_des_charges_export.pdf" download="cahier_des_charges_export.pdf" class="bg-[#6A8F3B] text-white p-2.5 rounded-xl text-xs font-bold inline-block text-center w-full hover:bg-[#5a7a2f] shadow-md mt-2">' . $pdfTitle . '</a>';
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
            
            $reply = $this->handleFallbackIntent($request->input('message'), $locale);
            
            return response()->json([
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            $reply = $this->handleFallbackIntent($request->input('message'), $locale);
            return response()->json([
                'reply' => $reply
            ]);
        }
    }

    private function handleFallbackIntent($message, $locale = 'ar')
    {
        $state = session('chatbot_state');
        $isLoggedIn = auth()->check();
        $user = auth()->user();
        $userName = $isLoggedIn ? htmlspecialchars($user->name) : '';

        // 1. PRODUCT LISTING FLOW
        if ($state === 'listing_step_1') {
            $cat = (str_contains($message, 'زيتون') || (str_contains($message, 'olive') && !str_contains($message, 'oil') && !str_contains($message, 'huile'))) ? 'olive' : 'oil';
            session(['chatbot_listing_category' => $cat]);
            session(['chatbot_state' => 'listing_step_2']);

            if ($cat === 'oil') {
                if ($locale === 'en') {
                    return 'Great, selling <b>Olive Oil</b> 🛢️.<br><br><b>Select the oil variety:</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('Chemlali', 'شملالي') .
                           $this->buildChoiceButton('Chetoui', 'شتوي') .
                           $this->buildChoiceButton('Oueslati', 'وسلاتي') .
                           $this->buildChoiceButton('Zarrazi', 'زرازي') .
                           $this->buildChoiceButton('Zalmati', 'زلماطي') .
                           $this->buildChoiceButton('Other Variety', 'أخرى') .
                           '</div>';
                } elseif ($locale === 'fr') {
                    return 'Parfait, vente d\'<b>Huile d\'Olive</b> 🛢️.<br><br><b>Sélectionnez la variété :</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('Chemlali', 'شملالي') .
                           $this->buildChoiceButton('Chétoui', 'شتوي') .
                           $this->buildChoiceButton('Oueslati', 'وسلاتي') .
                           $this->buildChoiceButton('Zarrazi', 'زرازي') .
                           $this->buildChoiceButton('Zalmati', 'زلماطي') .
                           $this->buildChoiceButton('Autre variété', 'أخرى') .
                           '</div>';
                } else {
                    return 'ممتاز، بيع <b>زيت الزيتون</b> 🛢️.<br><br><b>اختر صنف الزيت (Variety):</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('شملالي (Chemlali)', 'شملالي') .
                           $this->buildChoiceButton('شتوي (Chetoui)', 'شتوي') .
                           $this->buildChoiceButton('وسلاتي (Oueslati)', 'وسلاتي') .
                           $this->buildChoiceButton('زرازي (Zarrazi)', 'زرازي') .
                           $this->buildChoiceButton('زلماطي (Zalmati)', 'زلماطي') .
                           $this->buildChoiceButton('صنف آخر', 'أخرى') .
                           '</div>';
                }
            } else {
                if ($locale === 'en') {
                    return 'Great, selling <b>Olives (Harvest / Bulk)</b> 🫒.<br><br><b>Select the olive variety:</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('Chemlali', 'شملالي') .
                           $this->buildChoiceButton('Chetoui', 'شتوي') .
                           $this->buildChoiceButton('Meski (Table)', 'مسكي') .
                           $this->buildChoiceButton('Barouni', 'بروني') .
                           $this->buildChoiceButton('Zarrazi', 'زرازي') .
                           $this->buildChoiceButton('Entire Orchard (Standing)', 'سانية') .
                           '</div>';
                } elseif ($locale === 'fr') {
                    return 'Parfait, vente d\'<b>Olives (Récolte / Vrac)</b> 🫒.<br><br><b>Sélectionnez la variété :</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('Chemlali', 'شملالي') .
                           $this->buildChoiceButton('Chétoui', 'شتوي') .
                           $this->buildChoiceButton('Meski (Table)', 'مسكي') .
                           $this->buildChoiceButton('Barouni', 'بروني') .
                           $this->buildChoiceButton('Zarrazi', 'زرازي') .
                           $this->buildChoiceButton('Verger sur pied', 'سانية') .
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
        }

        if ($state === 'listing_step_2') {
            $variety = $this->normalizeVariety($message);
            session(['chatbot_listing_variety' => $variety]);
            session(['chatbot_state' => 'listing_step_3']);

            $cat = session('chatbot_listing_category', 'oil');
            if ($cat === 'oil') {
                if ($locale === 'en') {
                    return 'Excellent! <b>Select the quality grade:</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('🌟 Extra Virgin (EVOO)', 'بكر ممتاز') .
                           $this->buildChoiceButton('🛢️ Virgin', 'بكر') .
                           $this->buildChoiceButton('🌿 Bio Organic', 'بيولوجي') .
                           $this->buildChoiceButton('💡 Lampante', 'وقاد') .
                           '</div>';
                } elseif ($locale === 'fr') {
                    return 'Excellent ! <b>Sélectionnez le niveau de qualité :</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('🌟 Extra Vierge (EVOO)', 'بكر ممتاز') .
                           $this->buildChoiceButton('🛢️ Vierge', 'بكر') .
                           $this->buildChoiceButton('🌿 Bio Organique', 'بيولوجي') .
                           $this->buildChoiceButton('💡 Lampante', 'وقاد') .
                           '</div>';
                } else {
                    return 'أحسنت! <b>اختر درجة الجودة (Quality):</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('🌟 بكر ممتاز (Extra Virgin)', 'بكر ممتاز') .
                           $this->buildChoiceButton('🛢️ بكر (Virgin)', 'بكر') .
                           $this->buildChoiceButton('🌿 بيولوجي (Bio Organic)', 'بيولوجي') .
                           $this->buildChoiceButton('💡 وقاد (Lampante)', 'وقاد') .
                           '</div>';
                }
            } else {
                if ($locale === 'en') {
                    return 'Excellent! <b>Select sales format:</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('⚖️ Bulk by Kg / Ton (Harvested)', 'مقطوع') .
                           $this->buildChoiceButton('🌳 Standing Harvest (On Tree)', 'خضارة') .
                           '</div>';
                } elseif ($locale === 'fr') {
                    return 'Excellent ! <b>Format de vente :</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('⚖️ Vrac par Kg / Tonne (Récolté)', 'مقطوع') .
                           $this->buildChoiceButton('🌳 Récolte sur pied', 'خضارة') .
                           '</div>';
                } else {
                    return 'أحسنت! <b>طريقة البيع والتسليم:</b><br>' .
                           '<div class="flex flex-wrap gap-2 mt-3">' .
                           $this->buildChoiceButton('⚖️ بيع بالكيلوغرام / الطن (مقطوع)', 'مقطوع') .
                           $this->buildChoiceButton('🌳 سانية كاملة على رؤوس أشجارها (خضارة)', 'خضارة') .
                           '</div>';
                }
            }
        }

        if ($state === 'listing_step_3') {
            $quality = $this->normalizeQuality($message);
            $cat = session('chatbot_listing_category', 'oil');
            $variety = session('chatbot_listing_variety', 'chemlali');

            session()->forget(['chatbot_state', 'chatbot_listing_category', 'chatbot_listing_variety']);

            $createUrl = "/{$locale}/listings/create?category={$cat}&variety={$variety}" . ($quality ? "&quality={$quality}" : "");

            if (!$isLoggedIn) {
                if ($locale === 'en') {
                    return 'Your listing details have been prepared! 🫒✨<br><br>' .
                           '<b>Note:</b> Publishing requires a free account so buyers and mills can contact you directly:<br><br>' .
                           '<div class="flex flex-col gap-2 mt-2">' .
                           '<a href="/' . $locale . '/register/role?role=farmer&redirect=' . urlencode($createUrl) . '" class="bg-[#16a34a] text-white p-3 rounded-xl text-xs font-bold text-center hover:bg-[#15803d] transition shadow-sm">👨‍🌾 Create Free Farmer Account & Publish</a>' .
                           '<a href="/' . $locale . '/login?redirect=' . urlencode($createUrl) . '" class="bg-gray-700 text-white p-2.5 rounded-xl text-xs font-bold text-center hover:bg-gray-800 transition">🔑 Already Have an Account (Login)</a>' .
                           '</div>';
                } elseif ($locale === 'fr') {
                    return 'Vos informations d\'annonce sont prêtes ! 🫒✨<br><br>' .
                           '<b>Note :</b> La publication nécessite un compte gratuit pour que les acheteurs puissent vous contacter directement :<br><br>' .
                           '<div class="flex flex-col gap-2 mt-2">' .
                           '<a href="/' . $locale . '/register/role?role=farmer&redirect=' . urlencode($createUrl) . '" class="bg-[#16a34a] text-white p-3 rounded-xl text-xs font-bold text-center hover:bg-[#15803d] transition shadow-sm">👨‍🌾 Créer un compte agriculteur gratuit et publier</a>' .
                           '<a href="/' . $locale . '/login?redirect=' . urlencode($createUrl) . '" class="bg-gray-700 text-white p-2.5 rounded-xl text-xs font-bold text-center hover:bg-gray-800 transition">🔑 J\'ai déjà un compte (Connexion)</a>' .
                           '</div>';
                } else {
                    return 'تم تجهيز بيانات إعلانك بنجاح! 🫒✨<br><br>' .
                           '<b>ملاحظة:</b> يتطلب نشر الإعلان حساباً مجانياً لتتمكن المعاصر والمشترون من الاتصال بك مباشرة:<br><br>' .
                           '<div class="flex flex-col gap-2 mt-2">' .
                           '<a href="/' . $locale . '/register/role?role=farmer&redirect=' . urlencode($createUrl) . '" class="bg-[#16a34a] text-white p-3 rounded-xl text-xs font-bold text-center hover:bg-[#15803d] transition shadow-sm">👨‍🌾 إنشاء حساب فلاح مجاناً ونشر الإعلان</a>' .
                           '<a href="/' . $locale . '/login?redirect=' . urlencode($createUrl) . '" class="bg-gray-700 text-white p-2.5 rounded-xl text-xs font-bold text-center hover:bg-gray-800 transition">🔑 لدي حساب بالفعل (تسجيل الدخول)</a>' .
                           '</div>';
                }
            }

            if ($locale === 'en') {
                return 'Your listing details have been prepared, ' . $userName . '! 🎉<br><br>' .
                       'Variety and quality are configured. Click below to upload your product photo and publish immediately:<br><br>' .
                       '<a href="' . $createUrl . '" class="bg-[#6A8F3B] text-white p-3.5 rounded-xl text-sm font-bold text-center block shadow-lg hover:bg-[#5a7a2f] transition transform hover:scale-[1.02]">' .
                       '📸 Upload Photo & Publish Now' .
                       '</a>';
            } elseif ($locale === 'fr') {
                return 'Vos informations d\'annonce sont prêtes, ' . $userName . ' ! 🎉<br><br>' .
                       'La variété et la qualité sont sélectionnées. Cliquez ci-dessous pour ajouter la photo et publier :<br><br>' .
                       '<a href="' . $createUrl . '" class="bg-[#6A8F3B] text-white p-3.5 rounded-xl text-sm font-bold text-center block shadow-lg hover:bg-[#5a7a2f] transition transform hover:scale-[1.02]">' .
                       '📸 Télécharger la photo et publier' .
                       '</a>';
            } else {
                return 'تم تجهيز بيانات إعلانك بنجاح يا ' . $userName . '! 🎉<br><br>' .
                       'الصنف والجودة محددين بدقة. اضغط أدناه لرفع صورة المنتج ونشر الإعلان فوراً:<br><br>' .
                       '<a href="' . $createUrl . '" class="bg-[#6A8F3B] text-white p-3.5 rounded-xl text-sm font-bold text-center block shadow-lg hover:bg-[#5a7a2f] transition transform hover:scale-[1.02]">' .
                       '📸 رفع الصورة ونشر الإعلان الآن' .
                       '</a>';
            }
        }

        // 2. DEALS & OPPORTUNITIES FLOW
        if ($state === 'deal_step_1') {
            session()->forget(['chatbot_state']);
            $dealsUrl = "/{$locale}/home#deals";

            if ($locale === 'en') {
                return 'Here are the latest deals and commercial opportunities: 🤝<br><br>' .
                       '<div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 leading-relaxed mb-3">' .
                       '✨ Browse bulk purchase requests from oil mills and exporters directly without commission.' .
                       '</div>' .
                       '<div class="flex flex-col gap-2">' .
                       '<a href="' . $dealsUrl . '" class="bg-[#d97706] text-white p-3 rounded-xl text-xs font-bold text-center hover:bg-[#b45309] transition shadow-sm">🔍 Browse Today\'s Deals</a>' .
                       ($isLoggedIn ? '' : '<a href="/' . $locale . '/register?redirect=' . urlencode($dealsUrl) . '" class="bg-gray-800 text-white p-2.5 rounded-xl text-xs font-bold text-center hover:bg-gray-900 transition">📝 Register to Participate in Deals</a>') .
                       '</div>';
            } elseif ($locale === 'fr') {
                return 'Voici les dernières opportunités et contrats en cours : 🤝<br><br>' .
                       '<div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 leading-relaxed mb-3">' .
                       '✨ Parcourez les demandes d\'achat en gros des moulins et exportateurs sans commission.' .
                       '</div>' .
                       '<div class="flex flex-col gap-2">' .
                       '<a href="' . $dealsUrl . '" class="bg-[#d97706] text-white p-3 rounded-xl text-xs font-bold text-center hover:bg-[#b45309] transition shadow-sm">🔍 Découvrir les deals du jour</a>' .
                       ($isLoggedIn ? '' : '<a href="/' . $locale . '/register?redirect=' . urlencode($dealsUrl) . '" class="bg-gray-800 text-white p-2.5 rounded-xl text-xs font-bold text-center hover:bg-gray-900 transition">📝 S\'inscrire pour participer aux deals</a>') .
                       '</div>';
            } else {
                return 'إليك الصفقات والطلبات الكبرى المتاحة حالياً: 🤝<br><br>' .
                       '<div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 leading-relaxed mb-3">' .
                       '✨ تصفح طلبات الشراء الكبرى من المعاصر والمصدرين مباشرة بدون أي عمولات.' .
                       '</div>' .
                       '<div class="flex flex-col gap-2">' .
                       '<a href="' . $dealsUrl . '" class="bg-[#d97706] text-white p-3 rounded-xl text-xs font-bold text-center hover:bg-[#b45309] transition shadow-sm">🔍 تصفح صفقات اليوم في البورصة</a>' .
                       ($isLoggedIn ? '' : '<a href="/' . $locale . '/register?redirect=' . urlencode($dealsUrl) . '" class="bg-gray-800 text-white p-2.5 rounded-xl text-xs font-bold text-center hover:bg-gray-900 transition">📝 تسجيل حساب للمشاركة في الصفقات</a>') .
                       '</div>';
            }
        }

        // 3. REGISTRATION FLOW
        if ($state === 'register_step_1') {
            $role = 'farmer';
            if (str_contains($message, 'ناقل') || str_contains($message, 'carrier') || str_contains($message, 'transport')) $role = 'carrier';
            if (str_contains($message, 'معصرة') || str_contains($message, 'mill') || str_contains($message, 'moulin')) $role = 'mill';
            if (str_contains($message, 'معبئ') || str_contains($message, 'packer') || str_contains($message, 'conditionneur')) $role = 'packer';
            if (str_contains($message, 'مستخدم') || str_contains($message, 'normal') || str_contains($message, 'buyer') || str_contains($message, 'acheteur')) $role = 'normal';

            session()->forget(['chatbot_state']);
            $url = "/{$locale}/register/role?role=" . $role;

            $btnText = $locale === 'en' ? '🚀 Complete Registration Now' : ($locale === 'fr' ? '🚀 Compléter l\'inscription maintenant' : '🚀 إكمال التسجيل الآن');
            $msgText = $locale === 'en' 
                ? 'Great! Role selected. Click below to complete your free registration in 1 minute:' 
                : ($locale === 'fr' ? 'Parfait ! Rôle sélectionné. Cliquez ci-dessous pour finaliser votre inscription gratuite en 1 minute :' : 'ممتاز! تم اختيار دورك. اضغط أدناه لإكمال تسجيل حسابك المجاني في دقيقة واحدة:');

            return $msgText . '<br><br><a href="' . $url . '" class="bg-[#6A8F3B] text-white p-3 rounded-xl text-sm font-bold text-center block shadow-md hover:bg-[#5a7a2f] transition">' . $btnText . '</a>';
        }

        // INTENT DETECTION
        if (preg_match('/(بيع|إضافة منتج|زيتون|زيت|نحب نبيع|نهبط سلعة|نصب زيت|nbi3|nhebb nbi3|nhabbat sel3a|zite|zitoun|bi3|vendre|ajouter|produit|sell|add product|olive oil|post listing)/ui', $message)) {
            session(['chatbot_state' => 'listing_step_1']);
            
            if ($locale === 'en') {
                $header = $isLoggedIn 
                    ? 'Welcome back, ' . $userName . '! Ready to publish your listing? 🫒' 
                    : 'Want to sell your olive oil or harvest to thousands of buyers? 🫒';
                return $header . '<br><br><b>First, select product type:</b><br>' .
                       '<div class="flex flex-col sm:flex-row gap-2 mt-3">' .
                       $this->buildChoiceButton('🛢️ Olive Oil', 'زيت') .
                       $this->buildChoiceButton('🫒 Raw Olives / Orchard', 'زيتون') .
                       '</div>';
            } elseif ($locale === 'fr') {
                $header = $isLoggedIn 
                    ? 'Bienvenue, ' . $userName . ' ! Prêt à publier votre offre ? 🫒' 
                    : 'Vous souhaitez vendre votre huile ou votre récolte d\'olives ? 🫒';
                return $header . '<br><br><b>Sélectionnez d\'abord le type de produit :</b><br>' .
                       '<div class="flex flex-col sm:flex-row gap-2 mt-3">' .
                       $this->buildChoiceButton('🛢️ Huile d\'olive', 'زيت') .
                       $this->buildChoiceButton('🫒 Olives / Verger sur pied', 'زيتون') .
                       '</div>';
            } else {
                $header = $isLoggedIn 
                    ? 'مرحباً بك يا ' . $userName . '! يسعدنا مساعدتك في نشر إعلانك. 🫒' 
                    : 'تريد بيع منتجاتك والوصول لآلاف المشترين والمعاصر؟ 🫒';
                return $header . '<br><br><b>أولاً، اختر نوع المنتج:</b><br>' .
                       '<div class="flex flex-col sm:flex-row gap-2 mt-3">' .
                       $this->buildChoiceButton('🛢️ زيت زيتون', 'زيت') .
                       $this->buildChoiceButton('🫒 زيتون حب / سانية', 'زيتون') .
                       '</div>';
            }
        }

        if (preg_match('/(صفقة|صفقات|عروض كبرى|طلبات شراء|شراء كمية|تصدير بالجملة|deal|deals|bulk|opportunit|achats|offres)/ui', $message)) {
            session(['chatbot_state' => 'deal_step_1']);
            if ($locale === 'en') {
                return 'Welcome to <b>Deals & Opportunities</b> on ZinToop! 🤝<br><br><b>What type of deals interest you?</b><br>' .
                       '<div class="flex flex-col gap-2 mt-3">' .
                       $this->buildChoiceButton('🛢️ Bulk Olive Oil Deals', 'صفقات زيت') .
                       $this->buildChoiceButton('🫒 Raw Olives & Harvest Deals', 'صفقات زيتون') .
                       $this->buildChoiceButton('🚚 Transport & Freight Deals', 'صفقات نقل') .
                       '</div>';
            } elseif ($locale === 'fr') {
                return 'Bienvenue dans la section <b>Deals & Opportunités</b> de ZinToop ! 🤝<br><br><b>Quel type de deals vous intéresse ?</b><br>' .
                       '<div class="flex flex-col gap-2 mt-3">' .
                       $this->buildChoiceButton('🛢️ Deals d\'Huile d\'Olive en Gros', 'صفقات زيت') .
                       $this->buildChoiceButton('🫒 Deals d\'Olives & Récoltes', 'صفقات زيتون') .
                       $this->buildChoiceButton('🚚 Deals Transport & Fret', 'صفقات نقل') .
                       '</div>';
            } else {
                return 'مرحباً بك في قسم <b>الصفقات والفرص الكبرى</b> في ZinToop! 🤝<br><br><b>ما نوع الصفقات التي تهمك؟</b><br>' .
                       '<div class="flex flex-col gap-2 mt-3">' .
                       $this->buildChoiceButton('🛢️ صفقات زيت الزيتون (كميات كبرى)', 'صفقات زيت') .
                       $this->buildChoiceButton('🫒 صفقات الزيتون والسانية', 'صفقات زيتون') .
                       $this->buildChoiceButton('🚚 صفقات النقل واللوجستيك', 'صفقات نقل') .
                       '</div>';
            }
        }

        if (preg_match('/(تسجيل|حساب|انضمام|اشتراك|نعمل كونط|نحب نقيد|نسجل|قيدني|nsajel|compte|n7eb n9ayed|n9ayed|na3mel compte|inscrire|inscription|register|signup|join)/ui', $message)) {
            session(['chatbot_state' => 'register_step_1']);
            if ($locale === 'en') {
                return 'Welcome to ZinToop! 🇹🇳<br><br><b>Select your role to get started:</b><br>' .
                       '<div class="flex flex-col gap-2 mt-3">' .
                       $this->buildChoiceButton('👨‍🌾 Farmer (Producer)', 'فلاح') .
                       $this->buildChoiceButton('🏭 Oil Mill Owner', 'معصرة') .
                       $this->buildChoiceButton('🚚 Transporter & Logistics', 'ناقل') .
                       $this->buildChoiceButton('👤 Buyer / Consumer', 'مستخدم عادي') .
                       '</div>';
            } elseif ($locale === 'fr') {
                return 'Bienvenue sur ZinToop ! 🇹🇳<br><br><b>Choisissez votre profil :</b><br>' .
                       '<div class="flex flex-col gap-2 mt-3">' .
                       $this->buildChoiceButton('👨‍🌾 Agriculteur (Producteur)', 'فلاح') .
                       $this->buildChoiceButton('🏭 Propriétaire de Moulin', 'معصرة') .
                       $this->buildChoiceButton('🚚 Transporteur / Logistique', 'ناقل') .
                       $this->buildChoiceButton('👤 Acheteur / Particulier', 'مستخدم عادي') .
                       '</div>';
            } else {
                return 'يسعدنا انضمامك لمنصة ZinToop! 🇹🇳<br><br><b>اختر صفتك للبدء:</b><br>' .
                       '<div class="flex flex-col gap-2 mt-3">' .
                       $this->buildChoiceButton('👨‍🌾 فلاح (منتج زيت أو زيتون)', 'فلاح') .
                       $this->buildChoiceButton('🏭 صاحب معصرة', 'معصرة') .
                       $this->buildChoiceButton('🚚 ناقل ومزود لوجستيك', 'ناقل') .
                       $this->buildChoiceButton('👤 مشتري / مستخدم عادي', 'مستخدم عادي') .
                       '</div>';
            }
        }

        if (preg_match('/(اتصال|تواصل|مساعدة|نكلمكم|عاونوني|مشكلة|nkalmkom|mouchkla|help|contact|aide|problème|appeler|موعد|استشارة|rendez-vous|appointment|consultation)/ui', $message)) {
            $aptTitle = $locale === 'en' ? '📅 Book a Consultation Now' : ($locale === 'fr' ? '📅 Réserver une consultation' : '📅 حجز موعد استشارة الآن');
            $aptDesc = $locale === 'en'
                ? 'Our trade, legal, and export specialists are ready to advise you.'
                : ($locale === 'fr' ? 'Nos experts commerciaux, juridiques et d\'exportation sont à votre disposition.' : 'يمكنك حجز موعد استشارة تجارية، قانونية، أو استفسار حول التصدير مباشرة.');
            
            return '<b>ZinToop Consultation Support</b> 📅<br><br>' .
                   '<div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-900 mb-3">' . $aptDesc . '</div>' .
                   '<a href="/' . $locale . '/services/appointment/consultation" class="bg-[#2563eb] text-white p-3 rounded-xl text-xs font-bold text-center block hover:bg-[#1d4ed8] transition shadow-md">' . $aptTitle . '</a>';
        }

        if (preg_match('/(تصدير|ديوانة|لبرة|نصدر|tasdir|nsader|lbarra|export|diwana|exporter|exportation|customs|كراس الشروط|كيفاش|الشروط|القوانين|كيفاه|korraset chourout|koraset|korras|chorout|chourout|kifech|kiféh|kifeh|cahier des charges|comment|conditions|how|requirements|pdf)/ui', $message)) {
            $pdfTitle = $locale === 'en' ? '📄 Download Export Specifications (PDF)' : ($locale === 'fr' ? '📄 Télécharger le cahier des charges (PDF)' : '📄 تحميل كراس الشروط الرسمي للتصدير (PDF)');
            $msg = $locale === 'en'
                ? 'Interested in export? 🌍 Tunisian export operations comply with the 2026 official regulations.<br><br>'
                : ($locale === 'fr' ? 'Intéressé par l\'exportation ? 🌍 Les opérations d\'exportation respectent le cahier des charges 2026.<br><br>' : 'مهتم بالتصدير (Export)؟ 🌍 عملية التصدير التونسي تخضع لكراس الشروط المعتمد لسنة 2026.<br><br>');
            
            return $msg . '<a href="/downloads/cahier_des_charges_export.pdf" download="cahier_des_charges_export.pdf" class="bg-[#6A8F3B] text-white p-3 rounded-xl text-xs font-bold block text-center hover:bg-[#5a7a2f] shadow-md">' . $pdfTitle . '</a>';
        }

        if ($locale === 'en') {
            return 'Hello! I am «Ez-Zitouni», your smart advisor on ZinToop. How can I help you today?<br><br>' .
                   '<div class="flex flex-wrap gap-2 mt-2">' .
                   $this->buildChoiceButton('🛢️ Sell Oil or Olives', 'نحب نبيع زيت') .
                   $this->buildChoiceButton('🤝 Browse Bulk Deals', 'صفقات اليوم') .
                   $this->buildChoiceButton('📄 Export Specifications', 'كراس الشروط') .
                   $this->buildChoiceButton('📅 Book Consultation', 'حجز موعد استشارة') .
                   '</div>';
        } elseif ($locale === 'fr') {
            return 'Bonjour ! Je suis « Ez-Zitouni », votre conseiller intelligent sur ZinToop. Comment puis-je vous aider ?<br><br>' .
                   '<div class="flex flex-wrap gap-2 mt-2">' .
                   $this->buildChoiceButton('🛢️ Vendre de l\'huile ou olives', 'نحب نبيع زيت') .
                   $this->buildChoiceButton('🤝 Explorer les Deals', 'صفقات اليوم') .
                   $this->buildChoiceButton('📄 Cahier des charges Export', 'كراس الشروط') .
                   $this->buildChoiceButton('📅 Réserver une consultation', 'حجز موعد استشارة') .
                   '</div>';
        } else {
            return 'أهلاً بك! أنا «الزيتوني»، مستشارك في منصة ZinToop. كيف يمكنني مساعدتك اليوم؟<br><br>' .
                   '<div class="flex flex-wrap gap-2 mt-2">' .
                   $this->buildChoiceButton('🛢️ بيع زيت أو زيتون', 'نحب نبيع زيت') .
                   $this->buildChoiceButton('🤝 تصفح الصفقات الكبرى', 'صفقات اليوم') .
                   $this->buildChoiceButton('📄 كراس شروط التصدير', 'كراس الشروط') .
                   $this->buildChoiceButton('📅 حجز استشارة', 'حجز موعد استشارة') .
                   '</div>';
        }
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
}
