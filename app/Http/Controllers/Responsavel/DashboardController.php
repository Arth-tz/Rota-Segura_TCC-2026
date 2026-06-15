<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $usuario = auth()->user()->load([
            'responsavel.passageiros.pessoa',
            'responsavel.passageiros.vinculoAtivo',
        ]);

        $responsavel = $usuario->responsavel;

        if (!$responsavel) {
            abort(403, 'Perfil de responsável não encontrado.');
        }

        $passageiros = $responsavel->passageiros;

        // Busca solicitações pendentes apenas se houver passageiros
        $pendentesPorPassageiro = collect();

        if ($passageiros->isNotEmpty()) {
            $idsPassageiros = $passageiros->pluck('id_passageiro')->all();

            $pendentesPorPassageiro = Solicitacao::query()
                ->where('id_responsavel', $responsavel->id_responsavel)
                ->whereIn('id_passageiro', $idsPassageiros)
                ->where('status', 'pendente')
                ->get()
                ->groupBy('id_passageiro');
        }

        $passageirosResumo = $passageiros->map(function ($passageiro) use ($pendentesPorPassageiro) {
            $status      = 'sem_van';
            $statusLabel = 'Sem van';
            $statusColor = 'slate';

            if ($passageiro->vinculoAtivo) {
                $status      = 'vinculo_ativo';
                $statusLabel = 'Vínculo ativo';
                $statusColor = 'green';
            } elseif (($pendentesPorPassageiro[$passageiro->id_passageiro] ?? collect())->isNotEmpty()) {
                $status      = 'solicitacao_pendente';
                $statusLabel = 'Solicitação pendente';
                $statusColor = 'amber';
            }

            return [
                'id_passageiro' => $passageiro->id_passageiro,
                'nome'          => $passageiro->pessoa?->nome,
                'foto_url'      => $passageiro->pessoa?->foto_url,
                'status'        => $status,
                'status_label'  => $statusLabel,
                'status_color'  => $statusColor,
                'data_inscricao'=> optional($passageiro->data_inscricao)?->format('Y-m-d'),
            ];
        })->values();

        return Inertia::render('Responsavel/Dashboard', [
            'passageiros' => $passageirosResumo,
        ]);
    }
}