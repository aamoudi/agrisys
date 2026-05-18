<?php

namespace Database\Factories;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Worker>
 */
class WorkerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'job_cd' => function () {
                return DB::table('lookup_children')
                    ->whereIn('lookup_parent_id', function ($query) {
                        $query->select('id')
                            ->from('lookup_parents')
                            ->where('name', 'Job'); // Matches your parent lookup name
                    })
                    ->inRandomOrder()
                    ->value('id');
            }
        ];
    }
}
