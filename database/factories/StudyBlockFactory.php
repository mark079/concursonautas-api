<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudyBlock>
 */
class StudyBlockFactory extends Factory
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
            'schedule_id' => Schedule::all()->random()->id,
            'content' => $this->faker->words(7, true),
            'date' => $this->faker->date(),
            'completed' => $this->faker->boolean()
        ];
    }
}
