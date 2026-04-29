<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@farmlink.test'],
            [
                'name' => 'FARMLINK Admin',
                'role' => 'admin',
                'password' => 'password',
            ],
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'role' => 'buyer',
                'password' => 'password',
            ],
        );

        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
        ]);
    }
}
