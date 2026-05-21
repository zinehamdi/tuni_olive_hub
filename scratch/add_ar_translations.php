<?php
$file = __DIR__.'/../resources/lang/ar.json';
$json = json_decode(file_get_contents($file), true) ?? [];

$new_translations = [
    "Following" => "أتابعه",
    "Follow" => "متابعة",
    "Unlike this profile?" => "إلغاء الإعجاب بهذا الملف الشخصي؟",
    "Like this profile?" => "تسجيل الإعجاب بهذا الملف الشخصي؟",
    "Unfollow this user?" => "إلغاء متابعة هذا المستخدم؟",
    "Follow this user?" => "متابعة هذا المستخدم؟",
    "An error occurred" => "حدث خطأ ما",
    "About" => "حول",
    "Additional Details" => "تفاصيل إضافية",
    "Products & Listings" => "المنتجات والإعلانات",
    "Active" => "نشط",
    "Pending" => "قيد الانتظار",
    "Sold" => "مباع",
    "Inactive" => "غير نشط",
    "Oil" => "زيت",
    "Olives" => "زيتون",
    "Product" => "المنتج",
    "units" => "وحدات",
    "View Details" => "عرض التفاصيل",
    "No listings yet" => "لا توجد إعلانات بعد",
    "This user has not added any products." => "لم يقم هذا المستخدم بإضافة أي منتجات.",
    "Number of olive trees" => "عدد أشجار الزيتون",
    "Stories" => "القصص",
    "Live" => "مباشر",
    "No stories yet" => "لا توجد قصص بعد",
    "Check back soon!" => "عد قريباً!",
    "Photo Gallery" => "معرض الصور",
    "photos" => "صور",
    "Olive grower" => "مزارع زيتون",
    "Oil mill" => "معصرة زيت",
    "Transporter" => "ناقل",
    "Packaging" => "تعبئة وتغليف",
    "Member" => "عضو",
    "reviews" => "تقييمات"
];

foreach ($new_translations as $en => $ar) {
    if (!isset($json[$en])) {
        $json[$en] = $ar;
    }
}

file_put_contents($file, json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Translations added successfully.\n";
