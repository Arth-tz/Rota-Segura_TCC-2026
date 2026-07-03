<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\DisponibilidadePassageiro;
use App\Models\Localizacao;
use App\Models\Parada;
use App\Models\ParadaPassageiro;
use App\Models\Rota;
use App\Models\Vinculo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RotaController extends Controller
{
    // POST /motorista/rotas/{disponibilidadeId}/iniciar
    public function iniciar(int $disponibilidadeId): JsonResponse
    {
        $van = $this->van();
        if (!$van) return response()->json(['error' => 'Van não encontrada.'], 422);

        $rotaExistente = Rota::where('id_van', $van->id_van)
            ->where('id_disponibilidade', $disponibilidadeId)
            ->where('data', now()->toDateString())
            ->whereIn('status', ['planejada', 'em_andamento'])
            ->with(['paradas' => fn ($q) => $q->with(['endereco', 'passageiros.pessoa'])->orderBy('ordem')])
            ->first();

        if ($rotaExistente) {
            return response()->json(['rota' => $this->formatarRota($rotaExistente)]);
        }

        $rota = Rota::create([
            'id_van'              => $van->id_van,
            'id_disponibilidade'  => $disponibilidadeId,
            'data'                => now()->toDateString(),
            'status'              => 'em_andamento',
            'horario_inicio_real' => now(),
        ]);

        $this->gerarParadas($rota, $disponibilidadeId);

        $rota->load(['paradas' => fn ($q) => $q->with(['endereco', 'passageiros.pessoa'])->orderBy('ordem')]);

        return response()->json(['rota' => $this->formatarRota($rota)]);
    }

    // POST /motorista/rotas/{rotaId}/encerrar
    public function encerrar(int $rotaId): JsonResponse
    {
        $rota = $this->rotaAutorizada($rotaId);
        if (!$rota) return response()->json(['error' => 'Não autorizado.'], 403);

        $rota->update([
            'status'           => 'concluida',
            'horario_fim_real' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    // POST /motorista/rotas/{rotaId}/posicao
    public function posicao(Request $request, int $rotaId): JsonResponse
    {
        $dados = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'precisao'  => 'nullable|numeric',
        ]);

        $rota = $this->rotaAutorizada($rotaId);
        if (!$rota) return response()->json(['error' => 'Não autorizado.'], 403);

        Localizacao::create([
            'id_rota'           => $rotaId,
            'id_van'            => $rota->id_van,
            'latitude'          => $dados['latitude'],
            'longitude'         => $dados['longitude'],
            'precisao_metros'   => $dados['precisao'] ?? null,
            'fonte_localizacao' => 'gps',
            'timestamp_captura' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    // POST /motorista/rotas/{rotaId}/paradas/{paradaId}/confirmar
    public function confirmar(Request $request, int $rotaId, int $paradaId): JsonResponse
    {
        $dados = $request->validate([
            'id_passageiro' => 'required|integer',
            'tipo'          => 'required|in:embarque,desembarque',
        ]);

        $rota = $this->rotaAutorizada($rotaId);
        if (!$rota) return response()->json(['error' => 'Não autorizado.'], 403);

        $parada = Parada::where('id_parada', $paradaId)
            ->where('id_rota', $rotaId)
            ->firstOrFail();

        $pp = ParadaPassageiro::where('id_parada', $paradaId)
            ->where('id_passageiro', $dados['id_passageiro'])
            ->firstOrFail();

        $campo = $dados['tipo'] === 'embarque' ? 'embarque_em' : 'desembarque_em';
        $pp->update([
            $campo               => now(),
            'marcado_por'        => 'motorista',
            'metodo_confirmacao' => 'manual',
        ]);

        if (!$parada->horario_real) {
            $parada->update(['horario_real' => now()]);
        }

        return response()->json([
            'ok'    => true,
            'hora'  => now()->format('H:i'),
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function van()
    {
        return auth()->user()->motorista?->van;
    }

    private function rotaAutorizada(int $rotaId): ?Rota
    {
        $van = $this->van();
        if (!$van) return null;

        return Rota::where('id_rota', $rotaId)
            ->where('id_van', $van->id_van)
            ->first();
    }

    private function gerarParadas(Rota $rota, int $disponibilidadeId): void
    {
        $diaSemana = ['0'=>'dom','1'=>'seg','2'=>'ter','3'=>'qua','4'=>'qui','5'=>'sex','6'=>'sab'][now()->format('w')];

        $vinculos = Vinculo::whereHas(
            'disponibilidades',
            fn ($q) => $q->where('disponibilidade.id_disponibilidade', $disponibilidadeId)
        )
        ->where('status', 'ativo')
        ->with([
            'passageiro.enderecos.endereco',
            'disponibilidades' => fn ($q) => $q->where('disponibilidade.id_disponibilidade', $disponibilidadeId),
        ])
        ->get()
        ->filter(function ($v) use ($diaSemana) {
            $disp = $v->disponibilidades->first();
            if (!$disp) return false;
            $dias = json_decode($disp->pivot->dias_contratados ?? '[]', true);
            return in_array($diaSemana, $dias);
        })
        ->sortBy(fn ($v) => $v->disponibilidades->first()?->pivot->ordem ?? 999)
        ->values();

        $idsVinculos = $vinculos->pluck('id_vinculo')->all();
        $faltas = $idsVinculos
            ? DisponibilidadePassageiro::whereIn('id_vinculo', $idsVinculos)
                ->where('data', now()->toDateString())
                ->where('vai', false)
                ->pluck('id_vinculo')
                ->flip()
            : collect();

        $ordem = 1;
        $desembGrupos = [];

        foreach ($vinculos as $v) {
            if ($faltas->has($v->id_vinculo)) continue;

            $passageiro = $v->passageiro;
            if (!$passageiro) continue;

            $endEmbarque = $passageiro->enderecos->firstWhere('tipo', 'embarque')?->endereco;
            if (!$endEmbarque) continue;

            $parada = Parada::create([
                'id_rota'     => $rota->id_rota,
                'id_endereco' => $endEmbarque->id_endereco,
                'ordem'       => $ordem++,
                'tipo'        => 'embarque',
            ]);

            ParadaPassageiro::create([
                'id_parada'     => $parada->id_parada,
                'id_passageiro' => $passageiro->id_passageiro,
            ]);

            $endDesembarque = $passageiro->enderecos->firstWhere('tipo', 'desembarque')?->endereco;
            if ($endDesembarque) {
                $key = $endDesembarque->id_endereco;
                $desembGrupos[$key] ??= ['endereco' => $endDesembarque, 'passageiros' => []];
                $desembGrupos[$key]['passageiros'][] = $passageiro->id_passageiro;
            }
        }

        foreach ($desembGrupos as $grupo) {
            $parada = Parada::create([
                'id_rota'     => $rota->id_rota,
                'id_endereco' => $grupo['endereco']->id_endereco,
                'ordem'       => $ordem++,
                'tipo'        => 'desembarque',
            ]);

            foreach ($grupo['passageiros'] as $idPassageiro) {
                ParadaPassageiro::create([
                    'id_parada'     => $parada->id_parada,
                    'id_passageiro' => $idPassageiro,
                ]);
            }
        }
    }

    public static function formatarRotaArray(Rota $rota): array
    {
        return [
            'id_rota' => $rota->id_rota,
            'status'  => $rota->status,
            'paradas' => $rota->paradas->map(fn ($p) => [
                'id_parada'    => $p->id_parada,
                'ordem'        => $p->ordem,
                'tipo'         => $p->tipo,
                'horario_real' => $p->horario_real?->format('H:i'),
                'endereco' => $p->endereco ? [
                    'logradouro' => $p->endereco->logradouro,
                    'numero'     => $p->endereco->numero,
                    'bairro'     => $p->endereco->bairro,
                    'latitude'   => $p->endereco->latitude ? (float) $p->endereco->latitude : null,
                    'longitude'  => $p->endereco->longitude ? (float) $p->endereco->longitude : null,
                ] : null,
                'passageiros' => $p->passageiros->map(fn ($pas) => [
                    'id_passageiro'  => $pas->id_passageiro,
                    'nome'           => $pas->pessoa?->nome,
                    'embarque_em'    => $pas->pivot->embarque_em,
                    'desembarque_em' => $pas->pivot->desembarque_em,
                ])->values()->all(),
            ])->values()->all(),
        ];
    }

    private function formatarRota(Rota $rota): array
    {
        return self::formatarRotaArray($rota);
    }
}
