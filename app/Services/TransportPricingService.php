<?php

declare(strict_types=1);

namespace App\Services;

class TransportPricingService
{
    /**
     * Vehicle capacity tiers and pricing configuration for the Tunisian market (TND).
     */
    public const TIERS = [
        'fourgonnette' => [
            'name_ar' => 'فورغونات (بارتنر / كادي حتى 1 طن)',
            'name_fr' => 'Fourgonnette (Partner / Caddy jusqu\'à 1T)',
            'name_en' => 'Van / Fourgonnette (Up to 1T)',
            'max_qty' => 1000, // Liters or Kg
            'base_fee' => 25.0, // Economic base fee for small vans
            'rate_per_km' => 0.750, // 0.75 TND per KM
            'icon' => '🚐'
        ],
        'light_commercial' => [
            'name_ar' => 'شاحنة خفيفة (ديماكس / ستافات 1 إلى 2.5 طن)',
            'name_fr' => 'Camionnette légère / Stafette (1T - 2.5T)',
            'name_en' => 'Light Commercial / Stafette (1T - 2.5T)',
            'max_qty' => 2500, // Liters or Kg
            'base_fee' => 40.0,
            'rate_per_km' => 1.000, // 1.000 TND per KM
            'icon' => '🛻'
        ],
        'light_truck' => [
            'name_ar' => 'شاحنة متوسطة (2.5 إلى 5 طن)',
            'name_fr' => 'Camionnette moyenne (2.5T - 5T)',
            'name_en' => 'Medium Truck (2.5T - 5T)',
            'max_qty' => 5000,
            'base_fee' => 70.0,
            'rate_per_km' => 1.500,
            'icon' => '🚛'
        ],
        'heavy_truck' => [
            'name_ar' => 'شاحنة ثقيلة (5 إلى 12 طن)',
            'name_fr' => 'Poids Lourd (5T - 12T)',
            'name_en' => 'Heavy Truck (5T - 12T)',
            'max_qty' => 12000,
            'base_fee' => 120.0,
            'rate_per_km' => 2.000,
            'icon' => '🚚'
        ],
        'tanker' => [
            'name_ar' => 'شاحنة صهريج غذائي (Inox > 12 طن)',
            'name_fr' => 'Citerne Alimentaire Inox (> 12T)',
            'name_en' => 'Food-grade Tanker (> 12T)',
            'max_qty' => 30000,
            'base_fee' => 220.0, // Includes certified food-grade sterile washing
            'rate_per_km' => 2.800,
            'icon' => '🛢️'
        ]
    ];

    /**
     * Calculate straight-line Haversine distance in KM between two GPS points,
     * adjusted with a 1.25 road curvature multiplier for realistic road network distance in Tunisia.
     */
    public static function calculateDistance(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): float
    {
        if (!$lat1 || !$lng1 || !$lat2 || !$lng2 || ($lat1 == 0 && $lng1 == 0) || ($lat2 == 0 && $lng2 == 0)) {
            return 35.0; // Default estimate within standard regional distance if GPS is missing
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $straightDistance = $earthRadius * $c;

        // Apply a 1.25x road factor to estimate real driving distance
        $roadDistance = $straightDistance * 1.25;

        return max(5.0, round($roadDistance, 1));
    }

    /**
     * Auto-detect the appropriate vehicle tier based on quantity and product type.
     */
    public static function getVehicleTier(float $qty, string $productType = 'oil'): array
    {
        if ($productType === 'oil' && $qty > 12000) {
            return array_merge(['tier_key' => 'tanker'], self::TIERS['tanker']);
        }

        if ($qty <= 1000) {
            return array_merge(['tier_key' => 'fourgonnette'], self::TIERS['fourgonnette']);
        } elseif ($qty <= 2500) {
            return array_merge(['tier_key' => 'light_commercial'], self::TIERS['light_commercial']);
        } elseif ($qty <= 5000) {
            return array_merge(['tier_key' => 'light_truck'], self::TIERS['light_truck']);
        } elseif ($qty <= 12000) {
            return array_merge(['tier_key' => 'heavy_truck'], self::TIERS['heavy_truck']);
        } else {
            return array_merge(['tier_key' => 'tanker'], self::TIERS['tanker']);
        }
    }

    /**
     * Calculate fair transport cost estimate in TND.
     */
    public static function estimateCost(float $qty, ?float $pickupLat, ?float $pickupLng, ?float $dropoffLat, ?float $dropoffLng, string $productType = 'oil'): array
    {
        $distanceKm = self::calculateDistance($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);
        $tier = self::getVehicleTier($qty, $productType);

        $baseFee = $tier['base_fee'];
        $kmCost = $distanceKm * $tier['rate_per_km'];
        $totalCost = round($baseFee + $kmCost, 2);

        // Fair market price range (±10%)
        $minCost = round($totalCost * 0.92, 2);
        $maxCost = round($totalCost * 1.08, 2);

        return [
            'distance_km' => $distanceKm,
            'tier' => $tier,
            'base_fee' => $baseFee,
            'rate_per_km' => $tier['rate_per_km'],
            'total_cost' => $totalCost,
            'cost_range' => [
                'min' => $minCost,
                'max' => $maxCost,
            ],
            'currency' => 'TND'
        ];
    }

    /**
     * Tunisian Governorate Centroids (lat, lng)
     */
    public const GOVERNORATES = [
        'tunis' => ['lat' => 36.8065, 'lng' => 10.1815],
        'تونس' => ['lat' => 36.8065, 'lng' => 10.1815],
        'ariana' => ['lat' => 36.8665, 'lng' => 10.1647],
        'أريانة' => ['lat' => 36.8665, 'lng' => 10.1647],
        'اريانة' => ['lat' => 36.8665, 'lng' => 10.1647],
        'ben arous' => ['lat' => 36.7533, 'lng' => 10.2222],
        'بن عروس' => ['lat' => 36.7533, 'lng' => 10.2222],
        'manouba' => ['lat' => 36.8080, 'lng' => 10.0972],
        'منوبة' => ['lat' => 36.8080, 'lng' => 10.0972],
        'nabeul' => ['lat' => 36.4561, 'lng' => 10.7376],
        'نابل' => ['lat' => 36.4561, 'lng' => 10.7376],
        'zaghouan' => ['lat' => 36.4029, 'lng' => 10.1429],
        'زغوان' => ['lat' => 36.4029, 'lng' => 10.1429],
        'bizerte' => ['lat' => 37.2744, 'lng' => 9.8739],
        'بنزرت' => ['lat' => 37.2744, 'lng' => 9.8739],
        'beja' => ['lat' => 36.7256, 'lng' => 9.1817],
        'باجة' => ['lat' => 36.7256, 'lng' => 9.1817],
        'jendouba' => ['lat' => 36.5011, 'lng' => 8.7802],
        'جندوبة' => ['lat' => 36.5011, 'lng' => 8.7802],
        'kef' => ['lat' => 36.1822, 'lng' => 8.7149],
        'الكاف' => ['lat' => 36.1822, 'lng' => 8.7149],
        'كاف' => ['lat' => 36.1822, 'lng' => 8.7149],
        'siliana' => ['lat' => 36.0847, 'lng' => 9.3708],
        'سليانة' => ['lat' => 36.0847, 'lng' => 9.3708],
        'sousse' => ['lat' => 35.8256, 'lng' => 10.6369],
        'سوسة' => ['lat' => 35.8256, 'lng' => 10.6369],
        'monastir' => ['lat' => 35.7833, 'lng' => 10.8333],
        'المنستير' => ['lat' => 35.7833, 'lng' => 10.8333],
        'منستير' => ['lat' => 35.7833, 'lng' => 10.8333],
        'mahdia' => ['lat' => 35.5047, 'lng' => 11.0622],
        'المهدية' => ['lat' => 35.5047, 'lng' => 11.0622],
        'مهدية' => ['lat' => 35.5047, 'lng' => 11.0622],
        'sfax' => ['lat' => 34.7406, 'lng' => 10.7603],
        'صفاقس' => ['lat' => 34.7406, 'lng' => 10.7603],
        'kairouan' => ['lat' => 35.6781, 'lng' => 10.0963],
        'القيروان' => ['lat' => 35.6781, 'lng' => 10.0963],
        'قيروان' => ['lat' => 35.6781, 'lng' => 10.0963],
        'kasserine' => ['lat' => 35.1676, 'lng' => 8.8365],
        'القصرين' => ['lat' => 35.1676, 'lng' => 8.8365],
        'قصرين' => ['lat' => 35.1676, 'lng' => 8.8365],
        'sidi bouzid' => ['lat' => 35.0382, 'lng' => 9.4849],
        'سيدي بوزيد' => ['lat' => 35.0382, 'lng' => 9.4849],
        'gabes' => ['lat' => 33.8815, 'lng' => 10.0982],
        'قابس' => ['lat' => 33.8815, 'lng' => 10.0982],
        'medenine' => ['lat' => 33.3549, 'lng' => 10.5055],
        'مدنين' => ['lat' => 33.3549, 'lng' => 10.5055],
        'tataouine' => ['lat' => 32.9297, 'lng' => 10.4518],
        'تطاوين' => ['lat' => 32.9297, 'lng' => 10.4518],
        'gafsa' => ['lat' => 34.4250, 'lng' => 8.7842],
        'قفصة' => ['lat' => 34.4250, 'lng' => 8.7842],
        'tozeur' => ['lat' => 33.9197, 'lng' => 8.1335],
        'توزر' => ['lat' => 33.9197, 'lng' => 8.1335],
        'kebili' => ['lat' => 33.7050, 'lng' => 8.9690],
        'قبلي' => ['lat' => 33.7050, 'lng' => 8.9690],
    ];

    /**
     * Resolve GPS coordinates from governorate name or fallback to standard centroid
     */
    public static function resolveCoordinates(?string $gov, ?float $fallbackLat = null, ?float $fallbackLng = null): array
    {
        if ($fallbackLat && $fallbackLng && ($fallbackLat != 0.0 || $fallbackLng != 0.0)) {
            return ['lat' => $fallbackLat, 'lng' => $fallbackLng];
        }

        $key = strtolower(trim((string)$gov));
        if (isset(self::GOVERNORATES[$key])) {
            return self::GOVERNORATES[$key];
        }

        // Search partial matches (e.g., "صفاقس - عقارب")
        foreach (self::GOVERNORATES as $name => $coords) {
            if (str_contains($key, $name) || str_contains($name, $key)) {
                return $coords;
            }
        }

        return ['lat' => 34.7406, 'lng' => 10.7603]; // Safe Tunisia center/Sfax centroid
    }

    /**
     * Estimate transport cost using governorate names when GPS coords are missing.
     */
    public static function estimateCostByGovernorates(float $qty, ?string $pickupGov, ?string $dropoffGov, string $productType = 'oil'): array
    {
        $pCoords = self::resolveCoordinates($pickupGov);
        $dCoords = self::resolveCoordinates($dropoffGov);

        return self::estimateCost($qty, $pCoords['lat'], $pCoords['lng'], $dCoords['lat'], $dCoords['lng'], $productType);
    }
}
