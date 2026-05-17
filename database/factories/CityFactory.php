<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            // We Will Create The Cities From CitySeeder Directly <>

            //'name' => $this->faker->city(),
            //option 1. 'country_id' => Country::inRandomOrder()->first()?->id ?? Country::factory(),
            //option 2. 'country_id' => Country::all()->random()->id,

        ];
    }
}
