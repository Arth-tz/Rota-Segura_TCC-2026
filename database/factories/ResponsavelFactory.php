<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Responsavel>
 */
class ResponsavelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $estados = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT',
                    'MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO',
                    'RR','SC','SP','SE','TO'];

        return [
            'user_id'     => User::factory()->state(['role' => UserRole::Responsavel]),
            'endereco'    => fake()->streetAddress(),
            'cidade'      => fake()->city(),
            'estado'      =>fake()->randomElement($estados),
            'complemento' => fake()->optional()->secondaryAddress(),
            'cep'         => fake()->numerify('########'),
        ]; 
    }
}
