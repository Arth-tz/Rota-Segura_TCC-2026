<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\Disponibilidade;
use App\Models\Solicitacao;
use App\Models\Vinculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SolicitacaoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_disponibilidade' => 'required|integer|exists:disponibilidade,id_disponibilidade',
            'id_passageiro'      => 'required|integer',
            'dias_contratados'   => 'required|array|min:1',
            'dias_contratados.*' => 'in:seg,ter,qua,qui,sex,sab,dom',
            'mensagem'           => 'nullable|string|max:500',
        ], [
            'dias_contratados.required' => 'Selecione ao menos um dia da semana.',
            'dias_contratados.min'      => 'Selecione ao menos um dia da semana.',
        ]);

        $responsavel = auth()->user()->responsavel;

        // Garante que o passageiro pertence a este responsável
        $passageiro = $responsavel->passageiros()
            ->where('passageiro.id_passageiro', $validated['id_passageiro'])
            ->firstOrFail();

        // Passageiro deve estar ativo
        if (!$passageiro->ativo) {
            return back()->withErrors(['geral' => 'Este passageiro está inativo.']);
        }

        // Passageiro deve ter endereço de embarque e destino cadastrados
        $passageiro->load('enderecos');
        if ($passageiro->enderecos->where('tipo', 'embarque')->isEmpty()) {
            return back()->withErrors(['geral' => 'Cadastre um endereço de embarque para o passageiro antes de solicitar. O motorista precisa saber onde buscar.']);
        }
        if ($passageiro->enderecos->where('tipo', 'desembarque')->isEmpty()) {
            return back()->withErrors(['geral' => 'Cadastre um endereço de destino (escola) para o passageiro antes de solicitar. O motorista precisa saber onde deixar.']);
        }

        // Disponibilidade deve estar ativa e pertencer a van/motorista aprovados
        $disponibilidade = Disponibilidade::where('ativa', true)
            ->whereHas('van', fn ($q) => $q->where('status_aprovacao', 'aprovado')
                                           ->where('status_operacional', 'ativa'))
            ->whereHas('van.motorista', fn ($q) => $q->where('status_aprovacao', 'aprovado'))
            ->with('dias')
            ->findOrFail($validated['id_disponibilidade']);

        // Os dias solicitados devem ser subconjunto dos dias que a van atende
        $diasDisponiveis = $disponibilidade->dias->pluck('dia_semana')->toArray();
        $diasInvalidos   = array_diff($validated['dias_contratados'], $diasDisponiveis);

        if ($diasInvalidos) {
            return back()->withErrors(['dias_contratados' => 'Alguns dias selecionados não estão disponíveis nessa van.']);
        }

        // Verifica conflito: passageiro já tem vínculo ativo que cobre algum dos dias solicitados
        // no mesmo turno — usa JSON_OVERLAPS (MySQL 8+)
        $diasJson = json_encode(array_values($validated['dias_contratados']));
        $conflito = Vinculo::where('id_passageiro', $passageiro->id_passageiro)
            ->where('status', 'ativo')
            ->whereHas('disponibilidades', function ($q) use ($disponibilidade, $diasJson) {
                $q->where('turno', $disponibilidade->turno)
                  ->whereRaw('JSON_OVERLAPS(vinculo_disponibilidade.dias_contratados, ?)', [$diasJson]);
            })
            ->exists();

        if ($conflito) {
            return back()->with('aviso', 'Este passageiro já possui um vínculo ativo para um ou mais dos dias solicitados neste turno.');
        }

        // Já existe solicitação pendente para este passageiro nesta disponibilidade
        $jaPendente = Solicitacao::where('id_passageiro', $passageiro->id_passageiro)
            ->where('status', 'pendente')
            ->whereHas('disponibilidades', fn ($q) => $q->where('disponibilidade.id_disponibilidade', $disponibilidade->id_disponibilidade))
            ->exists();

        if ($jaPendente) {
            return back()->with('aviso', 'Já existe uma solicitação pendente para este passageiro nessa disponibilidade.');
        }

        DB::transaction(function () use ($responsavel, $passageiro, $disponibilidade, $validated) {
            $solicitacao = Solicitacao::create([
                'id_van'                 => $disponibilidade->id_van,
                'id_passageiro'          => $passageiro->id_passageiro,
                'id_responsavel'         => $responsavel->id_responsavel,
                'id_usuario_solicitante' => auth()->id(),
                'tipo_solicitante'       => 'responsavel',
                'status'                 => 'pendente',
                'mensagem'               => $validated['mensagem'] ?? null,
                'data_solicitacao'       => now(),
            ]);

            $solicitacao->disponibilidades()->attach($disponibilidade->id_disponibilidade, [
                'preco_mensal'     => $disponibilidade->preco_mensal,
                'dias_contratados' => json_encode(array_values($validated['dias_contratados'])),
            ]);
        });

        return back()->with('sucesso', 'Solicitação enviada com sucesso! O motorista será notificado.');
    }

    public function cancelar(int $id): RedirectResponse
    {
        $responsavel = Auth::user()->responsavel;

        $solicitacao = Solicitacao::where('id_responsavel', $responsavel->id_responsavel)
            ->where('status', 'pendente')
            ->findOrFail($id);

        $solicitacao->update([
            'status'      => 'cancelada',
            'cancelado_em' => now(),
            'cancelado_por' => Auth::id(),
        ]);

        return back()->with('sucesso', 'Solicitação cancelada.');
    }
}
