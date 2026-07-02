<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use App\Models\Vinculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitacaoController extends Controller
{
    private function vanDoMotorista()
    {
        return auth()->user()->motorista?->van;
    }

    public function aceitar(int $id): RedirectResponse
    {
        $van = $this->vanDoMotorista();

        if (!$van) {
            return back()->withErrors(['geral' => 'Van não encontrada.']);
        }

        $solicitacao = Solicitacao::where('id_van', $van->id_van)
            ->where('status', 'pendente')
            ->with('disponibilidades')
            ->findOrFail($id);

        DB::transaction(function () use ($solicitacao, $van) {
            $solicitacao->update([
                'status'        => 'aceita',
                'data_resposta' => now(),
            ]);

            $preco_total = $solicitacao->disponibilidades->sum(
                fn ($d) => (float) $d->pivot->preco_mensal
            );

            $vinculo = Vinculo::create([
                'id_van'        => $van->id_van,
                'id_passageiro' => $solicitacao->id_passageiro,
                'id_solicitacao'=> $solicitacao->id_solicitacao,
                'preco_total'   => $preco_total,
                'status'        => 'ativo',
                'data_inicio'   => now()->toDateString(),
            ]);

            foreach ($solicitacao->disponibilidades as $disp) {
                $vinculo->disponibilidades()->attach($disp->id_disponibilidade, [
                    'preco_mensal'     => $disp->pivot->preco_mensal,
                    'dias_contratados' => $disp->pivot->dias_contratados, // JSON string do DB
                ]);
            }
        });

        return back()->with('sucesso', 'Solicitação aceita! Vínculo criado com sucesso.');
    }

    public function recusar(Request $request, int $id): RedirectResponse
    {
        $van = $this->vanDoMotorista();

        if (!$van) {
            return back()->withErrors(['geral' => 'Van não encontrada.']);
        }

        $validated = $request->validate([
            'motivo_recusa' => 'nullable|string|max:500',
        ]);

        $solicitacao = Solicitacao::where('id_van', $van->id_van)
            ->where('status', 'pendente')
            ->findOrFail($id);

        $solicitacao->update([
            'status'        => 'recusada',
            'data_resposta' => now(),
            'motivo_recusa' => $validated['motivo_recusa'] ?? null,
        ]);

        return back()->with('sucesso', 'Solicitação recusada.');
    }
}
