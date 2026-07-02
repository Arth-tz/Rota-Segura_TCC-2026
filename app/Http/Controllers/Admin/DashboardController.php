<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorista;
use App\Models\Responsavel;
use App\Models\Van;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $motoristas = Motorista::with(['usuario.pessoa', 'van'])
            ->orderByRaw("FIELD(status_aprovacao, 'pendente', 'aprovado', 'rejeitado')")
            ->get()
            ->map(fn($m) => [
                'id_motorista'     => $m->id_motorista,
                'nome'             => $m->usuario?->pessoa?->nome ?? '—',
                'email'            => $m->usuario?->email ?? '—',
                'cnh_numero'       => $m->cnh_numero,
                'cnh_categoria'    => $m->cnh_categoria,
                'cnh_validade'     => $m->cnh_validade,
                'status_aprovacao' => $m->status_aprovacao,
                'motivo_rejeicao'  => $m->motivo_rejeicao,
                'data_avaliacao'   => $m->data_avaliacao_documento,
                'van'              => $m->van ? [
                    'id_van'  => $m->van->id_van,
                    'placa'   => $m->van->placa,
                    'modelo'  => $m->van->modelo,
                    'marca'   => $m->van->marca,
                    'ano'     => $m->van->ano_fabricacao,
                    'status'  => $m->van->status_aprovacao,
                ] : null,
            ]);

        $vans = Van::with(['motorista.usuario.pessoa'])
            ->orderByRaw("FIELD(status_aprovacao, 'pendente', 'aprovado', 'rejeitado')")
            ->get()
            ->map(fn($v) => [
                'id_van'               => $v->id_van,
                'placa'                => $v->placa,
                'marca'                => $v->marca,
                'modelo'               => $v->modelo,
                'ano_fabricacao'       => $v->ano_fabricacao,
                'cor'                  => $v->cor,
                'capacidade'           => $v->capacidade_passageiros,
                'status_aprovacao'     => $v->status_aprovacao,
                'motivo_rejeicao'      => $v->motivo_rejeicao,
                'documentacao_completa'=> $v->documentacao_completa,
                'nome_motorista'       => $v->motorista?->usuario?->pessoa?->nome ?? '—',
                'id_motorista'         => $v->id_motorista,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'motoristas_total'    => Motorista::count(),
                'motoristas_pendentes'=> Motorista::where('status_aprovacao', 'pendente')->count(),
                'motoristas_aprovados'=> Motorista::where('status_aprovacao', 'aprovado')->count(),
                'vans_total'          => Van::count(),
                'vans_pendentes'      => Van::where('status_aprovacao', 'pendente')->count(),
                'responsaveis_total'  => Responsavel::count(),
            ],
            'motoristas' => $motoristas,
            'vans'       => $vans,
        ]);
    }

    public function aprovarMotorista(int $id): RedirectResponse
    {
        $motorista = Motorista::findOrFail($id);
        $motorista->update([
            'status_aprovacao'           => 'aprovado',
            'motivo_rejeicao'            => null,
            'data_avaliacao_documento'   => now(),
            'id_usuario_avaliador'       => auth()->id(),
        ]);

        return back()->with('sucesso', 'Motorista aprovado.');
    }

    public function rejeitarMotorista(Request $request, int $id): RedirectResponse
    {
        $request->validate(['motivo' => 'nullable|string|max:500']);

        $motorista = Motorista::findOrFail($id);
        $motorista->update([
            'status_aprovacao'         => 'rejeitado',
            'motivo_rejeicao'          => $request->motivo,
            'data_avaliacao_documento' => now(),
            'id_usuario_avaliador'     => auth()->id(),
        ]);

        return back()->with('sucesso', 'Motorista rejeitado.');
    }

    public function aprovarVan(int $id): RedirectResponse
    {
        $van = Van::findOrFail($id);
        $van->update([
            'status_aprovacao'   => 'aprovado',
            'motivo_rejeicao'    => null,
            'data_avaliacao'     => now(),
            'id_usuario_avaliador' => auth()->id(),
        ]);

        return back()->with('sucesso', 'Van aprovada.');
    }

    public function rejeitarVan(Request $request, int $id): RedirectResponse
    {
        $request->validate(['motivo' => 'nullable|string|max:500']);

        $van = Van::findOrFail($id);
        $van->update([
            'status_aprovacao'   => 'rejeitado',
            'motivo_rejeicao'    => $request->motivo,
            'data_avaliacao'     => now(),
            'id_usuario_avaliador' => auth()->id(),
        ]);

        return back()->with('sucesso', 'Van rejeitada.');
    }
}
