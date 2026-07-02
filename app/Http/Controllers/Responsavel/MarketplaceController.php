<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\Disponibilidade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    public function index(Request $request): Response
    {
        $request->validate([
            'turno'     => 'nullable|in:manha,tarde,integral',
            'bairro'    => 'nullable|string|max:100',
            'escola'    => 'nullable|string|max:150',
            'motorista' => 'nullable|string|max:100',
            'dias'      => 'nullable|array|max:7',
            'dias.*'    => 'in:seg,ter,qua,qui,sex,sab,dom',
        ]);

        $turno     = $request->get('turno', '');
        $bairro    = $request->get('bairro', '');
        $escola    = $request->get('escola', '');
        $motorista = $request->get('motorista', '');
        $dias      = array_filter((array) $request->get('dias', []));

        $query = Disponibilidade::query()
            ->where('ativa', true)
            ->whereHas('van', fn ($q) => $q->where('status_aprovacao', 'aprovado')
                                           ->where('status_operacional', 'ativa'))
            ->whereHas('van.motorista', fn ($q) => $q->where('status_aprovacao', 'aprovado'))
            ->with(['van.motorista.usuario.pessoa', 'dias'])
            ->withCount(['vinculos as vagas_ocupadas' => fn ($q) => $q->where('status', 'ativo')]);

        if ($turno) {
            $query->where('turno', $turno);
        }

        if ($bairro) {
            $query->where('bairros_atendidos', 'LIKE', '%' . $bairro . '%');
        }

        if ($escola) {
            $query->where('escolas_atendidas', 'LIKE', '%' . $escola . '%');
        }

        if ($motorista) {
            $query->whereHas(
                'van.motorista.usuario.pessoa',
                fn ($q) => $q->where('nome', 'LIKE', '%' . $motorista . '%')
            );
        }

        foreach ($dias as $dia) {
            $query->whereHas('dias', fn ($q) => $q->where('dia_semana', $dia));
        }

        $paginado = $query->paginate(12)->withQueryString();

        $disponibilidades = $paginado->through(function ($disp) {
            $pessoa = $disp->van?->motorista?->usuario?->pessoa;

            return [
                'id_disponibilidade' => $disp->id_disponibilidade,
                'nome'               => $disp->nome,
                'turno'              => $disp->turno,
                'preco_mensal'       => (float) $disp->preco_mensal,
                'capacidade_total'   => $disp->capacidade_total,
                'vagas_disponiveis'  => max(0, $disp->capacidade_total - $disp->vagas_ocupadas),
                'bairros_atendidos'  => $disp->bairros_atendidos ?? [],
                'escolas_atendidas'  => $disp->escolas_atendidas ?? [],
                'dias'               => $disp->dias->pluck('dia_semana')->all(),
                'van' => [
                    'id_van'   => $disp->van?->id_van,
                    'placa'    => $disp->van?->placa,
                    'modelo'   => $disp->van?->modelo,
                    'marca'    => $disp->van?->marca,
                    'ano'      => $disp->van?->ano_fabricacao,
                    'cor'      => $disp->van?->cor,
                    'foto_url' => $disp->van?->foto_url,
                ],
                'motorista' => [
                    'nome'     => $pessoa?->nome,
                    'telefone' => $pessoa?->telefone,
                ],
            ];
        });

        $responsavel = auth()->user()->responsavel;
        $passageiros = $responsavel->passageiros()
            ->with(['pessoa', 'enderecos'])
            ->get()
            ->map(fn ($p) => [
                'id_passageiro'   => $p->id_passageiro,
                'nome'            => $p->pessoa?->nome,
                'ativo'           => (bool) $p->ativo,
                'tem_embarque'    => $p->enderecos->where('tipo', 'embarque')->isNotEmpty(),
                'tem_desembarque' => $p->enderecos->where('tipo', 'desembarque')->isNotEmpty(),
            ])
            ->values();

        return Inertia::render('Responsavel/Marketplace', [
            'disponibilidades' => $disponibilidades,
            'passageiros'      => $passageiros,
            'filtros'          => [
                'turno'     => $turno,
                'bairro'    => $bairro,
                'escola'    => $escola,
                'motorista' => $motorista,
                'dias'      => array_values($dias),
            ],
        ]);
    }
}
