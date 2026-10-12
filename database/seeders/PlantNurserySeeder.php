<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Database\Seeder;

class PlantNurserySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Flowering Plants' => 'Colorful plants grown for their attractive blooms.',
            'Ornamental Plants' => 'Decorative plants that add greenery to indoor and outdoor spaces.',
            'Fruit-Bearing Plants' => 'Plants that produce fresh fruit for harvesting.',
            'Vegetable Plants' => 'Edible plants grown for their nutritious vegetables.',
            'Medicinal Plants' => 'Plants traditionally valued for their useful properties.',
        ];

        $categoryRecords = [];

        foreach ($categories as $name => $description) {
            $categoryRecords[$name] = Category::firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }

        $plants = [
            [
                'name' => 'Peace Lily',
                'scientific_name' => 'Spathiphyllum wallisii',
                'category' => 'Ornamental Plants',
                'quantity' => 18,
                'price' => 249.00,
                'description' => 'A popular shade-tolerant indoor plant with elegant white flowers.',
            ],
            [
                'name' => 'Sunflower',
                'scientific_name' => 'Helianthus annuus',
                'category' => 'Flowering Plants',
                'quantity' => 4,
                'price' => 89.50,
                'description' => 'A cheerful annual flower that thrives in full sunlight.',
            ],
            [
                'name' => 'Calamansi',
                'scientific_name' => 'Citrus × microcarpa',
                'category' => 'Fruit-Bearing Plants',
                'quantity' => 9,
                'price' => 399.00,
                'description' => 'A compact citrus tree producing small, tangy fruits.',
            ],
            [
                'name' => 'Tomato',
                'scientific_name' => 'Solanum lycopersicum',
                'category' => 'Vegetable Plants',
                'quantity' => 0,
                'price' => 59.00,
                'description' => 'A productive vegetable plant for sunny gardens.',
            ],
            [
                'name' => 'Aloe Vera',
                'scientific_name' => 'Aloe vera',
                'category' => 'Medicinal Plants',
                'quantity' => 12,
                'price' => 129.00,
                'description' => 'A low-maintenance succulent commonly grown for its gel.',
            ],
            [
                'name' => 'Bougainvillea',
                'scientific_name' => 'Bougainvillea glabra',
                'category' => 'Flowering Plants',
                'quantity' => 7,
                'price' => 189.00,
                'description' => 'A hardy flowering vine with vivid papery bracts.',
            ],
        ];

        foreach ($plants as $plant) {
            Plant::firstOrCreate(
                ['plant_name' => $plant['name']],
                [
                    'scientific_name' => $plant['scientific_name'],
                    'category_id' => $categoryRecords[$plant['category']]->id,
                    'quantity' => $plant['quantity'],
                    'price' => $plant['price'],
                    'description' => $plant['description'],
                ],
            );
        }
    }
}
