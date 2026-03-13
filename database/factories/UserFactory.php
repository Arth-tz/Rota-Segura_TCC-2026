<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'email'      => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'   => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone'      => fake()->phoneNumber(),
            'role'       => fake()->randomElement([UserRole::Motorista, UserRole::Responsavel]),
            'cpf'        => fake()->cpf(false),

        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Admin,
        ]);
    }

    public function responsavel(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Responsavel,
        ]);
    }

    public function motorista(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Motorista,
        ]);
    }


    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
