<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\DisponibilidadePassageiro;
use App\Models\Rota;
use App\Models\Vinculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AcompanharController extends Controller
{
    private const MAPA_DIA = ['0' => 'dom', '1' => 'seg', '2' => 'ter', '3' => 'qua', '4' => 'qui', '5' => 'sex', '6' => 'sab'];

    // GET /responsavel/acompanhar  (JSON polling a cada 5s)
    public function index(): JsonResponse
    {
        $responsavel = auth()->user()->load('responsavel.passageiros.pessoa')->responsavel;

        if (!$responsavel) {
            return response()->json(['grupos' => [], 'sem_van' => []]);
        }

        $passageiros = $responsavel->passageiros->load(['enderecos.endereco']);
        $idsPassageiros = $passageiros->pluck('id_passageiro')->all();

        if (empty($idsPassageiros)) {
            return response()->json(['grupos' => [], 'sem_van' => []]);
        }

        $vinculos = Vinculo::whereIn('id_passageiro', $idsPassageiros)
            ->where('status', 'ativo')
            ->with(['van.motorista.usuario.pessoa', 'disponibilidades'])
            ->get();

        $passageirosPorId = $passageiros->keyBy('id_passageiro');

        // ── Passageiros sem nenhum vínculo ativo ────────────────────────────
        $idsComVinculo = $vinculos->pluck('id_passageiro')->unique()->all();
        $semVan = $passageiros->whereNotIn('id_passageiro', $idsComVinculo)
            ->map(fn ($p) => [
                'id_passageiro' => $p->id_passageiro,
                'nome'          => $p->pessoa?->nome,
                'foto_url'      => $p->pessoa?->foto_url,
            ])->values();

        if ($vinculos->isEmpty()) {
            return response()->json(['grupos' => [], 'sem_van' => $semVan]);
        }

        $hoje = now()->toDateString();
        $diaSemanaHoje = self::MAPA_DIA[now()->format('w')];
        $idsVans = $vinculos->pluck('id_van')->unique()->values()->all();

        $rotasHoje = Rota::whereIn('id_van', $idsVans)
            ->where('data', $hoje)
            ->with('ultimaLocalizacao')
            ->get();

        $idsVinculos = $vinculos->pluck('id_vinculo')->all();
        $faltasHoje = DisponibilidadePassageiro::whereIn('id_vinculo', $idsVinculos)
            ->where('data', $hoje)
            ->where('vai', false)
            ->get()
            ->keyBy('id_vinculo');

        $idsRotas = $rotasHoje->pluck('id_rota')->all();

        // Todas as paradas das rotas de hoje (não só as dos nossos passageiros) —
        // necessário pra calcular quantas paradas faltam até a de cada criança.
        $paradasPorRota = $idsRotas
            ? DB::table('parada')->whereIn('id_rota', $idsRotas)
                ->select('id_rota', 'id_parada', 'ordem', 'tipo', 'horario_real')
                ->orderBy('ordem')
                ->get()
                ->groupBy('id_rota')
            : collect();

        $confirmacoesRaw = $idsRotas
            ? DB::table('parada_passageiro as pp')
                ->join('parada as p', 'p.id_parada', '=', 'pp.id_parada')
                ->whereIn('p.id_rota', $idsRotas)
                ->whereIn('pp.id_passageiro', $idsPassageiros)
                ->select('p.id_rota', 'p.ordem', 'p.tipo', 'pp.id_passageiro', 'pp.embarque_em', 'pp.desembarque_em')
                ->get()
            : collect();

        $grupos = $vinculos->groupBy('id_van')->map(function ($vinculosDoVan) use (
            $rotasHoje, $faltasHoje, $confirmacoesRaw, $paradasPorRota, $diaSemanaHoje, $passageirosPorId
        ) {
            $van = $vinculosDoVan->first()->van;
            $pessoaMotorista = $van?->motorista?->usuario?->pessoa;

            $rotasDoVan = $rotasHoje->where('id_van', $van?->id_van);
            $rotaAtiva = $rotasDoVan->firstWhere('status', 'em_andamento');

            $posicaoVan = null;
            if ($rotaAtiva) {
                $ultima = $rotaAtiva->ultimaLocalizacao;
                $posicaoVan = $ultima ? [
                    'latitude'     => (float) $ultima->latitude,
                    'longitude'    => (float) $ultima->longitude,
                    'capturada_em' => $ultima->timestamp_captura?->toIso8601String(),
                ] : null;
            }

            $passageirosGrupo = $vinculosDoVan->map(fn ($vinculo) => $this->statusPassageiro(
                $vinculo, $passageirosPorId->get($vinculo->id_passageiro),
                $rotasDoVan, $faltasHoje, $confirmacoesRaw, $paradasPorRota, $diaSemanaHoje
            ))->filter()->values();

            $statusGrupo = 'sem_horario_hoje';
            if ($rotaAtiva) {
                $statusGrupo = 'em_andamento';
            } elseif ($passageirosGrupo->contains('status', 'atrasado')) {
                $statusGrupo = 'atrasado';
            } elseif ($passageirosGrupo->contains('status', 'aguardando_motorista')) {
                $statusGrupo = 'aguardando_motorista';
            } elseif ($passageirosGrupo->contains('status', 'concluido_sem_confirmacao')) {
                $statusGrupo = 'concluido_sem_confirmacao';
            } elseif ($passageirosGrupo->contains('status', 'concluido')) {
                $statusGrupo = 'concluido';
            } elseif ($passageirosGrupo->contains(fn ($p) => in_array($p['status'], ['aguardando', 'a_bordo', 'chegou']))) {
                $statusGrupo = 'em_andamento';
            }

            return [
                'chave'        => 'van-' . $van?->id_van,
                'status'       => $statusGrupo,
                'posicao_van'  => $posicaoVan,
                'van' => [
                    'id_van'       => $van?->id_van,
                    'nome_servico' => $van?->nome_servico,
                    'placa'        => $van?->placa,
                    'foto_url'     => $van?->foto_url,
                ],
                'motorista' => [
                    'nome'     => $pessoaMotorista?->nome,
                    'telefone' => $pessoaMotorista?->telefone,
                ],
                'passageiros' => $passageirosGrupo,
            ];
        })->values();

        return response()->json(['grupos' => $grupos, 'sem_van' => $semVan]);
    }

    private function statusPassageiro(
        Vinculo $vinculo, $passageiro, $rotasDoVan, $faltasHoje, $confirmacoesRaw, $paradasPorRota, string $diaSemanaHoje
    ): ?array {
        if (!$passageiro) {
            return null;
        }

        $idsDisponibilidades = $vinculo->disponibilidades->pluck('id_disponibilidade')->all();

        $temHorarioHoje = $vinculo->disponibilidades->contains(function ($disp) use ($diaSemanaHoje) {
            $dias = json_decode($disp->pivot->dias_contratados ?? '[]', true);
            return in_array($diaSemanaHoje, $dias);
        });

        $peEmbarque    = $passageiro->enderecos->firstWhere('tipo', 'embarque');
        $peDesembarque = $passageiro->enderecos->firstWhere('tipo', 'desembarque');

        $base = [
            'id_passageiro' => $passageiro->id_passageiro,
            'nome'          => $passageiro->pessoa?->nome,
            'foto_url'      => $passageiro->pessoa?->foto_url,
            'id_rota'       => null,
            'paradas_restantes' => null,
            'embarque' => $peEmbarque?->endereco ? [
                'latitude'   => $peEmbarque->endereco->latitude ? (float) $peEmbarque->endereco->latitude : null,
                'longitude'  => $peEmbarque->endereco->longitude ? (float) $peEmbarque->endereco->longitude : null,
                'logradouro' => $peEmbarque->endereco->logradouro,
                'bairro'     => $peEmbarque->endereco->bairro,
            ] : null,
            'desembarque' => $peDesembarque?->endereco ? [
                'latitude'   => $peDesembarque->endereco->latitude ? (float) $peDesembarque->endereco->latitude : null,
                'longitude'  => $peDesembarque->endereco->longitude ? (float) $peDesembarque->endereco->longitude : null,
                'logradouro' => $peDesembarque->endereco->logradouro,
                'bairro'     => $peDesembarque->endereco->bairro,
                'nome'       => $peDesembarque->nome,
            ] : null,
        ];

        if ($faltasHoje->has($vinculo->id_vinculo)) {
            return $base + ['status' => 'falta_hoje', 'motivo_falta' => $faltasHoje->get($vinculo->id_vinculo)->motivo_falta];
        }

        if (!$temHorarioHoje) {
            return $base + ['status' => 'sem_horario_hoje'];
        }

        // Se houver mais de uma rota hoje pra mesma disponibilidade (ex: motorista reiniciou
        // o trajeto), prioriza a que está em andamento e, entre as demais, a mais recente.
        $rota = $rotasDoVan
            ->filter(fn ($r) => in_array($r->id_disponibilidade, $idsDisponibilidades))
            ->sortByDesc(fn ($r) => ($r->status === 'em_andamento' ? 1_000_000 : 0) + $r->id_rota)
            ->first();

        if (!$rota) {
            $turno = $vinculo->disponibilidades->first(fn ($d) => in_array($d->id_disponibilidade, $idsDisponibilidades))?->turno;
            return $base + ['status' => $this->estaAtrasado($turno) ? 'atrasado' : 'aguardando_motorista'];
        }

        $base['id_rota'] = $rota->id_rota;

        if ($rota->status === 'concluida') {
            $confirmouDesembarque = $confirmacoesRaw
                ->where('id_rota', $rota->id_rota)
                ->where('id_passageiro', $passageiro->id_passageiro)
                ->where('tipo', 'desembarque')
                ->whereNotNull('desembarque_em')
                ->isNotEmpty();

            return $base + ['status' => $confirmouDesembarque ? 'concluido' : 'concluido_sem_confirmacao'];
        }

        // status === 'em_andamento' (ou 'planejada', tratado como aguardando)
        if ($rota->status !== 'em_andamento') {
            return $base + ['status' => 'aguardando_motorista'];
        }

        $minhasLinhas = $confirmacoesRaw
            ->where('id_rota', $rota->id_rota)
            ->where('id_passageiro', $passageiro->id_passageiro);

        $embarcou    = $minhasLinhas->where('tipo', 'embarque')->whereNotNull('embarque_em')->isNotEmpty();
        $desembarcou = $minhasLinhas->where('tipo', 'desembarque')->whereNotNull('desembarque_em')->isNotEmpty();

        $status = $desembarcou ? 'chegou' : ($embarcou ? 'a_bordo' : 'aguardando');

        // Quantas paradas faltam até a próxima parada relevante desta criança
        $proximaLinha = $desembarcou ? null : $minhasLinhas->firstWhere('tipo', $embarcou ? 'desembarque' : 'embarque');

        if ($proximaLinha) {
            $todasParadas = $paradasPorRota->get($rota->id_rota) ?? collect();
            $base['paradas_restantes'] = $todasParadas
                ->where('ordem', '<=', $proximaLinha->ordem)
                ->whereNull('horario_real')
                ->count();
        }

        return $base + ['status' => $status];
    }

    // Heurística aproximada — não há horário previsto cadastrado por disponibilidade,
    // então usamos uma janela típica por turno. Gera falso positivo/negativo em rotas
    // com horário fora do comum; é um sinal, não uma certeza.
    private function estaAtrasado(?string $turno): bool
    {
        $agora = now()->format('H:i');

        return match ($turno) {
            'manha' => $agora > '09:30',
            'tarde' => $agora > '15:00',
            default => false,
        };
    }
}
