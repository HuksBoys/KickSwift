<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        User::updateOrCreate(
            ['email' => 'admin@kickswift.com'],
            [
                'name' => 'Admin KickSwift',
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed Customer User
        User::updateOrCreate(
            ['email' => 'customer@kickswift.com'],
            [
                'name' => 'John Doe (Customer)',
                'phone' => '089876543210',
                'role' => 'customer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 3. Seed Fields and Schedules
        $this->call(FieldSeeder::class);
    }
}
