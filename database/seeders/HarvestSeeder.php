<?php

namespace Database\Seeders;

use App\Models\Harvest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HarvestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Harvest::factory()->count(150)->create();
    }
}
