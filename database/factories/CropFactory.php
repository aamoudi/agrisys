<?php

namespace Database\Factories;

use App\Models\Crop;
use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Crop>
 */
class CropFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'farm_id' => Farm::all()->random()->id,
            'type' => $this->faker->randomElement(['annual', 'perennial']),
            'planting_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['growing', 'harvested', 'fallow', 'failed']),


            // 1. Explicitly define variety_cd so Laravel registers the database column
            'variety_cd' => function () {
                return DB::table('lookup_children')
                    ->whereIn('lookup_parent_id', function ($query) {
                        $query->select('id')->from('lookup_parents')
                            ->where('name', 'Variety');
                    })
                    ->inRandomOrder()
                    ->value('id') ?? 6; // Your fallback is safely handled here!
            },

            // 2. The name field simply looks at what variety_cd chose and pulls the text string
            'name' => function (array $attributes) {
                $varietyName = DB::table('lookup_children')
                    ->where('id', $attributes['variety_cd'])
                    ->value('name');

                return ($varietyName ?? 'Onion') . ' Crop';
            },



            /* 'variety_cd' => function () {
                return DB::table('lookup_children')
                    ->whereIn('lookup_parent_id', function ($query) {
                        $query->select('id')->from('lookup_parents')
                            ->where('name', 'Variety');
                    })
                    ->inRandomOrder()
                    ->value('name');
            }, */

        ];
    }
}
