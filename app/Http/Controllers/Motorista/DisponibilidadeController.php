<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\Disponibilidade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DisponibilidadeController extends Controller
{
    private function vanDoMotorista()
    {
        return auth()->user()->motorista?->van;
    }

    public function create(): Response|RedirectResponse
    {
        $van = $this->vanDoMotorista();

        if (!$van) {
            return redirect()->route('motorista.dashboard')
                ->withErrors(['geral' => 'Cadastre uma van antes de criar disponibilidades.']);
        }

        return Inertia::render('Motorista/Disponibilidade/Criar', [
            'id_van' => $van->id_van,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $van = $this->vanDoMotorista();

        if (!$van) {
            return back()->withErrors(['geral' => 'Van não encontrada.']);
        }

        $dados = $request->validate([
            'nome'              => 'required|string|max:100',
            'turno'             => 'required|in:manha,tarde,integral',
            'dias'              => 'required|array|min:1',
            'dias.*'            => 'in:seg,ter,qua,qui,sex,sab,dom',
            'preco_mensal'      => 'required|numeric|min:0|max:9999.99',
            'capacidade_total'  => 'required|integer|min:1|max:' . $van->capacidade_passageiros,
            'bairros_atendidos' => 'nullable|array',
            'bairros_atendidos.*' => 'string|max:100',
            'escolas_atendidas' => 'nullable|array',
            'escolas_atendidas.*' => 'string|max:150',
        ], [
            'dias.required'        => 'Selecione ao menos um dia da semana.',
            'capacidade_total.max' => "A capacidade não pode exceder a da van ({$van->capacidade_passageiros}).",
        ]);

        DB::transaction(function () use ($dados, $van) {
            $disp = Disponibilidade::create([
                'id_van'            => $van->id_van,
                'nome'              => $dados['nome'],
                'turno'             => $dados['turno'],
                'preco_mensal'      => $dados['preco_mensal'],
                'capacidade_total'  => $dados['capacidade_total'],
                'ativa'             => true,
                'bairros_atendidos' => $dados['bairros_atendidos'] ?? [],
                'escolas_atendidas' => $dados['escolas_atendidas'] ?? [],
            ]);

            foreach ($dados['dias'] as $dia) {
                $disp->dias()->create(['dia_semana' => $dia]);
            }
        });

        return redirect()->route('motorista.dashboard')
            ->with('sucesso', 'Disponibilidade criada com sucesso!');
    }

    public function edit(int $id): Response|RedirectResponse
    {
        $van  = $this->vanDoMotorista();
        $disp = $van?->disponibilidades()->with('dias')->find($id);

        if (!$disp) {
            return redirect()->route('motorista.dashboard')
                ->withErrors(['geral' => 'Disponibilidade não encontrada.']);
        }

        return Inertia::render('Motorista/Disponibilidade/Editar', [
            'disponibilidade' => [
                'id_disponibilidade' => $disp->id_disponibilidade,
                'nome'               => $disp->nome,
                'turno'              => $disp->turno,
                'preco_mensal'       => $disp->preco_mensal,
                'capacidade_total'   => $disp->capacidade_total,
                'ativa'              => $disp->ativa,
                'dias'               => $disp->dias->pluck('dia_semana')->values(),
                'bairros_atendidos'  => $disp->bairros_atendidos ?? [],
                'escolas_atendidas'  => $disp->escolas_atendidas ?? [],
            ],
            'capacidade_van' => $van->capacidade_passageiros,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $van  = $this->vanDoMotorista();
        $disp = $van?->disponibilidades()->find($id);

        if (!$disp) {
            return back()->withErrors(['geral' => 'Disponibilidade não encontrada.']);
        }

        $dados = $request->validate([
            'nome'                => 'required|string|max:100',
            'turno'               => 'required|in:manha,tarde,integral',
            'dias'                => 'required|array|min:1',
            'dias.*'              => 'in:seg,ter,qua,qui,sex,sab,dom',
            'preco_mensal'        => 'required|numeric|min:0|max:9999.99',
            'capacidade_total'    => 'required|integer|min:1|max:' . $van->capacidade_passageiros,
            'ativa'               => 'boolean',
            'bairros_atendidos'   => 'nullable|array',
            'bairros_atendidos.*' => 'string|max:100',
            'escolas_atendidas'   => 'nullable|array',
            'escolas_atendidas.*' => 'string|max:150',
        ]);

        DB::transaction(function () use ($dados, $disp) {
            $disp->update([
                'nome'              => $dados['nome'],
                'turno'             => $dados['turno'],
                'preco_mensal'      => $dados['preco_mensal'],
                'capacidade_total'  => $dados['capacidade_total'],
                'ativa'             => $dados['ativa'] ?? $disp->ativa,
                'bairros_atendidos' => $dados['bairros_atendidos'] ?? [],
                'escolas_atendidas' => $dados['escolas_atendidas'] ?? [],
            ]);

            $disp->dias()->delete();
            foreach ($dados['dias'] as $dia) {
                $disp->dias()->create(['dia_semana' => $dia]);
            }
        });

        return redirect()->route('motorista.dashboard')
            ->with('sucesso', 'Disponibilidade atualizada.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $van  = $this->vanDoMotorista();
        $disp = $van?->disponibilidades()->find($id);

        if (!$disp) {
            return back()->withErrors(['geral' => 'Disponibilidade não encontrada.']);
        }

        DB::transaction(fn() => $disp->dias()->delete() && $disp->delete());

        return redirect()->route('motorista.dashboard')
            ->with('sucesso', 'Disponibilidade removida.');
    }
}
