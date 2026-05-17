<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupChildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define your fixed master data map
        $lookupMap = [
            'Soil Type' => ['Clay', 'Loam', 'Sandy', 'Silt'],
            'Variety' => ['Tomato', 'Onion', 'Strawberry', 'Orange', 'Corn'],
            'Input Type' => ['Fertilizer', 'Pesticide', 'Water', 'Herbicide', 'Manure'],
        ];

        // 2. Loop through the map dynamically
        foreach ($lookupMap as $parent => $children) {

            // Get the parent ID dynamically based on the 'type' string
            $parentId = DB::table('lookup_parents')
                ->where('name', $parent)
                ->value('id');

            if ($parentId) {

                // Prepare the rows for bulk insertion
                $rowsToInsert = [];
                foreach ($children as $childName) {
                    $rowsToInsert[] = [
                        'lookup_parent_id' => $parentId,
                        'name' => $childName,
                    ];
                }

                // Bulk insert for high performance
                DB::table('lookup_children')->insert($rowsToInsert);
            }
        }
    }
}
