<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\Rota;
use App\Models\Vinculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AcompanharController extends Controller
{
    // GET /responsavel/acompanhar  (JSON polling a cada 5s)
    public function index(): JsonResponse
    {
        $responsavel = auth()->user()->load('responsavel.passageiros.pessoa')->responsavel;

        if (!$responsavel) {
            return response()->json(['passageiros' => []]);
        }

        $passageiros = $responsavel->passageiros->load(['enderecos.endereco']);
        $idsPassageiros = $passageiros->pluck('id_passageiro')->all();

        if (empty($idsPassageiros)) {
            return response()->json(['passageiros' => []]);
        }

        // Vínculos ativos por passageiro
        $vinculos = Vinculo::whereIn('id_passageiro', $idsPassageiros)
            ->where('status', 'ativo')
            ->get()
            ->keyBy('id_passageiro');

        // Rotas ativas hoje por van
        $idsVans = $vinculos->pluck('id_van')->filter()->unique()->values()->all();

        $rotasAtivas = $idsVans
            ? Rota::whereIn('id_van', $idsVans)
                ->where('data', now()->toDateString())
                ->where('status', 'em_andamento')
                ->with('ultimaLocalizacao')
                ->get()
                ->keyBy('id_van')
            : collect();

        // Confirmações de parada (embarque/desembarque) — uma query para todos
        $idsRotas = $rotasAtivas->pluck('id_rota')->filter()->all();
        $confirmacoesRaw = $idsRotas
            ? DB::table('parada_passageiro as pp')
                ->join('parada as p', 'p.id_parada', '=', 'pp.id_parada')
                ->whereIn('p.id_rota', $idsRotas)
                ->whereIn('pp.id_passageiro', $idsPassageiros)
                ->select('p.id_rota', 'pp.id_passageiro', 'p.tipo', 'pp.embarque_em', 'pp.desembarque_em')
                ->get()
            : collect();

        $resultado = $passageiros->map(function ($passageiro) use ($vinculos, $rotasAtivas, $confirmacoesRaw) {
            $vinculo = $vinculos->get($passageiro->id_passageiro);
            $rota    = $vinculo ? $rotasAtivas->get($vinculo->id_van) : null;

            $peEmbarque    = $passageiro->enderecos->firstWhere('tipo', 'embarque');
            $peDesembarque = $passageiro->enderecos->firstWhere('tipo', 'desembarque');

            $posicaoVan       = null;
            $statusPassageiro = 'sem_rota';

            if ($rota) {
                $ultima = $rota->ultimaLocalizacao;
                $posicaoVan = $ultima ? [
                    'latitude'    => (float) $ultima->latitude,
                    'longitude'   => (float) $ultima->longitude,
                    'capturada_em'=> $ultima->timestamp_captura?->toIso8601String(),
                ] : null;

                $linhas = $confirmacoesRaw
                    ->where('id_rota', $rota->id_rota)
                    ->where('id_passageiro', $passageiro->id_passageiro);

                $embarcou    = $linhas->where('tipo', 'embarque')->whereNotNull('embarque_em')->isNotEmpty();
                $desembarcou = $linhas->where('tipo', 'desembarque')->whereNotNull('desembarque_em')->isNotEmpty();

                $statusPassageiro = $desembarcou ? 'chegou' : ($embarcou ? 'a_bordo' : 'aguardando');
            }

            return [
                'id_passageiro' => $passageiro->id_passageiro,
                'nome'          => $passageiro->pessoa?->nome,
                'foto_url'      => $passageiro->pessoa?->foto_url,
                'posicao_van'   => $posicaoVan,
                'status'        => $statusPassageiro,
                'id_rota'       => $rota?->id_rota,
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
        })->values();

        return response()->json(['passageiros' => $resultado]);
    }
}
