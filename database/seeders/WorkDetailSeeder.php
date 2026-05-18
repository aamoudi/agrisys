<?php

namespace Database\Seeders;

use App\Models\WorkDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkDetail::factory()->count(200)->create();
    }
}
