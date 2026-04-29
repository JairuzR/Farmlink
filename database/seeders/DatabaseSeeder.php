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
        User::create([
            'name'               => 'Admin',
            'email'              => 'admin@farmlink.test',
            'password'           => bcrypt('password'),
            'role'               => 'admin',
            'is_approved'        => true,
            'email_verified_at'  => now(),
        ]);

        User::create([
            'name'               => 'Test',
            'email'              => 'test@farmlink.test',
            'password'           => bcrypt('password'),
            'role'               => 'buyer',
            'is_approved'        => true,
            'email_verified_at'  => now(),
        ]);

        User::create([
            'name'               => 'Test Farmer',
            'email'              => 'farmer@farmlink.test',
            'password'           => bcrypt('password'),
            'role'               => 'farmer',
            'is_approved'        => true,
            'email_verified_at'  => now(),
        ]);

        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            ProductSeeder::class,
            FarmerSeeder::class,
        ]);
    }
}
