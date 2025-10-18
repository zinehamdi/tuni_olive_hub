<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoukPrice;
use App\Models\WorldOlivePrice;
use Carbon\Carbon;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();
        
        // Famous Tunisian Souks with realistic olive prices
        $souks = [
            ['name' => 'Sfax', 'gov' => 'صفاقس', 'variety' => 'chemlali', 'min' => 2.50, 'max' => 3.20, 'trend' => 'up', 'change' => 2.5],
            ['name' => 'Sfax', 'gov' => 'صفاقس', 'variety' => 'chetoui', 'min' => 2.80, 'max' => 3.50, 'trend' => 'up', 'change' => 3.0],
            ['name' => 'Tunis', 'gov' => 'تونس', 'variety' => 'chemlali', 'min' => 2.60, 'max' => 3.30, 'trend' => 'stable', 'change' => 0.0],
            ['name' => 'Tunis', 'gov' => 'تونس', 'variety' => 'meski', 'min' => 2.70, 'max' => 3.40, 'trend' => 'up', 'change' => 1.5],
            ['name' => 'Sousse', 'gov' => 'سوسة', 'variety' => 'chemlali', 'min' => 2.55, 'max' => 3.25, 'trend' => 'stable', 'change' => 0.5],
            ['name' => 'Monastir', 'gov' => 'المنستير', 'variety' => 'chetoui', 'min' => 2.75, 'max' => 3.45, 'trend' => 'down', 'change' => -1.0],
            ['name' => 'Mahdia', 'gov' => 'المهدية', 'variety' => 'chemlali', 'min' => 2.45, 'max' => 3.15, 'trend' => 'up', 'change' => 2.0],
            ['name' => 'Kairouan', 'gov' => 'القيروان', 'variety' => 'chemlali', 'min' => 2.40, 'max' => 3.10, 'trend' => 'stable', 'change' => 0.0],
            ['name' => 'Medenine', 'gov' => 'مدنين', 'variety' => 'chemchali', 'min' => 2.50, 'max' => 3.20, 'trend' => 'up', 'change' => 1.8],
            ['name' => 'Zarzis', 'gov' => 'جرجيس', 'variety' => 'chemlali', 'min' => 2.48, 'max' => 3.18, 'trend' => 'stable', 'change' => 0.3],
            ['name' => 'Djerba', 'gov' => 'جربة', 'variety' => 'chetoui', 'min' => 2.85, 'max' => 3.55, 'trend' => 'up', 'change' => 2.2],
            ['name' => 'Gabes', 'gov' => 'قابس', 'variety' => 'chemlali', 'min' => 2.42, 'max' => 3.12, 'trend' => 'stable', 'change' => 0.5],
        ];

        foreach ($souks as $souk) {
            SoukPrice::create([
                'souk_name' => $souk['name'],
                'governorate' => $souk['gov'],
                'variety' => $souk['variety'],
                'product_type' => 'olive',
                'quality' => null,
                'price_min' => $souk['min'],
                'price_max' => $souk['max'],
                'price_avg' => ($souk['min'] + $souk['max']) / 2,
                'currency' => 'TND',
                'unit' => 'kg',
                'date' => $today,
                'change_percentage' => $souk['change'],
                'trend' => $souk['trend'],
                'notes' => 'Sample data for ' . $today->format('Y-m-d'),
                'is_active' => true,
            ]);
        }

        // Add olive oil prices for some souks
        $oilSouks = [
            ['name' => 'Sfax', 'gov' => 'صفاقس', 'quality' => 'EVOO', 'min' => 18.50, 'max' => 22.00, 'trend' => 'up', 'change' => 3.5],
            ['name' => 'Tunis', 'gov' => 'تونس', 'quality' => 'EVOO', 'min' => 19.00, 'max' => 23.00, 'trend' => 'up', 'change' => 4.0],
            ['name' => 'Sousse', 'gov' => 'سوسة', 'quality' => 'virgin', 'min' => 15.00, 'max' => 18.00, 'trend' => 'stable', 'change' => 0.5],
            ['name' => 'Monastir', 'gov' => 'المنستير', 'quality' => 'EVOO', 'min' => 18.00, 'max' => 21.50, 'trend' => 'up', 'change' => 2.8],
        ];

        foreach ($oilSouks as $souk) {
            SoukPrice::create([
                'souk_name' => $souk['name'],
                'governorate' => $souk['gov'],
                'variety' => 'blend',
                'product_type' => 'oil',
                'quality' => $souk['quality'],
                'price_min' => $souk['min'],
                'price_max' => $souk['max'],
                'price_avg' => ($souk['min'] + $souk['max']) / 2,
                'currency' => 'TND',
                'unit' => 'liter',
                'date' => $today,
                'change_percentage' => $souk['change'],
                'trend' => $souk['trend'],
                'notes' => 'Sample oil price for ' . $today->format('Y-m-d'),
                'is_active' => true,
            ]);
        }

        // World Market Prices (major producers)
        $worldPrices = [
            ['country' => 'Spain', 'region' => 'Andalusia', 'variety' => 'Picual', 'price' => 6.80, 'trend' => 'up', 'change' => 5.2],
            ['country' => 'Italy', 'region' => 'Tuscany', 'variety' => 'Frantoio', 'price' => 8.50, 'trend' => 'up', 'change' => 4.8],
            ['country' => 'Greece', 'region' => 'Crete', 'variety' => 'Koroneiki', 'price' => 5.90, 'trend' => 'stable', 'change' => 1.0],
            ['country' => 'Turkey', 'region' => 'Aegean', 'variety' => 'Memecik', 'price' => 5.20, 'trend' => 'up', 'change' => 3.5],
            ['country' => 'Morocco', 'region' => 'Fez-Meknes', 'variety' => 'Picholine', 'price' => 4.50, 'trend' => 'stable', 'change' => 0.8],
            ['country' => 'Portugal', 'region' => 'Alentejo', 'variety' => 'Galega', 'price' => 6.20, 'trend' => 'up', 'change' => 2.5],
            ['country' => 'Tunisia', 'region' => 'Sfax', 'variety' => 'Chemlali', 'price' => 4.80, 'trend' => 'up', 'change' => 3.0],
            ['country' => 'Syria', 'region' => 'Aleppo', 'variety' => 'Souri', 'price' => 3.90, 'trend' => 'down', 'change' => -2.0],
        ];

        foreach ($worldPrices as $price) {
            WorldOlivePrice::create([
                'country' => $price['country'],
                'region' => $price['region'],
                'variety' => $price['variety'],
                'quality' => 'EVOO',
                'price' => $price['price'],
                'currency' => 'EUR',
                'unit' => 'liter',
                'date' => $today,
                'change_percentage' => $price['change'],
                'trend' => $price['trend'],
                'source' => 'International Olive Council',
                'notes' => 'Sample world market data',
            ]);
        }

        $this->command->info('✅ Price data seeded successfully!');
        $this->command->info('📊 Created ' . (count($souks) + count($oilSouks)) . ' souk prices');
        $this->command->info('🌍 Created ' . count($worldPrices) . ' world market prices');
    }
}
