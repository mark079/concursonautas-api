<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
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
            'type' => $this->faker->randomElement(['V','C']), // Vestibular ou Concurso
            'test_date' => $this->faker->dateTimeBetween('+2 months', '+6 months'),
            'content_to_study' => $this->faker->randomElement(['Conteudo tal', 'Conteudo fake', 'Teste conteúdo'])
        ];
    }
}
