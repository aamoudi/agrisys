<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define your fixed master data map
        $cityMap = [
            'Palestine' => ['Gaza', 'Ramallah', 'Nablus', 'Hebron', 'Rafah', 'Jenin'],
            'Jordan' => ['Amman', 'Irbid', 'Zarqa', 'Aqaba'],
            'Egypt'  => ['Cairo', 'Alexandria', 'Giza', 'Luxor'],
            'United Kingdom' => ['London', 'Manchester', 'Liverpool', 'Birmingham'],
        ];

        // 2. Loop through the map dynamically
        foreach ($cityMap as $contry => $cities) {

            // Get the country ID dynamically based on the 'name' string
            $countryId = DB::table('countries')
                ->where('name', $contry)
                ->value('id');

            if ($countryId) {
                // Prepare the rows for bulk insertion
                $rowsToInsert = [];
                foreach ($cities as $cityName) {
                    $rowsToInsert[] = [
                        'country_id' => $countryId,
                        'name' => $cityName,
                    ];
                }

                // Bulk insert for high performance
                DB::table('cities')->insert($rowsToInsert);
            }
        }
    }
}
