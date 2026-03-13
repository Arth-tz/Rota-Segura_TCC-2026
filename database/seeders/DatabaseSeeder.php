<?php

namespace Database\Seeders;

use App\Models\Motorista;
use App\Models\Responsavel;
use App\Models\Aluno;
use App\Models\Van;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //-- cria o Admin
        User::factory()->admin()->create([
            'first_name' => 'Admin',
            'last_name' => 'Sistema',
            'email' => 'admin@rotasegura.com',
            'cpf' => '00000000000',
        ]);

        //-- Cria 3 motoristas com suas vans
        $motoristas = Motorista::factory()->aprovado()->count(3)->create();
 
        //-- Vans dos três motoristas
        $motoristas->each(function($motorista){
            Van::factory()->create(['motorista_id' => $motorista->id,]);
        });

        //-- Cria 5 responsáveis com 2 alunos cada
        Responsavel::factory()
            ->count(5)
            ->create()
            ->each(function ($responsavel) use ($motoristas) {
            Aluno::factory()
                ->count(2)
                ->comMotorista($motoristas->random()->id)
                ->create(['responsavel_id' => $responsavel->id,]);
        });
    }
}
