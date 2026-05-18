<?php

namespace Database\Factories;

use App\Models\Worker;
use App\Models\WorkerRating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkerRating>
 */
class WorkerRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'worker_id' => Worker::factory(),
            'total_rating' => $this->faker->randomFloat(2, 5, 10),
            'works_num' => $this->faker->numberBetween(1, 20),
            'total_hours' => $this->faker->randomFloat(2, 20, 500),
        ];
    }
}
