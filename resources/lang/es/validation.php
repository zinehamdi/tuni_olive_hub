<?php

return [
    'qty_min_order' => 'La cantidad está por debajo del pedido mínimo del listado.',
    'insufficient_stock' => 'Stock insuficiente disponible para este pedido.',
    'offer_out_of_range' => 'El precio de oferta está fuera del rango permitido.',
    'pod_missing_photo' => 'Se requiere al menos una foto de entrega.',
    'pod_missing_token' => 'Se requiere un PIN firmado o un token QR para verificar POD.',
    'max' => [
        'numeric' => 'El valor de :attribute no debe ser mayor que :max.',
        'file' => 'El archivo :attribute no debe pesar más de :max kilobytes.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
        'array' => 'El campo :attribute no debe contener más de :max elementos.',
    ],
    'min' => [
        'numeric' => 'El valor de :attribute debe ser al menos :min.',
        'file' => 'El archivo :attribute debe ser de al menos :min kilobytes.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'array' => 'El campo :attribute debe contener al menos :min elementos.',
    ],
    'required' => 'El campo :attribute es obligatorio.',
    'attributes' => [
        'new_media' => 'nuevos medios',
        'images' => 'imágenes',
        'cover_photos' => 'fotos de portada',
        'cover_photos.*' => 'fotos de portada',
        'profile_picture' => 'foto de perfil',
        'name' => 'nombre completo',
        'email' => 'correo electrónico',
        'phone' => 'número de teléfono',
        'password' => 'contraseña',
    ],
];
