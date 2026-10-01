<?php

namespace Database\Seeders;

use App\Models\Disponibilidade;
use App\Models\DisponibilidadeDia;
use App\Models\Motorista;
use App\Models\Pessoa;
use App\Models\Usuario;
use App\Models\Van;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestMotoristaSeeder extends Seeder
{
    const EMAIL = 'motorista@teste.rotasegura';
    const SENHA = 'Teste@2026';

    public function run(): void
    {
        if (Usuario::where('email', self::EMAIL)->exists()) {
            $this->command->info('Motorista de teste já existe. Pulando.');
            return;
        }

        $pessoa = Pessoa::create([
            'nome'            => 'Motorista Teste',
            'cpf'             => '99988877766',
            'data_nascimento' => '1990-01-01',
            'telefone'        => '(51) 99900-0000',
            'ativo'           => true,
        ]);

        $usuario = Usuario::create([
            'id_pessoa'  => $pessoa->id_pessoa,
            'email'      => self::EMAIL,
            'senha_hash' => Hash::make(self::SENHA),
            'role'       => 'motorista',
            'ativo'      => true,
        ]);

        $motorista = Motorista::create([
            'id_usuario'               => $usuario->id_usuario,
            'cnh_numero'               => 'CNH-99999999',
            'cnh_categoria'            => 'D',
            'cnh_validade'             => '2030-12-31',
            'status_aprovacao'         => 'aprovado',
            'data_avaliacao_documento' => now(),
        ]);

        $van = Van::create([
            'id_motorista'           => $motorista->id_motorista,
            'nome_servico'           => 'Van Teste Rota Segura',
            'placa'                  => 'TST0T00',
            'modelo'                 => 'Sprinter',
            'marca'                  => 'Mercedes-Benz',
            'ano_fabricacao'         => 2022,
            'cor'                    => 'Branca',
            'capacidade_passageiros' => 15,
            'foto_url'               => 'https://placehold.co/600x400/1e3a8a/ffffff?text=Van+Teste',
            'status_aprovacao'       => 'aprovado',
            'data_avaliacao'         => now(),
        ]);

        $disponibilidade = Disponibilidade::create([
            'id_van'            => $van->id_van,
            'nome'              => 'Manha - Canoas',
            'turno'             => 'manha',
            'preco_mensal'      => 300.00,
            'capacidade_total'  => 10,
            'regioes_atendidas' => [['cidade' => 'Canoas', 'bairros' => ['Centro', 'Niteroi', 'Mathias Velho']]],
            'escolas_atendidas' => ['EMEF Joao XXIII', 'Colegio Marista Canoas'],
            'ativa'             => true,
        ]);

        foreach (['seg', 'ter', 'qua', 'qui', 'sex'] as $dia) {
            DisponibilidadeDia::create([
                'id_disponibilidade' => $disponibilidade->id_disponibilidade,
                'dia_semana'         => $dia,
            ]);
        }

        $this->command->line('');
        $this->command->info('========================================');
        $this->command->info('  Motorista de teste criado com sucesso!');
        $this->command->info('  Email: ' . self::EMAIL);
        $this->command->info('  Senha: ' . self::SENHA);
        $this->command->info('========================================');
        $this->command->line('');
    }
}
