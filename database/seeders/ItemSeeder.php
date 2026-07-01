<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'code' => 'l-1001',
                'product_name' => 'Laptop Dell Inspiron 15',
                'part_number' => 'DEL-INS15-001',
                'category' => 'Electronics',
                'subcategory' => 'Laptops',
                'brand_id' => 262,
                'thumbnail' => 'items/dell-inspiron-15.jpg',
                'barcode' => '1234567890123',
                'type' => 'local',
                'brand_code' => 405,
            ],
            [
                'code' => 'l-1002',
                'product_name' => 'HP LaserJet Pro',
                'part_number' => 'HP-LJP-002',
                'category' => 'Electronics',
                'subcategory' => 'Printers',
                'brand_id' => 262,
                'thumbnail' => 'items/hp-laserjet-pro.jpg',
                'barcode' => '1234567890124',
                'type' => 'local',
                'brand_code' => 405,

            ],
            [
                'code' => 'l-1003',
                'product_name' => 'Logitech MX Master 3',
                'part_number' => 'LOG-MXM3-003',
                'category' => 'Accessories',
                'subcategory' => 'Mouse',
                'brand_id' => 262,
                'thumbnail' => 'items/logitech-mx-master-3.jpg',
                'barcode' => '1234567890125',
                'type' => 'local',
                'brand_code' => 405,

            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
