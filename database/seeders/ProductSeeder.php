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
        $farmers = User::where('role', 'farmer')
            ->where('is_approved', true)
            ->get();

        if ($farmers->isEmpty()) {
            throw new \Exception('No farmers found. Run FarmerSeeder first.');
        }

        $products = [
            [
                'title' => 'Fresh Sayote',
                'description' => 'Freshly harvested sayote from the highlands.',
                'price' => 35.00,
                'unit' => 'kg',
                'stock' => 150,
                'category' => 'Vegetables',
                'tags' => ['Organic', 'Fresh', 'Vegetables'],
            ],
            [
                'title' => 'Native Chicken Eggs',
                'description' => 'Free-range native chicken eggs.',
                'price' => 12.00,
                'unit' => 'piece',
                'stock' => 200,
                'category' => 'Poultry & Eggs',
                'tags' => ['Eggs', 'Native', 'Free-Range'],
            ],
            [
                'title' => 'Organic Pechay',
                'description' => 'Crisp and tender pechay grown organically.',
                'price' => 25.00,
                'unit' => 'bundle',
                'stock' => 80,
                'category' => 'Leafy Greens',
                'tags' => ['Organic', 'Vegetables', 'Fresh'],
            ],
            [
                'title' => 'Lakatan Banana',
                'description' => 'Sweet Lakatan bananas from our farm.',
                'price' => 60.00,
                'unit' => 'kg',
                'stock' => 50,
                'category' => 'Fruits',
                'tags' => ['Fruits', 'Fresh'],
            ],
        ];

        foreach ($products as $data) {

            $farmer = $farmers->random();

            // ✅ STRICT category match (prevents null silently)
            $category = Category::where('slug', Str::slug($data['category']))->first();

            if (!$category) {
                throw new \Exception("Category not found: {$data['category']}");
            }

            $stock = $data['stock'];

            $status = $stock > 10
                ? 'in_stock'
                : ($stock > 0 ? 'low_stock' : 'out_of_stock');

            $product = Product::create([
                'user_id' => $farmer->id,
                'category_id' => $category->id,
                'title' => $data['title'],
                'slug' => Str::slug($data['title']) . '-' . Str::random(5),
                'description' => $data['description'],
                'price' => $data['price'],
                'unit' => $data['unit'],
                'stock' => $stock,
                'minimum_order' => 1,
                'status' => $status,
                'is_available' => $stock > 0,
            ]);

            // ✅ TAGS (now guaranteed to exist or be created)
            $tagIds = [];

            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    [
                        'name' => $tagName,
                        'slug' => Str::slug($tagName),
                    ]
                );

                $tagIds[] = $tag->id;
            }

            $product->tags()->sync($tagIds);

            // ✅ IMAGE
            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'products/sample.jpg',
                'is_primary' => true,
            ]);
        }
    }
}