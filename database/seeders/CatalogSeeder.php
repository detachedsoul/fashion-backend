<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use App\Models\Color;
use App\Models\Fabric;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $clothingTypes = ['Senator', 'Agbada', 'Suit', 'Kaftan', 'Native Wear', 'Shirt', 'Trouser', 'Gown', 'Jacket', 'Skirt'];

        foreach ($clothingTypes as $name) {
            ClothingType::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'description' => "Custom-tailored {$name}.",
                'is_custom_only' => false,
                'is_active' => true,
            ]);
        }

        // Customers uploading their own sketch/reference rather than
        // picking a predefined garment shape.
        ClothingType::updateOrCreate(['slug' => 'custom-design'], [
            'name' => 'Custom Design',
            'description' => 'Upload your own design, sketch, or inspiration photo.',
            'is_custom_only' => true,
            'is_active' => true,
        ]);

        $fabrics = [
            'Premium Cotton' => 0,
            'Linen' => 500000,
            'Silk' => 1500000,
            'Ankara' => 300000,
            'Aso-Oke' => 2000000,
            'Wool Blend' => 800000,
            'Velvet' => 1200000,
        ];

        foreach ($fabrics as $name => $priceModifierKobo) {
            Fabric::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'description' => "{$name} fabric.",
                'price_modifier_kobo' => $priceModifierKobo,
                'stock_status' => 'in_stock',
                'is_active' => true,
            ]);
        }

        $colors = [
            'Black' => '#000000', 'White' => '#FFFFFF', 'Navy' => '#001F3F', 'Royal Blue' => '#0074D9',
            'Burgundy' => '#800020', 'Emerald Green' => '#046307', 'Charcoal Grey' => '#36454F',
            'Beige' => '#F5F5DC', 'Gold' => '#FFD700', 'Wine Red' => '#722F37',
        ];

        foreach ($colors as $name => $hex) {
            Color::updateOrCreate(['name' => $name], ['hex_code' => $hex, 'is_active' => true]);
        }

        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Custom'];

        foreach ($sizes as $i => $name) {
            Size::updateOrCreate(['name' => $name], ['sort_order' => $i]);
        }
    }
}
