<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorista;
use App\Models\Passageiro;
use App\Models\Responsavel;
use App\Models\Solicitacao;
use App\Models\Van;
use App\Models\Vinculo;
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
            ->map(function ($m) {
                $email = $m->usuario?->email ?? '—';
                return [
                'id_motorista'     => $m->id_motorista,
                'nome'             => $m->usuario?->pessoa?->nome ?? '—',
                'email'            => $email,
                'is_teste'         => str_ends_with($email, '@teste.rotasegura'),
                'cnh_numero'       => $m->cnh_numero,
                'cnh_categoria'    => $m->cnh_categoria,
                'cnh_validade'              => $m->cnh_validade,
                'cnh_foto_url'              => $m->cnh_foto_url,
                'certidao_antecedentes_url' => $m->certidao_antecedentes_url,
                'curso_transporte_url'      => $m->curso_transporte_url,
                'renach_url'                => $m->renach_url,
                'status_aprovacao'          => $m->status_aprovacao,
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
            ];});

        $vans = Van::with(['motorista.usuario.pessoa', 'motorista.usuario'])
            ->orderByRaw("FIELD(status_aprovacao, 'pendente', 'aprovado', 'rejeitado')")
            ->get()
            ->map(function ($v) {
                $email = $v->motorista?->usuario?->email ?? '';
                return [
                'id_van'                         => $v->id_van,
                'placa'                          => $v->placa,
                'marca'                          => $v->marca,
                'modelo'                         => $v->modelo,
                'ano_fabricacao'                 => $v->ano_fabricacao,
                'cor'                            => $v->cor,
                'capacidade'                     => $v->capacidade_passageiros,
                'status_aprovacao'               => $v->status_aprovacao,
                'motivo_rejeicao'                => $v->motivo_rejeicao,
                'documentacao_completa'          => $v->documentacao_completa,
                'nome_motorista'                 => $v->motorista?->usuario?->pessoa?->nome ?? '—',
                'id_motorista'                   => $v->id_motorista,
                'is_teste'                       => str_ends_with($email, '@teste.rotasegura'),
                // fotos
                'foto_url'                       => $v->foto_url,
                'foto_verso_url'                 => $v->foto_verso_url,
                'foto_interior_url'              => $v->foto_interior_url,
                'foto_lateral_esq_url'           => $v->foto_lateral_esq_url,
                'foto_lateral_dir_url'           => $v->foto_lateral_dir_url,
                // documentos
                'crlv_url'                       => $v->crlv_url,
                'crlv_validade'                  => $v->crlv_validade?->format('d/m/Y'),
                'seguro_url'                     => $v->seguro_url,
                'seguro_validade'                => $v->seguro_validade?->format('d/m/Y'),
                'autorizacao_municipal_url'      => $v->autorizacao_municipal_url,
                'autorizacao_municipal_validade' => $v->autorizacao_municipal_validade?->format('d/m/Y'),
            ];});

        $responsaveis = Responsavel::with(['usuario.pessoa', 'passageiros.pessoa'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($r) {
                $email = $r->usuario?->email ?? '—';
                return [
                    'id_responsavel'   => $r->id_responsavel,
                    'nome'             => $r->usuario?->pessoa?->nome ?? '—',
                    'email'            => $email,
                    'is_teste'         => str_ends_with($email, '@teste.rotasegura'),
                    'tipo_responsavel' => $r->tipo_responsavel,
                    'created_at'       => $r->created_at?->format('d/m/Y'),
                    'passageiros'      => $r->passageiros->map(fn ($p) => [
                        'id_passageiro'  => $p->id_passageiro,
                        'nome'           => $p->pessoa?->nome ?? '—',
                        'ativo'          => (bool) $p->ativo,
                    ])->values()->all(),
                ];
            });

        $vinculos = Vinculo::with(['passageiro.pessoa', 'van.motorista.usuario.pessoa', 'disponibilidades'])
            ->orderByRaw("FIELD(status, 'ativo', 'inativo', 'cancelado')")
            ->orderBy('data_inicio', 'desc')
            ->get()
            ->map(fn ($v) => [
                'id_vinculo'    => $v->id_vinculo,
                'status'        => $v->status,
                'passageiro'    => $v->passageiro?->pessoa?->nome ?? '—',
                'motorista'     => $v->van?->motorista?->usuario?->pessoa?->nome ?? '—',
                'van'           => $v->van?->nome_servico ?? '—',
                'disponibilidades' => $v->disponibilidades->map(fn ($d) => [
                    'nome'  => $d->nome,
                    'turno' => $d->turno,
                ])->values()->all(),
                'preco_total'   => $v->preco_total ? number_format((float) $v->preco_total, 2, ',', '.') : null,
                'data_inicio'   => $v->data_inicio?->format('d/m/Y'),
                'data_fim'      => $v->data_fim?->format('d/m/Y'),
            ]);

        $solicitacoes = Solicitacao::with(['responsavel.usuario.pessoa', 'passageiro.pessoa', 'disponibilidades.van.motorista.usuario.pessoa'])
            ->orderByRaw("FIELD(status, 'pendente', 'aceita', 'rejeitada', 'cancelada')")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($s) => [
                'id_solicitacao' => $s->id_solicitacao,
                'status'         => $s->status,
                'responsavel'    => $s->responsavel?->usuario?->pessoa?->nome ?? '—',
                'passageiro'     => $s->passageiro?->pessoa?->nome ?? '—',
                'disponibilidades' => $s->disponibilidades->map(fn ($d) => [
                    'nome'      => $d->nome,
                    'turno'     => $d->turno,
                    'motorista' => $d->van?->motorista?->usuario?->pessoa?->nome ?? '—',
                    'van'       => $d->van?->nome_servico ?? '—',
                ])->values()->all(),
                'created_at' => $s->created_at?->format('d/m/Y'),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'motoristas_total'      => Motorista::count(),
                'motoristas_pendentes'  => Motorista::where('status_aprovacao', 'pendente')->count(),
                'motoristas_aprovados'  => Motorista::where('status_aprovacao', 'aprovado')->count(),
                'vans_total'            => Van::count(),
                'vans_pendentes'        => Van::where('status_aprovacao', 'pendente')->count(),
                'responsaveis_total'    => Responsavel::count(),
                'passageiros_total'     => Passageiro::count(),
                'vinculos_ativos'       => Vinculo::where('status', 'ativo')->count(),
                'solicitacoes_pendentes'=> Solicitacao::where('status', 'pendente')->count(),
            ],
            'motoristas'   => $motoristas,
            'vans'         => $vans,
            'responsaveis' => $responsaveis,
            'vinculos'     => $vinculos,
            'solicitacoes' => $solicitacoes,
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
