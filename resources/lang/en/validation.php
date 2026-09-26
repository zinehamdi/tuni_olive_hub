<?php

return [
    'qty_min_order' => 'Quantity is below the listing minimum order.',
    'insufficient_stock' => 'Insufficient stock available for this order.',
    'offer_out_of_range' => 'Offer price is outside the allowed range.',
    'pod_missing_photo' => 'At least one drop-off photo is required.',
    'pod_missing_token' => 'Either a signed PIN or a QR token is required to verify POD.',
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'string' => 'The :attribute must not be greater than :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'required' => 'The :attribute field is required.',
    'attributes' => [
        'new_media' => 'new media',
        'images' => 'images',
        'cover_photos' => 'cover photos',
        'cover_photos.*' => 'cover photos',
        'profile_picture' => 'profile picture',
        'name' => 'full name',
        'email' => 'email address',
        'phone' => 'phone number',
        'password' => 'password',
        'olive_type' => 'olive variety',
        'farm_location' => 'farm location',
        'tree_number' => 'number of trees',
    ],
];
