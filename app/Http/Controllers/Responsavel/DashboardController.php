<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\DisponibilidadePassageiro;
use App\Models\Solicitacao;
use App\Models\Vinculo;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private const MAPA_DIA  = ['0'=>'dom','1'=>'seg','2'=>'ter','3'=>'qua','4'=>'qui','5'=>'sex','6'=>'sab'];
    private const LABEL_DIA = ['dom'=>'Dom','seg'=>'Seg','ter'=>'Ter','qua'=>'Qua','qui'=>'Qui','sex'=>'Sex','sab'=>'Sáb'];

    public function index(): Response
    {
        $usuario = auth()->user()->load([
            'responsavel.passageiros.pessoa',
        ]);

        $responsavel = $usuario->responsavel;

        if (!$responsavel) {
            abort(403, 'Perfil de responsável não encontrado.');
        }

        $passageiros = $responsavel->passageiros;

        if ($passageiros->isEmpty()) {
            return Inertia::render('Responsavel/Dashboard', ['passageiros' => []]);
        }

        $idsPassageiros = $passageiros->pluck('id_passageiro')->all();

        // ── Solicitações pendentes ──────────────────────────────────────────
        $pendentesPorPassageiro = Solicitacao::query()
            ->where('id_responsavel', $responsavel->id_responsavel)
            ->whereIn('id_passageiro', $idsPassageiros)
            ->where('status', 'pendente')
            ->with('disponibilidades')
            ->get()
            ->groupBy('id_passageiro');

        // ── Vínculos ativos com disponibilidades (uma query, sem N+1) ───────
        $vinculosAtivos = Vinculo::whereIn('id_passageiro', $idsPassageiros)
            ->where('status', 'ativo')
            ->with(['disponibilidades.dias', 'van.motorista.usuario.pessoa'])
            ->get()
            ->groupBy('id_passageiro'); // suporta múltiplos vínculos por passageiro

        // ── Registros de presença dos próximos 14 dias (uma query) ──────────
        $idsVinculos = $vinculosAtivos->flatten()->pluck('id_vinculo')->filter()->all();

        $presencasBulk = $idsVinculos
            ? DisponibilidadePassageiro::whereIn('id_vinculo', $idsVinculos)
                ->where('data', '>=', now()->toDateString())
                ->where('data', '<=', now()->addDays(14)->toDateString())
                ->get()
                ->groupBy('id_vinculo')
            : collect();

        // ── Monta resumo ────────────────────────────────────────────────────
        $passageirosResumo = $passageiros->map(function ($passageiro) use (
            $pendentesPorPassageiro,
            $vinculosAtivos,
            $presencasBulk
        ) {
            $vinculosDeste          = $vinculosAtivos->get($passageiro->id_passageiro) ?? collect();
            $solicitacoesPassageiro = $pendentesPorPassageiro[$passageiro->id_passageiro] ?? collect();
            $status        = 'sem_van';
            $statusLabel   = 'Sem van';
            $statusColor   = 'slate';
            $proximosDias  = [];

            // BUG #3: status de 'solicitacao_pendente' só conta tipo='nova'
            $novasDestePassageiro = $solicitacoesPassageiro->filter(fn ($s) => $s->tipo === 'nova');

            if ($vinculosDeste->isNotEmpty()) {
                $status      = 'vinculo_ativo';
                $statusLabel = $vinculosDeste->count() > 1 ? 'Vínculos ativos' : 'Vínculo ativo';
                $statusColor = 'green';
                foreach ($vinculosDeste as $vinculo) {
                    $proximosDias = array_merge($proximosDias, $this->proximosDias($vinculo, $presencasBulk));
                }
                usort($proximosDias, fn ($a, $b) => strcmp($a['data'], $b['data']));
            } elseif ($novasDestePassageiro->isNotEmpty()) {
                $status      = 'solicitacao_pendente';
                $statusLabel = 'Solicitação pendente';
                $statusColor = 'amber';
            }

            // Solicitações nova para o accordion
            $solicitacoesDeste = $novasDestePassageiro
                ->map(fn ($s) => [
                    'id_solicitacao'      => $s->id_solicitacao,
                    'tipo'                => $s->tipo,
                    'id_vinculo_alterado' => $s->id_vinculo_alterado,
                    'data_solicitacao'    => $s->data_solicitacao?->format('d/m/Y'),
                    'disponibilidades'    => $s->disponibilidades->map(fn ($d) => [
                        'nome'             => $d->nome,
                        'turno'            => $d->turno,
                        'dias_contratados' => json_decode($d->pivot->dias_contratados ?? '[]', true),
                        'preco_mensal'     => $d->pivot->preco_mensal,
                    ])->all(),
                ])->values()->all();

            // BUG #2: alteracoesPendentes indexadas por id_disponibilidade (não por id_vinculo)
            $alteracoesPendentes = [];
            foreach ($solicitacoesPassageiro as $sol) {
                if ($sol->tipo === 'alteracao') {
                    foreach ($sol->disponibilidades as $disp) {
                        $alteracoesPendentes[$disp->id_disponibilidade] = [
                            'id_solicitacao'      => $sol->id_solicitacao,
                            'tipo'                => 'alteracao',
                            'id_vinculo_alterado' => $sol->id_vinculo_alterado,
                            'data_solicitacao'    => $sol->data_solicitacao?->format('d/m/Y'),
                        ];
                    }
                }
            }

            // BUG #1: motoristaDados como array de objetos por vínculo (não um único objeto do ->first())
            $motoristaDados = null;
            if ($vinculosDeste->isNotEmpty()) {
                $motoristaDados = $vinculosDeste->map(fn ($vl) => [
                    'id_vinculo'       => $vl->id_vinculo,
                    'nome'             => $vl->van?->motorista?->usuario?->pessoa?->nome,
                    'telefone'         => $vl->van?->motorista?->usuario?->pessoa?->telefone,
                    'van_modelo'       => trim(($vl->van?->marca ?? '') . ' ' . ($vl->van?->modelo ?? '')),
                    'van_placa'        => $vl->van?->placa,
                    'van_cor'          => $vl->van?->cor,
                    'nome_servico'     => $vl->van?->nome_servico,
                    'disponibilidades' => $vl->disponibilidades->map(fn ($d) => [
                        'id_vinculo'         => $vl->id_vinculo,
                        'id_disponibilidade' => $d->id_disponibilidade,
                        'nome'               => $d->nome,
                        'turno'              => $d->turno,
                        'dias_contratados'   => json_decode($d->pivot->dias_contratados ?? '[]', true),
                        'dias_disponiveis'   => $d->dias->pluck('dia_semana')->all(),
                        'alteracao_pendente' => $alteracoesPendentes[$d->id_disponibilidade] ?? null,
                    ])->values()->all(),
                ])->values()->all();
            }

            return [
                'id_passageiro'          => $passageiro->id_passageiro,
                'nome'                   => $passageiro->pessoa?->nome,
                'foto_url'               => $passageiro->pessoa?->foto_url,
                'status'                 => $status,
                'status_label'           => $statusLabel,
                'status_color'           => $statusColor,
                'data_inscricao'         => optional($passageiro->data_inscricao)?->format('Y-m-d'),
                'solicitacoes_pendentes' => $solicitacoesDeste,
                'proximos_dias'          => $proximosDias,
                'motorista'              => $motoristaDados,
            ];
        })->values();

        return Inertia::render('Responsavel/Dashboard', [
            'passageiros' => $passageirosResumo,
        ]);
    }

    // Gera as próximas 7 ocorrências do vínculo nos próximos 14 dias
    private function proximosDias(Vinculo $vinculo, $presencasBulk): array
    {
        // Reúne todos os dias_contratados de todas as disponibilidades do vínculo
        $diasContratados = collect();
        foreach ($vinculo->disponibilidades as $disp) {
            $dias = json_decode($disp->pivot->dias_contratados ?? '[]', true);
            $diasContratados = $diasContratados->merge($dias);
        }
        $diasContratados = $diasContratados->unique()->values()->toArray();

        if (empty($diasContratados)) {
            return [];
        }

        // Próximas ocorrências
        $datas = collect(range(0, 14))
            ->map(fn ($i) => now()->addDays($i))
            ->filter(fn ($d) => in_array(self::MAPA_DIA[$d->format('w')], $diasContratados))
            ->take(7)
            ->map(fn ($d) => $d->toDateString())
            ->values();

        // Registros de presença já carregados (sem nova query)
        $presencasVinculo = ($presencasBulk->get($vinculo->id_vinculo) ?? collect())
            ->keyBy(fn ($p) => $p->data->format('Y-m-d'));

        return $datas->map(function ($data) use ($presencasVinculo, $vinculo) {
            $p       = $presencasVinculo->get($data);
            $diaAbrev = self::MAPA_DIA[Carbon::parse($data)->format('w')];

            return [
                'data'         => $data,
                'dia_label'    => self::LABEL_DIA[$diaAbrev] ?? $diaAbrev,
                'vai'          => is_null($p) || (bool) $p->vai,
                'id_presenca'  => $p?->id_disponibilidade_passageiro,
                'motivo_falta' => $p?->motivo_falta,
                'id_vinculo'   => $vinculo->id_vinculo,
            ];
        })->all();
    }
}
