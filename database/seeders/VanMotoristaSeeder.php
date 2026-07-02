<?php

namespace Database\Seeders;

use App\Models\Disponibilidade;
use App\Models\Motorista;
use App\Models\Van;
use Illuminate\Database\Seeder;

class VanMotoristaSeeder extends Seeder
{
    public function run(): void
    {
        $motorista = Motorista::find(1);

        if (!$motorista) {
            $this->command->error('Motorista id=1 não encontrado.');
            return;
        }

        if ($motorista->van) {
            $this->command->info('Van já existe para João Motorista — seeder ignorado.');
            return;
        }

        $van = Van::create([
            'id_motorista'           => $motorista->id_motorista,
            'placa'                  => 'ABC1D23',
            'marca'                  => 'Mercedes-Benz',
            'modelo'                 => 'Sprinter 415',
            'ano_fabricacao'         => 2021,
            'cor'                    => 'Branco',
            'capacidade_passageiros' => 15,
            'status_aprovacao'       => 'aprovado',
            'status_operacional'     => 'ativa',
            'documentacao_completa'  => false,
        ]);

        // Disponibilidade manhã
        $manha = Disponibilidade::create([
            'id_van'            => $van->id_van,
            'nome'              => 'Manhã — Igara/Olaria',
            'turno'             => 'manha',
            'preco_mensal'      => 350.00,
            'capacidade_total'  => 12,
            'ativa'             => true,
            'bairros_atendidos' => ['Igara', 'Olaria', 'NSG'],
            'escolas_atendidas' => ['E.M. Nossa Senhora das Graças', 'EMEF Presidente Vargas'],
        ]);
        $manha->dias()->createMany([
            ['dia_semana' => 'seg'], ['dia_semana' => 'ter'], ['dia_semana' => 'qua'],
            ['dia_semana' => 'qui'], ['dia_semana' => 'sex'],
        ]);

        // Disponibilidade tarde
        $tarde = Disponibilidade::create([
            'id_van'            => $van->id_van,
            'nome'              => 'Tarde — Igara/Olaria',
            'turno'             => 'tarde',
            'preco_mensal'      => 350.00,
            'capacidade_total'  => 12,
            'ativa'             => true,
            'bairros_atendidos' => ['Igara', 'Olaria', 'NSG'],
            'escolas_atendidas' => ['E.M. Nossa Senhora das Graças', 'EMEF Presidente Vargas'],
        ]);
        $tarde->dias()->createMany([
            ['dia_semana' => 'seg'], ['dia_semana' => 'ter'], ['dia_semana' => 'qua'],
            ['dia_semana' => 'qui'], ['dia_semana' => 'sex'],
        ]);

        $this->command->info("Van {$van->placa} criada com 2 disponibilidades para João Motorista.");
    }
}
