<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Project-specific Validation Language Lines
    |--------------------------------------------------------------------------
    */
    'qty_min_order' => 'الكمية أقل من الحد الأدنى في العرض.',
    'insufficient_stock' => 'المخزون غير كافٍ لهذه الطلبية.',
    'offer_out_of_range' => 'سعر العرض خارج النطاق المسموح.',
    'pod_missing_photo' => 'مطلوب صورة واحدة على الأقل عند التسليم.',
    'pod_missing_token' => 'يجب توفير رمز PIN موقّع أو رمز QR للتحقق من POD.',

    /*
    |--------------------------------------------------------------------------
    | Standard Validation Language Lines
    |--------------------------------------------------------------------------
    */
    'accepted' => 'يجب قبول :attribute.',
    'accepted_if' => 'يجب قبول :attribute عندما يكون :other هو :value.',
    'active_url' => ':attribute لا يمثل رابطاً صالحاً.',
    'after' => 'يجب أن يكون :attribute تاريخاً لاحقاً لـ :date.',
    'after_or_equal' => 'يجب أن يكون :attribute تاريخاً لاحقاً أو مطابقاً لـ :date.',
    'alpha' => 'يجب أن يحتوي :attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي :attribute على أحرف وأرقام وشرطات فقط.',
    'alpha_num' => 'يجب أن يحتوي :attribute على أحرف وأرقام فقط.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'before' => 'يجب أن يكون :attribute تاريخاً سابقاً لـ :date.',
    'before_or_equal' => 'يجب أن يكون :attribute تاريخاً سابقاً أو مطابقاً لـ :date.',
    'between' => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون عدد أحرف :attribute بين :min و :max.',
        'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max.',
    ],
    'boolean' => 'يجب أن تكون قيمة :attribute إما صحيح أو خطأ.',
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => ':attribute ليس تاريخاً صالحاً.',
    'date_equals' => 'يجب أن يكون :attribute تاريخاً مطابقاً لـ :date.',
    'date_format' => 'لا يتوافق :attribute مع الشكل :format.',
    'declined' => 'يجب رفض :attribute.',
    'declined_if' => 'يجب رفض :attribute عندما يكون :other هو :value.',
    'different' => 'يجب أن يكون :attribute و :other مختلفين.',
    'digits' => 'يجب أن يحتوي :attribute على :digits رقماً.',
    'digits_between' => 'يجب أن يحتوي :attribute على أرقام بين :min و :max.',
    'dimensions' => ':attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مكررة.',
    'doesnt_end_with' => 'يجب ألا ينتهي :attribute بأحد القيم التالية: :values.',
    'doesnt_start_with' => 'يجب ألا يبدأ :attribute بأحد القيم التالية: :values.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالحاً.',
    'ends_with' => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'enum' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'exists' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'file' => 'يجب أن يكون :attribute ملفاً.',
    'filled' => 'حقل :attribute إجباري.',
    'gt' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'file' => 'يجب أن يكون حجم :attribute أكبر من :value كيلوبايت.',
        'string' => 'يجب أن يتجاوز :attribute :value حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر.',
    ],
    'gte' => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :value.',
        'file' => 'يجب أن يكون حجم :attribute مساوياً أو أكبر من :value كيلوبايت.',
        'string' => 'يجب ألا يقل :attribute عن :value حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على :value عناصر على الأقل.',
    ],
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون :attribute عدداً صحيحاً.',
    'ip' => 'يجب أن يكون :attribute عنوان IP صالحاً.',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صالحاً.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صالحاً.',
    'json' => 'يجب أن يكون :attribute نصاً من نوع JSON صالحاً.',
    'lowercase' => 'يجب أن يكون :attribute بحروف صغيرة.',
    'lt' => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من :value.',
        'file' => 'يجب أن يكون حجم :attribute أصغر من :value كيلوبايت.',
        'string' => 'يجب ألا يتجاوز :attribute :value حرفاً.',
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :value.',
        'file' => 'يجب أن يكون حجم :attribute مساوياً أو أصغر من :value كيلوبايت.',
        'string' => 'يجب ألا يزيد :attribute عن :value حرفاً.',
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :value عناصر.',
    ],
    'max' => [
        'numeric' => 'يجب ألا تكون قيمة :attribute أكبر من :max.',
        'file' => 'يجب ألا يتجاوز حجم :attribute :max كيلوبايت.',
        'string' => 'يجب ألا يتجاوز :attribute :max حرفاً.',
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عناصر.',
    ],
    'max_digits' => 'يجب ألا يحتوي :attribute على أكثر من :max أرقام.',
    'mimes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'mimetypes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'min' => [
        'numeric' => 'يجب أن تكون قيمة :attribute على الأقل :min.',
        'file' => 'يجب أن يكون حجم :attribute على الأقل :min كيلوبايت.',
        'string' => 'يجب ألا يقل :attribute عن :min حروف.',
        'array' => 'يجب أن يحتوي :attribute على الأقل على :min عناصر.',
    ],
    'min_digits' => 'يجب أن يحتوي :attribute على :min أرقام على الأقل.',
    'missing' => 'يجب أن يكون حقل :attribute مفقوداً.',
    'missing_if' => 'يجب أن يكون حقل :attribute مفقوداً عندما يكون :other هو :value.',
    'missing_unless' => 'يجب أن يكون حقل :attribute مفقوداً ما لم يكن :other هو :value.',
    'missing_with' => 'يجب أن يكون حقل :attribute مفقوداً عندما يتوفر :values.',
    'missing_with_all' => 'يجب أن يكون حقل :attribute مفقوداً عندما تتوفر كل :values.',
    'multiple_of' => 'يجب أن تكون قيمة :attribute مضاعفاً لـ :value.',
    'not_in' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'not_regex' => 'صيغة :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون :attribute رقماً.',
    'password' => [
        'letters' => 'يجب أن يحتوي :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن يحتوي :attribute على حرف كبير وآخر صغير على الأقل.',
        'numbers' => 'يجب أن يحتوي :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن يحتوي :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'لقد ظهرت قيمة :attribute المحددة في تسريب بيانات سابق، يرجى اختيار كلمة مرور مختلفة.',
    ],
    'present' => 'يجب تقديم حقل :attribute.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other هو :value.',
    'prohibited_unless' => 'حقل :attribute محظور ما لم يكن :other في :values.',
    'prohibits' => 'حقل :attribute يحظر تواجد :other.',
    'regex' => 'صيغة :attribute غير صالحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي حقل :attribute على مدخلات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عند قبول :other.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما يتوفر :values.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تتوفر كل :values.',
    'required_without' => 'حقل :attribute مطلوب عندما لا يتوفر :values.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تتوفر أي من :values.',
    'same' => 'يجب أن يتطابق :attribute مع :other.',
    'size' => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size.',
        'file' => 'يجب أن يكون حجم :attribute :size كيلوبايت.',
        'string' => 'يجب أن يحتوي :attribute على :size حروف.',
        'array' => 'يجب أن يحتوي :attribute على :size عناصر.',
    ],
    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون :attribute نصاً.',
    'timezone' => 'يجب أن يكون :attribute نطاقاً زمنياً صالحاً.',
    'unique' => ':attribute مسجل بالفعل لدينا في المنصة.',
    'uploaded' => 'فشل في تحميل :attribute.',
    'uppercase' => 'يجب أن يكون :attribute بحروف كبيرة.',
    'url' => 'صيغة الرابط :attribute غير صحيحة.',
    'ulid' => 'يجب أن يكون :attribute معرف ULID صالحاً.',
    'uuid' => 'يجب أن يكون :attribute معرف UUID صالحاً.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'email' => [
            'unique' => 'البريد الإلكتروني مسجل بالفعل لدينا في المنصة، يرجى تسجيل الدخول أو استخدام بريد إلكتروني آخر.',
        ],
        'phone' => [
            'unique' => 'رقم الهاتف مسجل بالفعل لدينا في المنصة، يرجى استخدام رقم آخر.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'new_media' => 'الصور الجديدة',
        'images' => 'الصور',
        'cover_photos' => 'صور الغلاف',
        'cover_photos.*' => 'صور الغلاف',
        'profile_picture' => 'الصورة الشخصية',
        'name' => 'الاسم الكامل',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الهاتف',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'role' => 'نوع الحساب',
        'olive_type' => 'نوع الزيتون',
        'farm_location' => 'موقع المزرعة',
        'tree_number' => 'عدد الأشجار',
        'camion_capacity' => 'حمولة الشاحنة',
        'vehicle_type' => 'نوع المركبة',
        'shipping_scope' => 'نطاق الشحن',
        'commercial_register' => 'السجل التجاري',
        'tax_id' => 'المعرف الجبائي',
        'quantity' => 'الكمية',
        'price' => 'السعر',
        'city' => 'المدينة',
        'state' => 'الولاية',
        'address' => 'العنوان',
        'notes' => 'الملاحظات',
    ],
];
