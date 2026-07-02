<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\Van;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class VanController extends Controller
{
    public function create(): Response
    {
        $motorista = auth()->user()->motorista;

        if ($motorista && $motorista->van) {
            return redirect()->route('motorista.dashboard')
                ->with('aviso', 'Você já possui uma van cadastrada.');
        }

        return Inertia::render('Motorista/Van/Criar');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'placa'                 => ['required', 'string', 'max:10', 'unique:van,placa', 'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/'],
            'marca'                 => ['required', 'string', 'max:100'],
            'modelo'                => ['required', 'string', 'max:100'],
            'ano_fabricacao'        => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'cor'                   => ['required', 'string', 'max:50'],
            'capacidade_passageiros'=> ['required', 'integer', 'min:1', 'max:30'],
        ], [
            'placa.required'                  => 'A placa é obrigatória.',
            'placa.unique'                    => 'Esta placa já está cadastrada.',
            'placa.regex'                     => 'Placa inválida. Use o formato ABC1234 ou ABC1D23.',
            'marca.required'                  => 'A marca é obrigatória.',
            'modelo.required'                 => 'O modelo é obrigatório.',
            'ano_fabricacao.required'         => 'O ano de fabricação é obrigatório.',
            'ano_fabricacao.min'              => 'Ano de fabricação inválido (mínimo 1990).',
            'ano_fabricacao.max'              => 'Ano de fabricação inválido.',
            'cor.required'                    => 'A cor é obrigatória.',
            'capacidade_passageiros.required' => 'A capacidade é obrigatória.',
            'capacidade_passageiros.min'      => 'Capacidade mínima: 1 passageiro.',
            'capacidade_passageiros.max'      => 'Capacidade máxima: 30 passageiros.',
        ]);

        $motorista = auth()->user()->motorista;

        if (!$motorista) {
            return back()->withErrors(['geral' => 'Perfil de motorista não encontrado.']);
        }

        if ($motorista->van) {
            return redirect()->route('motorista.dashboard')
                ->with('aviso', 'Você já possui uma van cadastrada.');
        }

        DB::transaction(function () use ($dados, $motorista) {
            Van::create([
                'id_motorista'          => $motorista->id_motorista,
                'placa'                 => strtoupper($dados['placa']),
                'marca'                 => $dados['marca'],
                'modelo'                => $dados['modelo'],
                'ano_fabricacao'        => $dados['ano_fabricacao'],
                'cor'                   => $dados['cor'],
                'capacidade_passageiros'=> $dados['capacidade_passageiros'],
                'status_aprovacao'      => 'pendente',
                'status_operacional'    => 'ativa',
                'documentacao_completa' => false,
            ]);
        });

        return redirect()->route('motorista.dashboard')
            ->with('sucesso', 'Van cadastrada com sucesso! Aguarde a aprovação do administrador.');
    }
}
