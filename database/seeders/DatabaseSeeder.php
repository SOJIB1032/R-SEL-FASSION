<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Admin account ---
        User::create([
            'name' => 'ShopNova Admin',
            'email' => 'admin@shopnova.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // --- Demo customer ---
        $customer = User::create([
            'name' => 'Jane Customer',
            'email' => 'customer@shopnova.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);
        Wishlist::create(['user_id' => $customer->id]);

        // --- Catalog ---
        $categories = Category::factory(8)->create();
        $brands = Brand::factory(10)->create();

        Product::factory(60)
            ->recycle($categories)
            ->recycle($brands)
            ->create();

        // --- Coupons ---
        Coupon::create([
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 500,
            'max_discount_amount' => 300,
            'usage_limit' => 500,
            'per_user_limit' => 1,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FLAT200',
            'type' => 'fixed',
            'value' => 200,
            'min_order_amount' => 1500,
            'usage_limit' => 200,
            'per_user_limit' => 1,
            'is_active' => true,
        ]);
    }
}
