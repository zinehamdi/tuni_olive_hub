<?php
$arFile = __DIR__ . "/resources/lang/ar.json";
$json = json_decode(file_get_contents($arFile), true);

$translations = [
    "Users by Role" => "المستخدمين حسب الدور",
    "Farmers" => "المزارعين",
    "Carriers" => "الناقلين",
    "Mills" => "المعاصر",
    "Packers" => "المُعبّئين",
    "Normal Users" => "المستخدمين العاديين",
    "Marketing Analytics" => "إحصائيات التسويق",
    "Purchases" => "المبيعات",
    "Checkouts" => "بدء الشراء",
    "Cart Adds" => "إضافة للسلة",
    "Revenue" => "الإيرادات",
    "Admin Dashboard" => "لوحة تحكم الإدارة",
    "Platform moderation and statistics" => "إدارة المنصة والإحصائيات",
    "Total Users" => "إجمالي المستخدمين",
    "this week" => "هذا الأسبوع",
    "Total Listings" => "إجمالي العروض",
    "Active Listings" => "العروض النشطة",
    "Published" => "منشور",
    "Pending Listings" => "العروض المعلقة",
    "Awaiting approval" => "بانتظار الموافقة",
    "View All" => "عرض الكل",
    "No pending listings" => "لا توجد عروض معلقة",
    "Appointments" => "المواعيد",
    "Manage All" => "إدارة الكل",
    "Pending" => "معلق",
    "No pending appointments" => "لا توجد مواعيد معلقة",
    "Recent Users" => "أحدث المستخدمين",
    "Recent Listings" => "أحدث العروض",
    "Manage Users" => "إدارة المستخدمين",
    "Manage Listings" => "إدارة العروض",
    "Souk Prices" => "أسعار السوق المحلي",
    "World Prices" => "الأسعار العالمية",
    "Articles" => "المقالات",
    "Deals" => "الصفقات",
    "Deal Requests" => "طلبات الصفقات",
    "Admin Panel" => "لوحة الإدارة"
];

$updated = 0;
foreach ($translations as $en => $ar) {
    if (isset($json[$en]) && $json[$en] === $en) {
        $json[$en] = $ar;
        $updated++;
    }
}

file_put_contents($arFile, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Translated $updated key(s) in ar.json.\n";
