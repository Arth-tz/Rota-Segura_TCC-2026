<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Motorista;
use App\Models\Responsavel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aluno>
 */
class AlunoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'responsavel_id'      => Responsavel::factory(),
            'motorista_id'        => null, // null por padrão, aluno sem motorista ainda
            'first_name'          => fake()->firstName(),
            'last_name'           => fake()->lastName(),
            'data_nascimento'     => fake()->dateTimeBetween('-14 years', '-5 years')->format('Y-m-d'),
            'escola_nome'         => fake()->randomElement([
                                        'IFRS - Canoas',
                                        'Colégio Estadual Marechal Rondon',
                                        'EMEF Santos Dumont',
                                    ]),
            'escola_endereco'     => fake()->streetAddress(),
            'turno'               => fake()->randomElement(['manha', 'tarde', 'noite']),
            'serie_ano'           => fake()->randomElement([
                                        '1º Ano', '2º Ano', '3º Ano',
                                        '4º Ano', '5º Ano', '6º Ano',
                                        '7º Ano', '8º Ano', '9º Ano',
                                    ]),
            'endereco_embarque'   => fake()->streetAddress(),
            'endereco_desembarque'=> fake()->streetAddress(),
            'obs_medica'          => fake()->optional(0.2)->sentence(), // 20% de chance de ter
            'ctt_emergencia'      => fake()->name(),
            'tel_emergencia'      => fake()->phoneNumber(),
            'foto'                => null,
            'ativo'               => true,
        ];

        }
        public function comMotorista(int $motoristaId): static{
            return $this->state(fn () => ['motorista_id' => $motoristaId,]);
        }

        public function inativo(): static{
            return $this->state(fn () => ['ativo' => false]);
        }
}
