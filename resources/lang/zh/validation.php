<?php

return [
    'qty_min_order' => '数量低于列出的最小订单量。',
    'insufficient_stock' => '此订单的可用库存不足。',
    'offer_out_of_range' => '报价超出允许范围。',
    'pod_missing_photo' => '至少需要一张还车照片。',
    'pod_missing_token' => '验证 POD 需要签名的 PIN 码或 QR 令牌。',
    'max' => [
        'numeric' => ':attribute 不能大于 :max。',
        'file' => ':attribute 大小不能超过 :max KB。',
        'string' => ':attribute 不能超过 :max 个字符。',
        'array' => ':attribute 最多只能包含 :max 个项。',
    ],
    'min' => [
        'numeric' => ':attribute 必须至少为 :min。',
        'file' => ':attribute 大小至少为 :min KB。',
        'string' => ':attribute 至少包含 :min 个字符。',
        'array' => ':attribute 至少必须有 :min 个项。',
    ],
    'required' => ':attribute 字段是必需的。',
    'attributes' => [
        'new_media' => '新媒体',
        'images' => '图片',
        'cover_photos' => '封面照片',
        'cover_photos.*' => '封面照片',
        'profile_picture' => '个人资料头像',
        'name' => '姓名',
        'email' => '电子邮件',
        'phone' => '电话号码',
        'password' => '密码',
    ],
];
