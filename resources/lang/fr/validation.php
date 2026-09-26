<?php

return [
    'qty_min_order' => 'La quantité est inférieure au minimum de la fiche.',
    'insufficient_stock' => 'Stock insuffisant pour cette commande.',
    'offer_out_of_range' => "Le prix de l'offre est hors de la plage autorisée.",
    'pod_missing_photo' => 'Au moins une photo de livraison est requise.',
    'pod_missing_token' => 'Un code PIN signé ou un jeton QR est requis pour vérifier le POD.',
    'max' => [
        'numeric' => 'La valeur de :attribute ne doit pas être supérieure à :max.',
        'file' => 'Le fichier :attribute ne doit pas dépasser :max kilo-octets.',
        'string' => 'Le texte :attribute ne doit pas contenir plus de :max caractères.',
        'array' => 'Le tableau :attribute ne doit pas contenir plus de :max éléments.',
    ],
    'min' => [
        'numeric' => 'La valeur de :attribute doit être au moins de :min.',
        'file' => 'Le fichier :attribute doit être au moins de :min kilo-octets.',
        'string' => 'Le texte :attribute doit contenir au moins :min caractères.',
        'array' => 'Le tableau :attribute doit contenir au moins :min éléments.',
    ],
    'required' => 'Le champ :attribute est obligatoire.',
    'attributes' => [
        'new_media' => 'nouveaux médias',
        'images' => 'images',
        'cover_photos' => 'photos de couverture',
        'cover_photos.*' => 'photos de couverture',
        'profile_picture' => 'photo de profil',
        'name' => 'nom complet',
        'email' => 'adresse e-mail',
        'phone' => 'numéro de téléphone',
        'password' => 'mot de passe',
        'olive_type' => "variété d'olives",
        'farm_location' => "emplacement de la ferme",
        'tree_number' => "nombre d'arbres",
    ],
];
