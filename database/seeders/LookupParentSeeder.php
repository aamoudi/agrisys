<?php

namespace Database\Seeders;

use App\Models\Lookup_parent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = [
            ['name' => 'Soil Type'],
            ['name' => 'Variety'],
            ['name' => 'Input Type'],
            ['name' => 'Job'],
        ];

        // Using upsert or insert to seed them
        DB::table('lookup_parents')->insert($parents);
    }
}
