<?php

namespace Database\Seeders;

use App\Models\Disponibilidade;
use App\Models\DisponibilidadeDia;
use App\Models\Endereco;
use App\Models\Motorista;
use App\Models\Passageiro;
use App\Models\PassageiroEndereco;
use App\Models\Pessoa;
use App\Models\Responsavel;
use App\Models\ResponsavelPassageiro;
use App\Models\Usuario;
use App\Models\Van;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->criarMotoristas();
        $this->criarResponsaveis();
    }

    private function criarMotoristas(): void
    {
        $motoristas = [
            [
                'email'          => 'ana.motorista@teste.rotasegura',
                'nome'           => 'Ana Paula Silveira',
                'cpf'            => '11122233344',
                'telefone'       => '(51) 99111-2233',
                'data_nascimento'=> '1988-04-22',
                'cnh_numero'    => 'CNH-10001111',
                'cnh_categoria' => 'D',
                'cnh_validade'  => '2029-06-15',
                'van' => [
                    'nome_servico'           => 'Van da Ana - Canoas',
                    'placa'                  => 'TES1A11',
                    'modelo'                 => 'Sprinter',
                    'marca'                  => 'Mercedes-Benz',
                    'ano_fabricacao'         => 2021,
                    'cor'                    => 'Branca',
                    'capacidade_passageiros' => 15,
                    'foto_url'               => 'https://placehold.co/600x400/1e3a8a/ffffff?text=Van+da+Ana',
                ],
                'disponibilidades' => [
                    [
                        'nome'              => 'Manha - Canoas Centro',
                        'turno'             => 'manha',
                        'preco_mensal'      => 320.00,
                        'capacidade_total'  => 10,
                        'regioes_atendidas' => [['cidade' => 'Canoas', 'bairros' => ['Centro', 'Niteroi', 'Mathias Velho']]],
                        'escolas_atendidas' => ['EMEF Joao XXIII', 'Colegio Marista Canoas'],
                        'dias'              => ['seg', 'ter', 'qua', 'qui', 'sex'],
                    ],
                    [
                        'nome'              => 'Tarde - Canoas Centro',
                        'turno'             => 'tarde',
                        'preco_mensal'      => 300.00,
                        'capacidade_total'  => 10,
                        'regioes_atendidas' => [['cidade' => 'Canoas', 'bairros' => ['Centro', 'Niteroi']]],
                        'escolas_atendidas' => ['EMEF Joao XXIII'],
                        'dias'              => ['seg', 'ter', 'qua', 'qui', 'sex'],
                    ],
                ],
            ],
            [
                'email'          => 'carlos.motorista@teste.rotasegura',
                'nome'           => 'Carlos Eduardo Matos',
                'cpf'            => '22233344455',
                'telefone'       => '(51) 99222-3344',
                'data_nascimento'=> '1985-11-07',
                'cnh_numero'    => 'CNH-20002222',
                'cnh_categoria' => 'D',
                'cnh_validade'  => '2028-03-20',
                'van' => [
                    'nome_servico'           => 'TransCanoas - Carlos',
                    'placa'                  => 'TES2B22',
                    'modelo'                 => 'Master',
                    'marca'                  => 'Renault',
                    'ano_fabricacao'         => 2020,
                    'cor'                    => 'Prata',
                    'capacidade_passageiros' => 12,
                    'foto_url'               => 'https://placehold.co/600x400/065f46/ffffff?text=TransCanoas',
                ],
                'disponibilidades' => [
                    [
                        'nome'              => 'Manha - Mathias Velho',
                        'turno'             => 'manha',
                        'preco_mensal'      => null,
                        'capacidade_total'  => 12,
                        'regioes_atendidas' => [['cidade' => 'Canoas', 'bairros' => ['Mathias Velho', 'Rio Branco', 'Igara']]],
                        'escolas_atendidas' => ['EMEF Getulio Vargas', 'Escola Estadual Olavo Bilac'],
                        'dias'              => ['seg', 'ter', 'qua', 'qui', 'sex'],
                    ],
                    [
                        'nome'              => 'Integral - Mathias Velho',
                        'turno'             => 'integral',
                        'preco_mensal'      => 550.00,
                        'capacidade_total'  => 8,
                        'regioes_atendidas' => [['cidade' => 'Canoas', 'bairros' => ['Mathias Velho', 'Rio Branco']]],
                        'escolas_atendidas' => ['EMEF Getulio Vargas'],
                        'dias'              => ['seg', 'ter', 'qua', 'qui', 'sex'],
                    ],
                ],
            ],
            [
                'email'          => 'rodrigo.motorista@teste.rotasegura',
                'nome'           => 'Rodrigo Ferreira Lima',
                'cpf'            => '33344455566',
                'telefone'       => '(51) 99333-4455',
                'data_nascimento'=> '1991-03-15',
                'cnh_numero'    => 'CNH-30003333',
                'cnh_categoria' => 'D',
                'cnh_validade'  => '2030-11-01',
                'van' => [
                    'nome_servico'           => 'Van Segura RS',
                    'placa'                  => 'TES3C33',
                    'modelo'                 => 'Ducato',
                    'marca'                  => 'Fiat',
                    'ano_fabricacao'         => 2022,
                    'cor'                    => 'Amarela',
                    'capacidade_passageiros' => 14,
                    'foto_url'               => 'https://placehold.co/600x400/78350f/ffffff?text=Van+Segura+RS',
                ],
                'disponibilidades' => [
                    [
                        'nome'              => 'Manha - Harmonia / Sao Luis',
                        'turno'             => 'manha',
                        'preco_mensal'      => 280.00,
                        'capacidade_total'  => 14,
                        'regioes_atendidas' => [['cidade' => 'Canoas', 'bairros' => ['Harmonia', 'Sao Luis', 'Estancia Velha']]],
                        'escolas_atendidas' => ['Colegio Adventista de Canoas', 'EMEF Tiradentes'],
                        'dias'              => ['seg', 'ter', 'qua', 'qui', 'sex', 'sab'],
                    ],
                    [
                        'nome'              => 'Tarde - Harmonia',
                        'turno'             => 'tarde',
                        'preco_mensal'      => 260.00,
                        'capacidade_total'  => 14,
                        'regioes_atendidas' => [['cidade' => 'Canoas', 'bairros' => ['Harmonia', 'Sao Luis']]],
                        'escolas_atendidas' => ['Colegio Adventista de Canoas'],
                        'dias'              => ['seg', 'ter', 'qua', 'qui', 'sex'],
                    ],
                ],
            ],
        ];

        foreach ($motoristas as $dados) {
            $usuario = $this->criarUsuario($dados['email'], $dados['nome'], $dados['cpf'], $dados['telefone'], 'motorista', $dados['data_nascimento']);

            if (!$usuario->motorista) {
                $motorista = Motorista::create([
                    'id_usuario'               => $usuario->id_usuario,
                    'cnh_numero'               => $dados['cnh_numero'],
                    'cnh_categoria'            => $dados['cnh_categoria'],
                    'cnh_validade'             => $dados['cnh_validade'],
                    'status_aprovacao'         => 'aprovado',
                    'data_avaliacao_documento' => now(),
                ]);

                $van = Van::create(array_merge($dados['van'], [
                    'id_motorista'       => $motorista->id_motorista,
                    'status_aprovacao'   => 'aprovado',
                    'status_operacional' => 'ativa',
                    'data_avaliacao'     => now(),
                ]));

                foreach ($dados['disponibilidades'] as $disp) {
                    $dias = $disp['dias'];
                    unset($disp['dias']);

                    $disponibilidade = Disponibilidade::create(array_merge($disp, [
                        'id_van' => $van->id_van,
                        'ativa'  => true,
                    ]));

                    foreach ($dias as $dia) {
                        DisponibilidadeDia::create([
                            'id_disponibilidade' => $disponibilidade->id_disponibilidade,
                            'dia_semana'         => $dia,
                        ]);
                    }
                }
            }

            $this->command->info("Motorista: {$dados['nome']} ({$dados['email']}) -- OK");
        }
    }

    private function criarResponsaveis(): void
    {
        $responsaveis = [
            [
                'email'            => 'marcos.responsavel@teste.rotasegura',
                'nome'             => 'Marcos Oliveira',
                'cpf'              => '44455566677',
                'telefone'         => '(51) 98444-5566',
                'data_nascimento'  => '1984-08-19',
                'tipo_responsavel' => 'pai',
                'passageiros' => [
                    [
                        'nome'            => 'Lucas Oliveira',
                        'cpf'             => '77788899901',
                        'data_nascimento' => '2017-03-10',
                        'embarque'  => ['logradouro' => 'Rua das Flores', 'numero' => '42', 'bairro' => 'Centro', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92010000', 'latitude' => -29.9175, 'longitude' => -51.1834],
                        'desembarque' => ['logradouro' => 'Av. Guilherme Schell', 'numero' => '5800', 'bairro' => 'Centro', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92010080', 'latitude' => -29.9140, 'longitude' => -51.1820, 'nome' => 'EMEF Joao XXIII'],
                    ],
                    [
                        'nome'            => 'Sofia Oliveira',
                        'cpf'             => '77788899902',
                        'data_nascimento' => '2019-07-22',
                        'embarque'  => ['logradouro' => 'Rua das Flores', 'numero' => '42', 'bairro' => 'Centro', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92010000', 'latitude' => -29.9175, 'longitude' => -51.1834],
                        'desembarque' => ['logradouro' => 'Rua Coronel Niederauer', 'numero' => '300', 'bairro' => 'Niteroi', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92120000', 'latitude' => -29.9250, 'longitude' => -51.1900, 'nome' => 'Colegio Marista Canoas'],
                    ],
                ],
            ],
            [
                'email'            => 'fernanda.responsavel@teste.rotasegura',
                'nome'             => 'Fernanda Costa',
                'cpf'              => '55566677788',
                'telefone'         => '(51) 98555-6677',
                'data_nascimento'  => '1990-02-28',
                'tipo_responsavel' => 'mae',
                'passageiros' => [
                    [
                        'nome'            => 'Pedro Costa',
                        'cpf'             => '77788899903',
                        'data_nascimento' => '2016-11-05',
                        'embarque'    => ['logradouro' => 'Rua Tiradentes', 'numero' => '180', 'bairro' => 'Mathias Velho', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92240000', 'latitude' => -29.9380, 'longitude' => -51.1950],
                        'desembarque' => ['logradouro' => 'Av. Victor Barreto', 'numero' => '2288', 'bairro' => 'Mathias Velho', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92240001', 'latitude' => -29.9360, 'longitude' => -51.1930, 'nome' => 'EMEF Getulio Vargas'],
                    ],
                ],
            ],
            [
                'email'            => 'patricia.responsavel@teste.rotasegura',
                'nome'             => 'Patricia Ramos',
                'cpf'              => '66677788899',
                'telefone'         => '(51) 98666-7788',
                'data_nascimento'  => '1987-06-11',
                'tipo_responsavel' => 'mae',
                'passageiros' => [
                    [
                        'nome'            => 'Isabela Ramos',
                        'cpf'             => '77788899904',
                        'data_nascimento' => '2018-02-14',
                        'embarque'    => ['logradouro' => 'Rua Harmonia', 'numero' => '55', 'bairro' => 'Harmonia', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92410000', 'latitude' => -29.9050, 'longitude' => -51.2010],
                        'desembarque' => ['logradouro' => 'Av. Inconfidencia', 'numero' => '1200', 'bairro' => 'Harmonia', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92415000', 'latitude' => -29.9020, 'longitude' => -51.2000, 'nome' => 'Colegio Adventista de Canoas'],
                    ],
                    [
                        'nome'            => 'Miguel Ramos',
                        'cpf'             => '77788899905',
                        'data_nascimento' => '2015-09-30',
                        'embarque'    => ['logradouro' => 'Rua Harmonia', 'numero' => '55', 'bairro' => 'Harmonia', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92410000', 'latitude' => -29.9050, 'longitude' => -51.2010],
                        'desembarque' => ['logradouro' => 'Rua Tiradentes', 'numero' => '890', 'bairro' => 'Sao Luis', 'cidade' => 'Canoas', 'estado' => 'RS', 'cep' => '92420000', 'latitude' => -29.9080, 'longitude' => -51.1980, 'nome' => 'EMEF Tiradentes'],
                    ],
                ],
            ],
        ];

        foreach ($responsaveis as $dados) {
            $usuario = $this->criarUsuario($dados['email'], $dados['nome'], $dados['cpf'], $dados['telefone'], 'responsavel', $dados['data_nascimento']);

            if (!$usuario->responsavel) {
                $responsavel = Responsavel::create([
                    'id_usuario'       => $usuario->id_usuario,
                    'tipo_responsavel' => $dados['tipo_responsavel'],
                ]);

                foreach ($dados['passageiros'] as $p) {
                    $pessoa = Pessoa::create([
                        'nome'            => $p['nome'],
                        'cpf'             => $p['cpf'],
                        'data_nascimento' => $p['data_nascimento'],
                        'ativo'           => true,
                    ]);

                    $passageiro = Passageiro::create([
                        'id_pessoa'               => $pessoa->id_pessoa,
                        'id_usuario'              => null,
                        'foto_consentimento_lgpd' => false,
                        'ativo'                   => true,
                        'data_inscricao'          => now(),
                    ]);

                    ResponsavelPassageiro::create([
                        'id_responsavel' => $responsavel->id_responsavel,
                        'id_passageiro'  => $passageiro->id_passageiro,
                        'data_inicio'    => now(),
                    ]);

                    foreach (['embarque', 'desembarque'] as $tipo) {
                        $dadosEndereco = $p[$tipo];
                        $nomePonto = $dadosEndereco['nome'] ?? null;
                        unset($dadosEndereco['nome']);

                        $endereco = Endereco::create($dadosEndereco);

                        PassageiroEndereco::create([
                            'id_passageiro' => $passageiro->id_passageiro,
                            'id_endereco'   => $endereco->id_endereco,
                            'tipo'          => $tipo,
                            'principal'     => true,
                            'nome'          => $nomePonto,
                        ]);
                    }
                }
            }

            $this->command->info("Responsavel: {$dados['nome']} ({$dados['email']}) -- OK");
        }
    }

    private function criarUsuario(string $email, string $nome, string $cpf, string $telefone, string $role, string $dataNascimento = '1990-01-01'): Usuario
    {
        $existing = Usuario::where('email', $email)->first();
        if ($existing) {
            return $existing;
        }

        $pessoa = Pessoa::create([
            'nome'            => $nome,
            'cpf'             => $cpf,
            'data_nascimento' => $dataNascimento,
            'telefone'        => $telefone,
            'ativo'           => true,
        ]);

        return Usuario::create([
            'id_pessoa'  => $pessoa->id_pessoa,
            'email'      => $email,
            'senha_hash' => Hash::make('Teste@2026'),
            'role'       => $role,
            'ativo'      => true,
        ]);
    }
}
