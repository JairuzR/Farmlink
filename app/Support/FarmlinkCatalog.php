<?php

namespace App\Support;

use Illuminate\Support\Str;

class FarmlinkCatalog
{
    public static function products(): array
    {
        return array_merge(self::baseProducts(), session('farmer_products', []));
    }

    public static function categories(): array
    {
        return ['All Products', 'Vegetables', 'Fruits', 'Leafy Greens', 'Root Crops'];
    }

    public static function filtered(?string $category): array
    {
        $products = self::products();

        if (! $category || $category === 'All Products') {
            return $products;
        }

        return array_values(array_filter($products, fn ($product) => $product['category'] === $category));
    }

    public static function find(string $slug): ?array
    {
        foreach (self::products() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    public static function addProduct(array $data): array
    {
        $product = [
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(5)),
            'name' => $data['name'],
            'farm' => $data['farm'],
            'price' => (float) $data['price'],
            'unit' => $data['unit'],
            'image' => $data['image'] ?: 'https://www.figma.com/api/mcp/asset/b200b88e-e4ea-44ac-97bd-dc5a656a07a5',
            'status' => 'In Stock',
            'category' => $data['category'],
            'stock' => (int) $data['stock'],
            'minimum' => max(1, (int) $data['minimum']),
            'pickup' => $data['pickup'],
            'description' => $data['description'],
        ];

        session()->push('farmer_products', $product);

        return $product;
    }

    public static function cartItems(): array
    {
        $cart = session('cart', []);

        return collect($cart)
            ->map(function (int $quantity, string $slug) {
                $product = self::find($slug);

                if (! $product) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_total' => $product['price'] * $quantity,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public static function cartTotals(): array
    {
        $subtotal = array_sum(array_column(self::cartItems(), 'line_total'));
        $delivery = $subtotal > 0 ? 50 : 0;

        return [
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total' => $subtotal + $delivery,
        ];
    }

    private static function baseProducts(): array
    {
        return [
            [
                'slug' => 'fresh-tomatoes',
                'name' => 'Fresh Tomatoes',
                'farm' => 'Juan Dela Cruz Farm',
                'price' => 120,
                'unit' => 'kg',
                'image' => 'https://www.figma.com/api/mcp/asset/d33e6fad-7a3e-4c8f-aef8-1bb601ce1ee9',
                'status' => 'In Stock',
                'category' => 'Vegetables',
                'stock' => 120,
                'minimum' => 2,
                'pickup' => 'Valencia City',
                'description' => 'Fresh, ripe tomatoes harvested daily. Perfect for sauces, salads, and Filipino home cooking.',
            ],
            [
                'slug' => 'organic-cabbage',
                'name' => 'Organic Cabbage',
                'farm' => 'Green Valley Farm',
                'price' => 85,
                'unit' => 'kg',
                'image' => 'https://www.figma.com/api/mcp/asset/b200b88e-e4ea-44ac-97bd-dc5a656a07a5',
                'status' => 'In Stock',
                'category' => 'Leafy Greens',
                'stock' => 90,
                'minimum' => 1,
                'pickup' => 'Malaybalay City',
                'description' => 'Crisp organic cabbage grown without synthetic pesticides.',
            ],
            [
                'slug' => 'red-bell-peppers',
                'name' => 'Red Bell Peppers',
                'farm' => 'Bukidnon Highlands',
                'price' => 180,
                'unit' => 'kg',
                'image' => 'https://www.figma.com/api/mcp/asset/dad4603d-3de5-4d8e-aa51-e8f072f0983c',
                'status' => 'Low Stock',
                'category' => 'Vegetables',
                'stock' => 18,
                'minimum' => 1,
                'pickup' => 'Lantapan, Bukidnon',
                'description' => 'Sweet red bell peppers with firm texture and bright color.',
            ],
            [
                'slug' => 'fresh-carrots',
                'name' => 'Fresh Carrots',
                'farm' => 'Mountain View Farm',
                'price' => 95,
                'unit' => 'kg',
                'image' => 'https://www.figma.com/api/mcp/asset/dad4603d-3de5-4d8e-aa51-e8f072f0983c',
                'status' => 'In Stock',
                'category' => 'Root Crops',
                'stock' => 75,
                'minimum' => 1,
                'pickup' => 'Impasugong, Bukidnon',
                'description' => 'Naturally sweet carrots suitable for soups, stews, and fresh juices.',
            ],
            [
                'slug' => 'native-eggplant',
                'name' => 'Native Eggplant',
                'farm' => 'Reyes Family Farm',
                'price' => 70,
                'unit' => 'kg',
                'image' => 'https://www.figma.com/api/mcp/asset/2775fbcd-e2b5-468a-9b2e-285d50f6b0c2',
                'status' => 'In Stock',
                'category' => 'Vegetables',
                'stock' => 60,
                'minimum' => 1,
                'pickup' => 'Maramag, Bukidnon',
                'description' => 'Tender native eggplant ready for grilling, torta, or pinakbet.',
            ],
            [
                'slug' => 'green-beans',
                'name' => 'Green Beans',
                'farm' => 'Fresh Fields',
                'price' => 110,
                'unit' => 'kg',
                'image' => 'https://www.figma.com/api/mcp/asset/b200b88e-e4ea-44ac-97bd-dc5a656a07a5',
                'status' => 'In Stock',
                'category' => 'Vegetables',
                'stock' => 45,
                'minimum' => 1,
                'pickup' => 'Valencia City',
                'description' => 'Fresh green beans picked young for better flavor and crunch.',
            ],
        ];
    }
}
