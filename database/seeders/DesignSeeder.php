<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use App\Models\Design;
use App\Models\DesignImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DesignSeeder extends Seeder
{
    public function run(): void
    {
        $adjectives = ['Classic', 'Modern', 'Elegant', 'Bold', 'Timeless', 'Regal', 'Sleek', 'Heritage'];

        ClothingType::where('is_custom_only', false)->get()->each(function (ClothingType $type) use ($adjectives) {
            foreach (array_slice($adjectives, 0, fake()->numberBetween(2, 4)) as $adjective) {
                $name = "{$adjective} {$type->name}";

                $design = Design::create([
                    'clothing_type_id' => $type->id,
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
                    'description' => fake()->sentence(12),
                    'base_price_kobo' => fake()->numberBetween(1500000, 8000000), // ~NGN15,000 - NGN80,000
                    'is_featured' => fake()->boolean(20),
                    'is_active' => true,
                ]);

                for ($i = 0; $i < 2; $i++) {
                    DesignImage::create([
                        'design_id' => $design->id,
                        'path' => "designs/{$design->id}/image-{$i}.jpg",
                        'sort_order' => $i,
                    ]);
                }
            }
        });
    }
}
