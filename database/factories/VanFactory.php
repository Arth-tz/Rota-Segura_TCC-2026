<?php

namespace Database\Factories;

use App\Models\Motorista;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Van>
 */
class VanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marcas = [
            'Mercedes-Benz' => ['Sprinter', 'Vito'],
            'Renault'       => ['Master', 'Trafic'],
            'Fiat'          => ['Ducato'],
            'Volkswagen'    => ['Crafter', 'Transporter'],
            'Peugeot'       => ['Boxer'],
        ];

        $marca = fake()->randomElement(array_keys($marcas));
        $modelo = fake()->randomElement($marcas[$marca]);

        return [
             'motorista_id'   => Motorista::factory()->aprovado(),
            'placa'          => strtoupper(fake()->bothify('???####')), // formato antigo
            'modelo'         => $modelo,
            'marca'          => $marca,
            'ano_fabricacao' => fake()->numberBetween(2015, 2024),
            'cor'            => fake()->randomElement(['Branca', 'Prata', 'Amarela', 'Azul']),
            'capacidade'     => fake()->randomElement([10, 12, 15, 16]),
            'crlv_num'       => fake()->numerify('##########'),
            'crlv_val'       => fake()->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
            'seguro_num'     => fake()->numerify('############'),
            'seguro_val'     => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'ult_rev'        => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'prox_rev'       => fake()->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'foto_van'       => null,
            'disponivel'     => true,
        ];
    }

    public function indisponivel(): static{
        return $this->state(fn () => ['disponivel' => false]);
    }

}
