<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Farm>
 */
class FarmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Farm',
            'user_id' => User::all()->random()->id,
            'city_id' => City::all()->random()->id,

            // Gitting the random soil_type value from lookup children table regarding to look up parents
            // Its like select name from lookup_children lc where parent_id in(select id from lookup_parents where name = 'Soil Type')
            // Your SQL subquery translated directly to Eloquent/Query Builder
            'soil_type_cd' => function () {
                return DB::table('lookup_children')
                    ->whereIn('lookup_parent_id', function ($query) {
                        $query->select('id')
                            ->from('lookup_parents')
                            ->where('name', 'Soil Type'); // Matches your parent lookup name
                    })
                    ->inRandomOrder()
                    ->value('id'); // 'value()' directly grabs just the ID column value
            },
            'size_hectares' => $this->faker->randomFloat(2, 5.00, 500.00),
        ];
    }
}
