<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = ucwords($this->faker->words(3, true));
        $price = $this->faker->randomFloat(2, 300, 15000);
        $hasDiscount = $this->faker->boolean(40);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1000, 999999),
            'sku' => strtoupper(Str::random(3)).'-'.$this->faker->unique()->numberBetween(10000, 99999),
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'brand_id' => Brand::inRandomOrder()->value('id'),
            'short_description' => $this->faker->sentence(12),
            'description' => '<p>'.implode('</p><p>', $this->faker->paragraphs(3)).'</p>',
            'price' => $price,
            'discount_price' => $hasDiscount ? round($price * 0.8, 2) : null,
            'stock_quantity' => $this->faker->numberBetween(0, 200),
            'low_stock_threshold' => 5,
            'status' => 'active',
            'is_featured' => $this->faker->boolean(20),
            'is_new_arrival' => $this->faker->boolean(20),
            'is_best_seller' => $this->faker->boolean(15),
            'average_rating' => $this->faker->randomFloat(2, 3, 5),
            'review_count' => $this->faker->numberBetween(0, 120),
            'sales_count' => $this->faker->numberBetween(0, 500),
        ];
    }
}
