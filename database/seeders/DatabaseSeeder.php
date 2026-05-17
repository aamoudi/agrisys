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

        $this->call(
            [
                PermissionSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
                CountrySeeder::class,
                CitySeeder::class,
                LookupParentSeeder::class,
                LookupChildSeeder::class,
                FarmSeeder::class,
                CropSeeder::class,
                InputSeeder::class,
                HarvestSeeder::class,

            ]
        );


        // User::factory(10)->create();

        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
    }
}
