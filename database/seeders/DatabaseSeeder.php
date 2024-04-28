<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '01412341234',
            'address' => 'Menara IMC, No 8 Jalan Sultan Ismail, Kuala Lumpur, Malaysia',
        ]);
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Burgers'],
            ['id' => 2, 'name' => 'Desserts & Sides'],
            ['id' => 3, 'name' => 'Drinks']]);
        DB::table('items')->insert([
            ['id' => 1, 'name' => 'Big Mac', 'description' => 'Two all-beef patties with lettuce, onions, pickles, cheese and special sauce in a toasted sesame seed bun.', 'price' => 14.20, 'available' => true, 'image_url' => '/storage/bigmac.png'],
            ['id' => 2, 'name' => 'Spicy Chicken McDeluxe', 'description' => 'Specially marinated whole chicken thigh meat with a delightfully crispy coat, layered with fresh lettuce and special sauce in a corn meal bun.', 'available' => true, 'price' => 14.35, 'image_url' => '/storage/spicy-mcdeluxe.png'],
            ['id' => 3, 'name' => 'Filet-O-Fish', 'description' => 'A classic favourite of a fish burger served with tartar sauce and cheddar cheese in a steamed bun.', 'available' => true, 'price' => 10.55, 'image_url' => '/storage/filetofish.png'],
            ['id' => 4, 'name' => 'Cheeseburger', 'description' => 'Complement the taste and pure Beef Burger with a slice of cheese.', 'available' => true, 'price' => 5.99, 'image_url' => '/storage/cheeseburger.png'],
            ['id' => 5, 'name' => 'French fries', 'description' => 'We only use premium Russet Burbank variety potatoes for that fluffy inside, crispy outside taste of our world-famous fries.', 'available' => true, 'price' => 6.70, 'image_url' => '/storage/fries.png'],
            ['id' => 6, 'name' => 'Oreo McFlurry', 'description' => 'Indulge in our famous Sundae, whirled with bits of Oreo.', 'available' => true, 'price' => 8.25, 'image_url' => '/storage/mcflurry-oreo.png'],
            ['id' => 7, 'name' => 'Apple Pie', 'description' => 'Crispy pie crust with tasty apple chunks.', 'available' => true, 'price' => 5.25, 'image_url' => '/storage/apple-pie.png'],
            ['id' => 8, 'name' => 'Coca Cola', 'description' => 'Adds a zing to any McDonald’s meal.', 'available' => true, 'price' => 5.80, 'image_url' => '/storage/cocacola.png'],
            ['id' => 9, 'name' => 'Iced Lemon Tea', 'description' => 'Citrusy and light flavours in every sip.', 'available' => true, 'price' => 5.80, 'image_url' => '/storage/iced-lemon-tea.png'],
            ['id' => 10, 'name' => 'Iced Milo', 'description' => 'Theres nothing like an icy drink full of chocolate and malt flavours.', 'available' => true, 'price' => 8.30, 'image_url' => '/storage/milo-iced.png'],

        ]);
        DB::table('items_categories')->insert([
            ['item_id' => 1, 'category_id' => 1],
            ['item_id' => 2, 'category_id' => 1],
            ['item_id' => 3, 'category_id' => 1],
            ['item_id' => 4, 'category_id' => 1],
            ['item_id' => 5, 'category_id' => 2],
            ['item_id' => 6, 'category_id' => 2],
            ['item_id' => 7, 'category_id' => 2],
            ['item_id' => 8, 'category_id' => 3],
            ['item_id' => 9, 'category_id' => 3],
            ['item_id' => 10, 'category_id' => 3],
        ]);

    }
}
