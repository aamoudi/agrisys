<?php

namespace Database\Factories;

use App\Models\Crop;
use App\Models\Harvest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Harvest>
 */
class HarvestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Harvest ' . $this->faker->randomDigit(),
            'crop_id' => Crop::all()->random()->id,
            'quantitiy' => $this->faker->randomFloat(2, 1, 1000000),
            'unit' => $this->faker->randomElement(['kg', 'tons', 'piece']),
            'quality_grade' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'harvest_date' => $this->faker->date(),
            'revenue' => $this->faker->randomFloat(2, 1, 1000000),
        ];
    }
}
