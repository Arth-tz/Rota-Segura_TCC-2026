<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Pessoa;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (Usuario::where('role', UserRole::Admin)->exists()) {
            $this->command->info('Admin já existe — seeder ignorado.');
            return;
        }

        $pessoa = Pessoa::create([
            'nome'            => 'Administrador',
            'cpf'             => '00000000000',
            'data_nascimento' => '1990-01-01',
            'ativo'           => true,
        ]);

        Usuario::create([
            'id_pessoa'  => $pessoa->id_pessoa,
            'email'      => 'admin@rotasegura.com',
            'senha_hash' => Hash::make('admin1234'),
            'role'       => UserRole::Admin,
            'ativo'      => true,
        ]);

        $this->command->info('Admin criado: admin@rotasegura.com / admin1234');
    }
}
