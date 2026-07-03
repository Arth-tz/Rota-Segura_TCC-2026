<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\DisponibilidadePassageiro;
use App\Models\Rota;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $usuario = auth()->user()->load([
            'pessoa',
            'motorista.van.disponibilidades.dias',
            'motorista.van.vinculos.passageiro.pessoa',
        ]);

        $motorista = $usuario->motorista;
        $van       = $motorista?->van;

        $vinculosAtivos = $van
            ? $van->vinculos->where('status', 'ativo')->values()
            : collect();

        // Faltas de hoje (uma query para todos os vínculos)
        $idsVinculos = $vinculosAtivos->pluck('id_vinculo')->all();
        $faltasHoje  = $idsVinculos
            ? DisponibilidadePassageiro::whereIn('id_vinculo', $idsVinculos)
                ->where('data', now()->toDateString())
                ->where('vai', false)
                ->get()
                ->keyBy('id_vinculo')
            : collect();

        $passageiros = $vinculosAtivos->map(fn ($v) => [
            'id_passageiro' => $v->passageiro?->id_passageiro,
            'nome'          => $v->passageiro?->pessoa?->nome,
            'foto_url'      => $v->passageiro?->pessoa?->foto_url,
            'id_vinculo'    => $v->id_vinculo,
            'preco_total'   => $v->preco_total,
            'data_inicio'   => $v->data_inicio?->format('Y-m-d'),
            'falta_hoje'    => $faltasHoje->has($v->id_vinculo),
            'motivo_falta'  => $faltasHoje->get($v->id_vinculo)?->motivo_falta,
        ])->values();

        $disponibilidades = $van
            ? $van->disponibilidades->map(fn ($d) => [
                'id_disponibilidade' => $d->id_disponibilidade,
                'nome'               => $d->nome,
                'turno'              => $d->turno,
                'preco_mensal'       => $d->preco_mensal,
                'capacidade_total'   => $d->capacidade_total,
                'ativa'              => $d->ativa,
                'dias'               => $d->dias->pluck('dia_semana')->values(),
                'bairros_atendidos'  => $d->bairros_atendidos ?? [],
                'escolas_atendidas'  => $d->escolas_atendidas ?? [],
            ])->values()
            : collect();

        // ── Trajetos: disponibilidades ativas com passageiros ordenados ──────
        $hoje = now()->toDateString();
        $diaSemanaHoje = ['0'=>'dom','1'=>'seg','2'=>'ter','3'=>'qua','4'=>'qui','5'=>'sex','6'=>'sab'][now()->format('w')];

        // Rotas ativas hoje (keyed by id_disponibilidade)
        $rotasAtivas = $van
            ? Rota::where('id_van', $van->id_van)
                ->where('data', $hoje)
                ->whereIn('status', ['em_andamento', 'planejada'])
                ->with(['paradas' => fn ($q) => $q->with(['endereco', 'passageiros.pessoa'])->orderBy('ordem')])
                ->get()
                ->keyBy('id_disponibilidade')
            : collect();

        $trajetos = $van
            ? $van->disponibilidades()
                ->where('ativa', true)
                ->with(['vinculos' => fn ($q) => $q->where('status', 'ativo')
                    ->with(['passageiro.pessoa', 'passageiro.enderecos.endereco'])
                    ->orderByPivot('ordem')])
                ->get()
                ->map(function ($d) use ($faltasHoje, $diaSemanaHoje, $rotasAtivas) {
                    $passageiros = $d->vinculos
                        ->filter(fn ($v) => collect(
                            json_decode($v->pivot->dias_contratados ?? '[]', true)
                        )->contains($diaSemanaHoje))
                        ->map(fn ($v) => [
                            'id_vinculo'   => $v->id_vinculo,
                            'id_passageiro'=> $v->passageiro?->id_passageiro,
                            'nome'         => $v->passageiro?->pessoa?->nome,
                            'ordem'        => $v->pivot->ordem,
                            'falta_hoje'   => $faltasHoje->has($v->id_vinculo),
                            'embarque'     => ($v->passageiro?->enderecos ?? collect())
                                ->where('tipo', 'embarque')->first()?->endereco?->enderecoCompleto ?? null,
                            'desembarque'  => ($v->passageiro?->enderecos ?? collect())
                                ->where('tipo', 'desembarque')->first()?->endereco?->enderecoCompleto ?? null,
                        ])->values();

                    $rotaAtiva = $rotasAtivas->get($d->id_disponibilidade);

                    return [
                        'id_disponibilidade' => $d->id_disponibilidade,
                        'nome'               => $d->nome,
                        'turno'              => $d->turno,
                        'passageiros'        => $passageiros,
                        'rota_ativa'         => $rotaAtiva
                            ? RotaController::formatarRotaArray($rotaAtiva)
                            : null,
                    ];
                })->values()
            : collect();

        $solicitacoes = $van
            ? $van->solicitacoes()
                ->where('status', 'pendente')
                ->with(['passageiro.pessoa', 'passageiro.enderecos.endereco', 'responsavel.usuario.pessoa', 'disponibilidades'])
                ->get()
                ->map(fn ($s) => [
                    'id_solicitacao'   => $s->id_solicitacao,
                    'mensagem'         => $s->mensagem,
                    'data_solicitacao' => $s->data_solicitacao?->format('d/m/Y'),
                    'passageiro' => [
                        'nome'                => $s->passageiro?->pessoa?->nome,
                        'foto_url'            => $s->passageiro?->pessoa?->foto_url,
                        'observacoes_medicas' => $s->passageiro?->observacoes_medicas,
                        'enderecos'           => ($s->passageiro?->enderecos ?? collect())->map(fn ($pe) => [
                            'tipo'     => $pe->tipo,
                            'principal' => $pe->principal,
                            'resumo'   => trim(collect([
                                $pe->endereco?->logradouro,
                                $pe->endereco?->numero ? 'nº ' . $pe->endereco->numero : null,
                                $pe->endereco?->bairro,
                            ])->filter()->implode(', ')),
                        ])->all(),
                    ],
                    'responsavel' => [
                        'nome'     => $s->responsavel?->usuario?->pessoa?->nome,
                        'telefone' => $s->responsavel?->usuario?->pessoa?->telefone,
                    ],
                    'disponibilidades' => $s->disponibilidades->map(fn ($d) => [
                        'nome'             => $d->nome,
                        'turno'            => $d->turno,
                        'preco_mensal'     => $d->pivot->preco_mensal,
                        'dias_contratados' => json_decode($d->pivot->dias_contratados ?? '[]', true),
                    ])->all(),
                    'preco_total' => $s->disponibilidades->sum(fn ($d) => (float) $d->pivot->preco_mensal),
                ])->values()
            : collect();

        $solicitacoesPendentes = $solicitacoes->count();

        return Inertia::render('Motorista/Dashboard', [
            'motorista' => $motorista ? [
                'id_motorista'     => $motorista->id_motorista,
                'cnh_numero'       => $motorista->cnh_numero,
                'cnh_categoria'    => $motorista->cnh_categoria,
                'cnh_validade'     => $motorista->cnh_validade?->format('Y-m-d'),
                'status_aprovacao' => $motorista->status_aprovacao,
                'motivo_rejeicao'  => $motorista->motivo_rejeicao,
            ] : null,
            'van' => $van ? [
                'id_van'                 => $van->id_van,
                'placa'                  => $van->placa,
                'modelo'                 => $van->modelo,
                'marca'                  => $van->marca,
                'ano_fabricacao'         => $van->ano_fabricacao,
                'cor'                    => $van->cor,
                'capacidade_passageiros' => $van->capacidade_passageiros,
                'status_aprovacao'       => $van->status_aprovacao,
                'status_operacional'     => $van->status_operacional,
                'documentacao_completa'  => $van->documentacao_completa,
                'motivo_rejeicao'        => $van->motivo_rejeicao,
                'foto_url'               => $van->foto_url,
            ] : null,
            'passageiros'           => $passageiros,
            'disponibilidades'      => $disponibilidades,
            'trajetos'              => $trajetos,
            'solicitacoes'          => $solicitacoes,
            'solicitacoesPendentes' => $solicitacoesPendentes,
            'usuario' => [
                'nome'  => $usuario->pessoa?->nome,
                'email' => $usuario->email,
            ],
        ]);
    }
}
