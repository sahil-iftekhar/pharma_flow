<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(7)->create();

        User::factory()->admin()->create();

        User::factory()->superAdmin()->create();

        User::factory()->inactive()->create();

        // Seed categories
        $categories = Category::factory(5)->create();

        // Seed medicines and attach random categories
        Medicine::factory(20)
            ->create()
            ->each(function ($medicine) use ($categories) {
                $medicine->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')->toArray()
                );
            });

        // Seed carts and cart items
        Cart::factory(5)
            ->has(CartItem::factory()->count(3), 'items')
            ->create();
    }
}
