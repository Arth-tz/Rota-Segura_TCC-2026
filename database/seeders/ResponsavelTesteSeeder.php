<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Passageiro;
use App\Models\Pessoa;
use App\Models\Responsavel;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResponsavelTesteSeeder extends Seeder
{
    public function run(): void
    {
        if (Usuario::where('email', 'maria@rotasegura.com')->exists()) {
            $this->command->info('Responsável de teste já existe — seeder ignorado.');
            return;
        }

        // Cria pessoa do responsável
        $pessoaResp = Pessoa::create([
            'nome'            => 'Maria Silva',
            'cpf'             => '12345678901',
            'data_nascimento' => '1985-03-20',
            'telefone'        => '51991234567',
            'ativo'           => true,
        ]);

        // Cria usuário do responsável
        $usuario = Usuario::create([
            'id_pessoa'  => $pessoaResp->id_pessoa,
            'email'      => 'maria@rotasegura.com',
            'senha_hash' => Hash::make('teste1234'),
            'role'       => UserRole::Responsavel,
            'ativo'      => true,
        ]);

        // Cria perfil responsável
        $responsavel = Responsavel::create([
            'id_usuario'         => $usuario->id_usuario,
            'tipo_responsavel'   => 'mae',
            'telefone_emergencia'=> '51981234567',
        ]);

        // Cria pessoa do passageiro
        $pessoaPass = Pessoa::create([
            'nome'            => 'Pedro Silva',
            'cpf'             => '98765432100',
            'data_nascimento' => '2017-06-10',
            'ativo'           => true,
        ]);

        // Cria passageiro
        $passageiro = Passageiro::create([
            'id_pessoa'     => $pessoaPass->id_pessoa,
            'ativo'         => true,
            'data_inscricao'=> now()->toDateString(),
        ]);

        // Vincula passageiro ao responsável
        $responsavel->passageiros()->attach($passageiro->id_passageiro, [
            'data_inicio' => now()->toDateString(),
        ]);

        $this->command->info('Responsável de teste criado:');
        $this->command->info('  E-mail: maria@rotasegura.com');
        $this->command->info('  Senha:  teste1234');
        $this->command->info('  Passageiro: Pedro Silva (filho, 8 anos)');
    }
}
