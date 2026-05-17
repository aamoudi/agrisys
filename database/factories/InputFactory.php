<?php

namespace Database\Factories;

use App\Models\Crop;
use App\Models\Input;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Input>
 */
class InputFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crop_id' => Crop::all()->random()->id,
            'quantity' => $this->faker->randomFloat(2, 1, 1000000),
            'unit' => $this->faker->randomElement(['kg', 'ton', 'L']),
            'cost' => $this->faker->randomFloat(2, 3, 1000000),
            'applied_at' => $this->faker->date(),

            'type_cd' => function () {
                return DB::table('lookup_children')
                    ->whereIn('lookup_parent_id', function ($query) {
                        $query->select('id')->from('lookup_parents')
                            ->where('name', 'Input Type');
                    })
                    ->inRandomOrder()
                    ->value('id');
            },
        ];
    }
}
