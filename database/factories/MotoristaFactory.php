<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Motorista>
 */
class MotoristaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => UserRole::Motorista]),
            'cnh_numero' => fake()->numerify('##########'),
            'cnh_categoria' => 'D',
            'cnh_validade'  => fake()->dateTimeBetween('+6 months', '+5 years')->format('Y-m-d'),
            'cnh_foto' => null,
            'crlv' => null,
            'ant_crim' => null,
            'status_aprov' => 'pendente',
            'data_aprov' => null,

        ];

        }
    public function aprovado(): static{
        return $this->state(fn () => [
            'status_aprov' => 'aprovado',
            'data_aprov' => now()
        ]);
    }
}
