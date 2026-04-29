<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FarmerSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = [
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan@farmlink.test',
                'farm_name' => 'Bukid Fresh Farm',
                'address' => 'Bukidnon, Philippines',
                'phone' => '09171234567',
                'latitude' => 8.0515,
                'longitude' => 124.9220,
                'bio' => 'Organic vegetables and fruits straight from the farm.',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@farmlink.test',
                'farm_name' => 'Green Valley Farm',
                'address' => 'Malaybalay, Bukidnon',
                'phone' => '09181234567',
                'latitude' => 8.1575,
                'longitude' => 125.1278,
                'bio' => 'Fresh leafy greens and herbs.',
            ],
            [
                'name' => 'Pedro Reyes',
                'email' => 'pedro@farmlink.test',
                'farm_name' => 'Golden Harvest',
                'address' => 'Valencia, Bukidnon',
                'phone' => '09191234567',
                'latitude' => 7.9064,
                'longitude' => 125.0946,
                'bio' => 'High-quality root crops and rice.',
            ],

            [
                'name' => 'Juan dela Cruz',
                'email' => 'farmer@farmlink.test',
                'farm_name' => 'Dela Cruz Farm',
                'address' => 'Valencia City, Bukidnon',
                'phone' => '09171234567',
                'latitude' => 7.9063,
                'longitude' => 125.0942,
                'bio' => 'Fresh produce straight from the highlands of Bukidnon.',
            ],
        ];

        foreach ($farmers as $farmer) {
            User::updateOrCreate(
                ['email' => $farmer['email']],
                [
                    'name' => $farmer['name'],
                    'password' => Hash::make('password'),
                    'role' => 'farmer',
                    'farm_name' => $farmer['farm_name'],
                    'address' => $farmer['address'],
                    'phone' => $farmer['phone'],
                    'latitude' => $farmer['latitude'],
                    'longitude' => $farmer['longitude'],
                    'bio' => $farmer['bio'],
                    'is_approved' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}