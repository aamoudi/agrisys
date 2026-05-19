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
            'Job' => ['Farmer', 'Driver'],
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
        // Define beautiful, lightweight inline SVGs with predefined Tailwind color contexts
        $svgs = [
            'Tomato' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 inline-block" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="13" r="7" fill="#EF4444" stroke="#DC2626"/><path d="M12 6V3" stroke="#10B981" stroke-width="2.5"/><path d="M9 6c1.5-1 4.5-1 6 0" stroke="#10B981" stroke-width="2"/></svg>',

            'Onion' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 inline-block" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3C8 8 7 14 12 21C17 14 16 8 12 3Z" fill="#D946EF" stroke="#A21CAF"/><path d="M12 3V21" stroke="#F472B6" stroke-width="1"/><path d="M9.5 7C8 11 8 16 10.5 19.5" stroke="#F472B6" stroke-width="1"/><path d="M14.5 7C16 11 16 16 13.5 19.5" stroke="#F472B6" stroke-width="1"/></svg>',

            'Strawberry' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 inline-block" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21C12 21 5 15 5 10C5 6.5 8 4 12 4C16 4 19 6.5 19 10C19 15 12 21 12 21Z" fill="#F43F5E" stroke="#E11D48"/><path d="M9 4C10 5 11 6 12 6C13 6 14 5 15 4" stroke="#10B981" stroke-width="2.5"/><circle cx="9" cy="9" r="0.7" fill="#FBBF24"/><circle cx="15" cy="10" r="0.7" fill="#FBBF24"/><circle cx="12" cy="13" r="0.7" fill="#FBBF24"/><circle cx="9" cy="14" r="0.7" fill="#FBBF24"/><circle cx="14" cy="15" r="0.7" fill="#FBBF24"/></svg>',

            'Orange' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 inline-block" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="13" r="8" fill="#FB923C" stroke="#EA580C"/><path d="M12 5C13 3.5 15 3 16 3" stroke="#15803D" stroke-width="2"/><circle cx="11" cy="10" r="0.5" fill="#EA580C"/><circle cx="15" cy="12" r="0.5" fill="#EA580C"/><circle cx="10" cy="15" r="0.5" fill="#EA580C"/></svg>',

            'Corn' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 inline-block" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3C10 6 10 14 12 20C14 14 14 6 12 3Z" fill="#FACC15" stroke="#EAB308"/><path d="M9 7C8.5 12 10 17 12 21" stroke="#22C55E" stroke-width="1.5"/><path d="M15 7C15.5 12 14 17 12 21" stroke="#22C55E" stroke-width="1.5"/><path d="M12 6H12.01M12 9H12.01M12 12H12.01M12 15H12.01" stroke="#CA8A04" stroke-width="2.5" stroke-linecap="round"/></svg>'
        ];

        foreach ($svgs as $name => $svgString) {
            DB::table('lookup_children')
                ->where('name', $name)
                ->update([
                    'link' => $svgString // Updates ONLY the link column
                ]);
        }
    }
}
