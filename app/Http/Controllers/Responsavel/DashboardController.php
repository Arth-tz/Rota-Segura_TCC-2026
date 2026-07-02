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
            ->with('disponibilidades')
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
            $vinculosDeste = $vinculosAtivos->get($passageiro->id_passageiro) ?? collect();
            $status        = 'sem_van';
            $statusLabel   = 'Sem van';
            $statusColor   = 'slate';
            $proximosDias  = [];

            if ($vinculosDeste->isNotEmpty()) {
                $status      = 'vinculo_ativo';
                $statusLabel = $vinculosDeste->count() > 1 ? 'Vínculos ativos' : 'Vínculo ativo';
                $statusColor = 'green';
                foreach ($vinculosDeste as $vinculo) {
                    $proximosDias = array_merge($proximosDias, $this->proximosDias($vinculo, $presencasBulk));
                }
                // Ordena por data e remove duplicatas de data+vinculo
                usort($proximosDias, fn ($a, $b) => strcmp($a['data'], $b['data']));
            } elseif (($pendentesPorPassageiro[$passageiro->id_passageiro] ?? collect())->isNotEmpty()) {
                $status      = 'solicitacao_pendente';
                $statusLabel = 'Solicitação pendente';
                $statusColor = 'amber';
            }

            $solicitacoesDeste = ($pendentesPorPassageiro[$passageiro->id_passageiro] ?? collect())
                ->map(fn ($s) => [
                    'id_solicitacao'   => $s->id_solicitacao,
                    'data_solicitacao' => $s->data_solicitacao?->format('d/m/Y'),
                    'disponibilidades' => $s->disponibilidades->map(fn ($d) => [
                        'nome'             => $d->nome,
                        'turno'            => $d->turno,
                        'dias_contratados' => json_decode($d->pivot->dias_contratados ?? '[]', true),
                        'preco_mensal'     => $d->pivot->preco_mensal,
                    ])->all(),
                ])->values()->all();

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
