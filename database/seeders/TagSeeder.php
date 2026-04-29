<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
{
    $tags = [
        'Organic', 'Pesticide-Free', 'Fresh Harvest', 'Pre-Order',
        'Bulk Available', 'Seasonal', 'Native Variety', 'Low Stock',
    ];

    foreach ($tags as $name) {
        \App\Models\Tag::create([
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
        ]);
    }
}
}
