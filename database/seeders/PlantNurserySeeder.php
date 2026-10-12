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
            'Flowering Plants',
            'Ornamental Plants',
            'Fruit-Bearing Plants',
            'Vegetable Plants',
            'Medicinal Plants',
        ];

        $categoryRecords = [];

        foreach ($categories as $name) {
            $categoryRecords[$name] = Category::firstOrCreate(['name' => $name]);
        }

        $plants = [
            [
                'name' => 'Peace Lily',
                'category' => 'Ornamental Plants',
                'stock' => 18,
                'price' => 249.00,
            ],
            [
                'name' => 'Sunflower',
                'category' => 'Flowering Plants',
                'stock' => 4,
                'price' => 89.50,
            ],
            [
                'name' => 'Calamansi',
                'category' => 'Fruit-Bearing Plants',
                'stock' => 9,
                'price' => 399.00,
            ],
            [
                'name' => 'Tomato',
                'category' => 'Vegetable Plants',
                'stock' => 0,
                'price' => 59.00,
            ],
            [
                'name' => 'Aloe Vera',
                'category' => 'Medicinal Plants',
                'stock' => 12,
                'price' => 129.00,
            ],
            [
                'name' => 'Bougainvillea',
                'category' => 'Flowering Plants',
                'stock' => 7,
                'price' => 189.00,
            ],
        ];

        foreach ($plants as $plant) {
            Plant::firstOrCreate(
                ['name' => $plant['name']],
                [
                    'category_id' => $categoryRecords[$plant['category']]->id,
                    'stock' => $plant['stock'],
                    'price' => $plant['price'],
                ],
            );
        }
    }
}
