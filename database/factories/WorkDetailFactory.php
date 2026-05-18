<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\WorkDetail;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkDetail>
 */
class WorkDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fromDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $toDate = (clone $fromDate)->modify('+' . rand(1, 14) . ' days');

        return [
            'farm_id' => Farm::all()->random()->id,
            'worker_id' => Worker::all()->random()->id,
            'salary' => $this->faker->randomFloat(2, 150, 2500),
            'from_date' => $fromDate->format('Y-m-d'),
            'to_date' => $toDate->format('Y-m-d'),
            'rate_num' => $this->faker->numberBetween(1, 10),
            'work_hours' => $this->faker->randomFloat(2, 10, 120),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
