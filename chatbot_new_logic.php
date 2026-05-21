<?php

class ChatbotController {

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
        if (preg_match('/(تصدير|ديوانة|لبرة|نصدر|tasdir|nsader|lbarra|export|diwana|exporter|exportation|customs|كراس الشروط|كيفاش|الشروط|القوانين|كيفاه|korraset chourout|koraset|chourout|kifech|kiféh|kifeh|cahier des charges|comment|conditions|how|requirements)/ui', $message)) {
            return 'مهتم بالتصدير (Export)؟ عملية التصدير تتطلب الالتزام بكراس الشروط الخاص بالديوانة التونسية.' .
                   '<br><br><a href="/guides/export" class="bg-[#6A8F3B] text-white p-2 rounded text-sm inline-block text-center w-full hover:bg-[#5a7a2f]">📄 تحميل كراس الشروط للزيت (PDF)</a>';
        }
        
        return 'يبدو أنني أواجه مشكلة في الاتصال بالخادم. الرجاء المحاولة مرة أخرى لاحقاً، أو تصفح المنصة مباشرة.';
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
}
