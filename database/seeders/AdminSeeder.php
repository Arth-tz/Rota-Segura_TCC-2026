<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Pessoa;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $novoHash = Hash::make('admin1234');

        // Primeiro tenta pelo email esperado
        $atualizado = DB::table('usuario')
            ->where('email', 'admin@rotasegura.com')
            ->update(['senha_hash' => $novoHash]);

        if ($atualizado > 0) {
            $this->command->info('Admin (admin@rotasegura.com) — senha redefinida.');
            return;
        }

        // Se não existe pelo email, busca qualquer admin pela role e atualiza email+senha
        $atualizado = DB::table('usuario')
            ->where('role', 'admin')
            ->update([
                'email'      => 'admin@rotasegura.com',
                'senha_hash' => $novoHash,
            ]);

        if ($atualizado > 0) {
            $this->command->info('Admin existente — email e senha atualizados para admin@rotasegura.com / admin1234.');
            return;
        }

        $pessoa = Pessoa::create([
            'nome'            => 'Administrador',
            'cpf'             => '00000000000',
            'data_nascimento' => '1990-01-01',
            'ativo'           => true,
        ]);

        DB::table('usuario')->insert([
            'id_pessoa'  => $pessoa->id_pessoa,
            'email'      => 'admin@rotasegura.com',
            'senha_hash' => $novoHash,
            'role'       => 'admin',
            'ativo'      => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Admin criado: admin@rotasegura.com / admin1234');
    }
}
