<?php

function updateJson($path, $newKeys) {
    $content = json_decode(file_get_contents($path), true);
    $content = array_merge($content, $newKeys);
    file_put_contents($path, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

$arKeys = [
    "Transporter summoned successfully." => "تم استدعاء الناقل بنجاح.",
    "password-updated" => "تم تحديث كلمة المرور بنجاح.",
    "verification-link-sent" => "تم إرسال رابط التحقق بنجاح.",
    "profile-updated" => "تم تحديث الملف الشخصي بنجاح.",
    "Your appointment status has been updated to: " => "تم تحديث حالة موعدك إلى: ",
    "Expert Analysis" => "تحليل الخبراء",
    "min read" => "دقائق للقراءة",
    "Stay tuned to ZinToop for more updates and insights on the global and local olive oil market." => "ابقوا على تواصل مع ZinToop لمزيد من التحديثات والرؤى حول سوق زيت الزيتون العالمي والمحلي."
];

$frKeys = [
    "Transporter summoned successfully." => "Transporteur convoqué avec succès.",
    "password-updated" => "Mot de passe mis à jour avec succès.",
    "verification-link-sent" => "Lien de vérification envoyé avec succès.",
    "profile-updated" => "Profil mis à jour avec succès.",
    "Your appointment status has been updated to: " => "Le statut de votre rendez-vous a été mis à jour à : ",
    "Expert Analysis" => "Analyse d'experts",
    "min read" => "min de lecture",
    "Stay tuned to ZinToop for more updates and insights on the global and local olive oil market." => "Restez à l'écoute de ZinToop pour plus de mises à jour et d'informations sur le marché mondial et local de l'huile d'olive."
];

updateJson('resources/lang/ar.json', $arKeys);
updateJson('resources/lang/fr.json', $frKeys);
echo "Updated ar.json and fr.json\n";
