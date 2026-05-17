<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the specific Admin User    
        $owner = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $owner->assignRole('owner');

        // 2. Create 10 "Regular" Users using the Factory
        User::factory()
            ->count(3)
            ->create()
            ->each(function ($user) {
                // Assign the standard 'user' role to each
                $user->assignRole('user');
            });
    }
}
