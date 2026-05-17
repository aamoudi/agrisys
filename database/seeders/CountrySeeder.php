<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // IN THIS WAY WE CAN CREATE A DATA WITHOUT USING THE FACTORY
        $countries = [
            ['name' => 'Palestine', 'code' => 'PALN'],
            ['name' => 'Jordan', 'code' => 'JOD'],
            ['name' => 'Egypt', 'code' => 'EGY'],
            ['name' => 'United Kingdom', 'code' => 'UK'],
        ];

        // Using upsert or insert to seed them
        DB::table('countries')->insert($countries);

        // the seconde way by passing the CountryFactory use this below :

        // $countries = ['Palestine', 'Jordan', 'Egypt', 'United Kingdom'];
        /* foreach ($countries as $country) {
            Country::create([
                'name' => $country,
                'code' => strtoupper(substr($country, 0, 3)), // e.g., PAL, JOR
            ]);
        } */

        //Country::factory()->count(4)->create();
    }
}
