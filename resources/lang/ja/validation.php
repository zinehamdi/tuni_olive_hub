<?php

return [
    'qty_min_order' => '数量がリストの最低注文数を下回っています。',
    'insufficient_stock' => 'この注文には在庫が不足しています。',
    'offer_out_of_range' => 'オファー価格が許容範囲外です。',
    'pod_missing_photo' => '少なくとも 1 枚のドロップオフ写真が必要です。',
    'pod_missing_token' => 'POD を検証するには、署名された PIN または QR トークンが必要です。',
    'max' => [
        'numeric' => ':attributeは:max以下である必要があります。',
        'file' => ':attributeは:maxキロバイト以下である必要があります。',
        'string' => ':attributeは:max文字以下である必要があります。',
        'array' => ':attributeの項目は:max個以下である必要があります。',
    ],
    'min' => [
        'numeric' => ':attributeは:min以上である必要があります。',
        'file' => ':attributeは:minキロバイト以上である必要があります。',
        'string' => ':attributeは:min文字以上である必要があります。',
        'array' => ':attributeの項目は:min個以上である必要があります。',
    ],
    'required' => ':attributeは必須です。',
    'attributes' => [
        'new_media' => '新しいメディア',
        'images' => '画像',
        'cover_photos' => 'カバー写真',
        'cover_photos.*' => 'カバー写真',
        'profile_picture' => 'プロフィール画像',
        'name' => '氏名',
        'email' => 'メールアドレス',
        'phone' => '電話番号',
        'password' => 'パスワード',
    ],
];
