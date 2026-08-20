<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use App\Models\Color;
use App\Models\Fabric;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $clothingTypes = ClothingType::where('is_custom_only', false)->get();
        $fabrics = Fabric::all();
        $colors = Color::all();
        $sizes = Size::where('name', '!=', 'Custom')->get();

        foreach ($clothingTypes as $type) {
            for ($i = 0; $i < fake()->numberBetween(2, 3); $i++) {
                $name = Str::title(fake()->words(2, true).' '.$type->name);

                $product = Product::create([
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
                    'clothing_type_id' => $type->id,
                    'description' => fake()->paragraph(),
                    'base_price_kobo' => fake()->numberBetween(1000000, 6000000),
                    'sku' => 'SKU-'.Str::upper(Str::random(8)),
                    'stock_quantity' => fake()->numberBetween(0, 50),
                    'is_active' => true,
                    'published_at' => now()->subDays(fake()->numberBetween(1, 180)),
                ]);

                foreach ($colors->random(min(3, $colors->count())) as $color) {
                    foreach ($sizes->random(min(3, $sizes->count())) as $size) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'fabric_id' => $fabrics->random()->id,
                            'color_id' => $color->id,
                            'size_id' => $size->id,
                            'sku' => $product->sku.'-'.Str::upper(Str::random(4)),
                            'stock_quantity' => fake()->numberBetween(0, 20),
                        ]);
                    }
                }
            }
        }
    }
}
