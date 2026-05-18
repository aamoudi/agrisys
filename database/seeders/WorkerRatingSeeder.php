<?php

namespace Database\Seeders;

use App\Models\WorkerRating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkerRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkerRating::factory()->count(100)->create();
    }
}
