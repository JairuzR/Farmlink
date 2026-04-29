<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get the seeded farmer (or create one if none exists)
        $farmer = User::where('role', 'farmer')->where('is_approved', true)->first();

        if (!$farmer) {
            $farmer = User::create([
                'name'              => 'Juan dela Cruz',
                'email'             => 'farmer@farmlink.test',
                'password'          => bcrypt('password'),
                'role'              => 'farmer',
                'phone'             => '09171234567',
                'address'           => 'Valencia City, Bukidnon',
                'farm_name'         => 'Dela Cruz Farm',
                'bio'               => 'Fresh produce straight from the highlands of Bukidnon.',
                'latitude'          => 7.9063,
                'longitude'         => 125.0942,
                'is_approved'       => true,
                'email_verified_at' => now(),
            ]);
        }

        $products = [
            [
                'title'       => 'Fresh Sayote',
                'description' => 'Freshly harvested sayote from the highlands. Great for sinigang, stir fry, or as a healthy snack. Grown without pesticides.',
                'price'       => 35.00,
                'unit'        => 'kg',
                'stock'       => 150,
                'category'    => 'Vegetables',
                'tags'        => ['organic', 'fresh', 'vegetables'],
            ],
            [
                'title'       => 'Native Chicken Eggs',
                'description' => 'Free-range native chicken eggs. Rich in flavor and nutrients compared to commercial eggs. Collected fresh daily.',
                'price'       => 12.00,
                'unit'        => 'piece',
                'stock'       => 200,
                'category'    => 'Poultry & Eggs',
                'tags'        => ['eggs', 'native', 'free-range'],
            ],
            [
                'title'       => 'Organic Pechay',
                'description' => 'Crisp and tender pechay grown organically. Perfect for soups, stir fry, and salads. Harvested every morning.',
                'price'       => 25.00,
                'unit'        => 'bundle',
                'stock'       => 80,
                'category'    => 'Vegetables',
                'tags'        => ['organic', 'leafy', 'vegetables'],
            ],
            [
                'title'       => 'Lakatan Banana',
                'description' => 'Sweet and fragrant Lakatan bananas from our farm. Sold per kilo, minimum 2 kilos per order.',
                'price'       => 60.00,
                'unit'        => 'kg',
                'stock'       => 5,  // low stock on purpose
                'category'    => 'Fruits',
                'tags'        => ['fruits', 'banana', 'fresh'],
            ],
            [
                'title'       => 'White Corn',
                'description' => 'Freshly harvested white corn. Great for boiling, grilling, or making cornmeal. Comes in bundles of 5 ears.',
                'price'       => 45.00,
                'unit'        => 'bundle',
                'stock'       => 60,
                'category'    => 'Grains & Crops',
                'tags'        => ['corn', 'grains', 'fresh'],
            ],
            [
                'title'       => 'Fresh Ginger (Luya)',
                'description' => 'Freshly harvested ginger root. Strong aroma and flavor. Great for cooking, tea, and medicinal use.',
                'price'       => 80.00,
                'unit'        => 'kg',
                'stock'       => 40,
                'category'    => 'Herbs & Spices',
                'tags'        => ['herbs', 'spices', 'organic'],
            ],
            [
                'title'       => 'Sitaw (String Beans)',
                'description' => 'Fresh and tender string beans. Sold per bundle. Harvested early morning for maximum freshness.',
                'price'       => 20.00,
                'unit'        => 'bundle',
                'stock'       => 0, // out of stock on purpose
                'category'    => 'Vegetables',
                'tags'        => ['vegetables', 'fresh'],
            ],
            [
                'title'       => 'Sweet Kamote',
                'description' => 'Naturally sweet sweet potato from Bukidnon highlands. Perfect for boiling, frying, or making kakanin.',
                'price'       => 30.00,
                'unit'        => 'kg',
                'stock'       => 120,
                'category'    => 'Root Crops',
                'tags'        => ['root crops', 'organic', 'fresh'],
            ],
        ];

        foreach ($products as $data) {
            // Find or get category
            $category = Category::where('name', $data['category'])->first();

            $stock = $data['stock'];
            $status = $stock > 10 ? 'in_stock' : ($stock > 0 ? 'low_stock' : 'out_of_stock');

            $product = Product::create([
                'user_id'      => $farmer->id,
                'category_id'  => $category?->id,
                'title'        => $data['title'],
                'slug'         => Str::slug($data['title']) . '-' . Str::random(5),
                'description'  => $data['description'],
                'price'        => $data['price'],
                'unit'         => $data['unit'],
                'stock'        => $stock,
                'minimum_order'=> 1,
                'status'       => $status,
                'is_available' => $stock > 0,
            ]);

            // Attach tags
            $tagIds = [];
            foreach ($data['tags'] as $tagName) {
                $tag = Tag::whereNull('user_id')
                    ->where('name', 'like', "%{$tagName}%")
                    ->first();
                if ($tag) $tagIds[] = $tag->id;
            }
            if ($tagIds) $product->tags()->sync($tagIds);
        }
    }
}