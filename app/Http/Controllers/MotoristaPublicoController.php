<?php

namespace App\Http\Controllers;

use App\Models\Motorista;
use Inertia\Inertia;
use Inertia\Response;

class MotoristaPublicoController extends Controller
{
    public function show(string $id): Response
    {
        abort_if(!ctype_digit($id), 404);

        $motorista = Motorista::where('id_motorista', (int) $id)
            ->where('status_aprovacao', 'aprovado')
            ->with([
                'usuario.pessoa',
                'van' => fn ($q) => $q->where('status_aprovacao', 'aprovado'),
                'van.disponibilidades' => fn ($q) => $q
                    ->where('ativa', true)
                    ->withCount(['vinculos as vagas_ocupadas' => fn ($q) => $q->where('status', 'ativo')]),
                'van.disponibilidades.dias',
            ])
            ->firstOrFail();

        $van = $motorista->van;
        abort_if(!$van, 404);

        $email = $motorista->usuario?->email ?? '';

        return Inertia::render('Motorista/PerfilPublico', [
            'motorista' => [
                'id_motorista' => $motorista->id_motorista,
                'nome'         => $motorista->usuario->pessoa->nome,
                'foto_url'     => $motorista->usuario->foto_url,
                'is_teste'     => str_ends_with($email, '@teste.rotasegura'),
            ],
            'van' => [
                'nome_servico'     => $van->nome_servico,
                'foto_url'         => $van->foto_url,
                'placa'            => $van->placa,
                'disponibilidades' => $van->disponibilidades->map(fn ($d) => [
                    'id_disponibilidade' => $d->id_disponibilidade,
                    'nome'               => $d->nome,
                    'turno'              => $d->turno,
                    'preco_mensal'       => $d->preco_mensal !== null ? (float) $d->preco_mensal : null,
                    'capacidade_total'   => $d->capacidade_total,
                    'vagas_ocupadas'     => $d->vagas_ocupadas,
                    'regioes_atendidas'  => $d->regioes_atendidas ?? [],
                    'escolas_atendidas'  => $d->escolas_atendidas ?? [],
                    'dias'               => $d->dias->pluck('dia_semana')->all(),
                ])->values()->all(),
            ],
        ]);
    }
}
