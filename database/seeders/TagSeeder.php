<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Organic',
            'Pesticide-Free',
            'Fresh',
            'Vegetables',
            'Fruits',
            'Grains',
            'Root Crops',
            'Herbs',
            'Poultry',
            'Eggs',
            'Native',
            'Free-Range',
        ];

        foreach ($tags as $name) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                ]
            );
        }
    }
}