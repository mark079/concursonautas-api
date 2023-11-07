<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'goal_id' => Goal::all()->random()->id,
            'weekday' => $this->faker->numberBetween(0, 6),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time()
        ];
    }
}
