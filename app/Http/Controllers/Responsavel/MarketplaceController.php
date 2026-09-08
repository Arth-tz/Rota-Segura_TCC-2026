<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\Disponibilidade;
use App\Models\Solicitacao;
use App\Models\Vinculo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    public function index(Request $request): Response
    {
        $request->validate([
            'turno'     => 'nullable|in:manha,tarde,integral',
            'cidade'    => 'nullable|string|max:100',
            'bairro'    => 'nullable|string|max:100',
            'escola'    => 'nullable|string|max:150',
            'motorista' => 'nullable|string|max:100',
            'prefixo'   => 'nullable|string|max:20',
            'dias'      => 'nullable|array|max:7',
            'dias.*'    => 'in:seg,ter,qua,qui,sex,sab,dom',
        ]);

        $turno     = $request->get('turno', '');
        $cidade    = $request->get('cidade', '');
        $bairro    = $request->get('bairro', '');
        $escola    = $request->get('escola', '');
        $motorista = $request->get('motorista', '');
        $prefixo   = $request->get('prefixo', '');
        $dias      = array_filter((array) $request->get('dias', []));

        $query = Disponibilidade::query()
            ->where('ativa', true)
            ->whereHas('van', fn ($q) => $q->where('status_aprovacao', 'aprovado')
                                           ->where('status_operacional', 'ativa')
                                           ->whereNotNull('foto_url'))
            ->whereHas('van.motorista', fn ($q) => $q->where('status_aprovacao', 'aprovado'))
            ->with(['van.motorista.usuario.pessoa', 'dias'])
            ->withCount(['vinculos as vagas_ocupadas' => fn ($q) => $q->where('status', 'ativo')]);

        if ($turno) {
            $query->where('turno', $turno);
        }

        if ($cidade) {
            $query->where('regioes_atendidas', 'LIKE', '%' . $cidade . '%');
        }

        if ($bairro) {
            $query->where('regioes_atendidas', 'LIKE', '%' . $bairro . '%');
        }

        if ($escola) {
            $query->where('escolas_atendidas', 'LIKE', '%' . $escola . '%');
        }

        if ($motorista) {
            $query->where(function ($q) use ($motorista) {
                $q->whereHas(
                    'van.motorista.usuario.pessoa',
                    fn ($q2) => $q2->where('nome', 'LIKE', '%' . $motorista . '%')
                )->orWhereHas(
                    'van',
                    fn ($q2) => $q2->where('nome_servico', 'LIKE', '%' . $motorista . '%')
                );
            });
        }

        if ($prefixo) {
            $query->whereHas('van', fn ($q) => $q->where('prefixo_municipal', 'LIKE', '%' . $prefixo . '%'));
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
                'regioes_atendidas'  => $disp->regioes_atendidas ?? [],
                'escolas_atendidas'  => $disp->escolas_atendidas ?? [],
                'dias'               => $disp->dias->pluck('dia_semana')->all(),
                'van' => [
                    'id_van'                 => $disp->van?->id_van,
                    'placa'                  => $disp->van?->placa,
                    'nome_servico'           => $disp->van?->nome_servico,
                    'modelo'                 => $disp->van?->modelo,
                    'marca'                  => $disp->van?->marca,
                    'ano'                    => $disp->van?->ano_fabricacao,
                    'cor'                    => $disp->van?->cor,
                    'capacidade_passageiros' => $disp->van?->capacidade_passageiros,
                    'prefixo_municipal'      => $disp->van?->prefixo_municipal,
                    // Fotos
                    'foto_url'               => $disp->van?->foto_url,
                    'foto_verso_url'         => $disp->van?->foto_verso_url,
                    'foto_interior_url'      => $disp->van?->foto_interior_url,
                    'foto_lateral_esq_url'   => $disp->van?->foto_lateral_esq_url,
                    'foto_lateral_dir_url'   => $disp->van?->foto_lateral_dir_url,
                    // Status dos documentos
                    'doc_crlv'                       => !empty($disp->van?->crlv_url),
                    'doc_seguro'                     => !empty($disp->van?->seguro_url),
                    'doc_autorizacao'                => !empty($disp->van?->autorizacao_municipal_url),
                    'doc_ipva'                       => !empty($disp->van?->ipva_comprovante_url),
                    'documentacao_completa'          => (bool) ($disp->van?->documentacao_completa ?? false),
                    // URLs de documentos regulatórios — visíveis ao responsável (CTB/LGPD art. 7, II)
                    'crlv_url'                       => $disp->van?->crlv_url,
                    'crlv_validade'                  => $disp->van?->crlv_validade?->format('d/m/Y'),
                    'seguro_url'                     => $disp->van?->seguro_url,
                    'seguro_validade'                => $disp->van?->seguro_validade?->format('d/m/Y'),
                    'autorizacao_municipal_url'      => $disp->van?->autorizacao_municipal_url,
                    'autorizacao_municipal_validade' => $disp->van?->autorizacao_municipal_validade?->format('d/m/Y'),
                ],
                'motorista' => [
                    'nome'          => $pessoa?->nome,
                    'telefone'      => $pessoa?->telefone,
                    'foto_url'      => $pessoa?->foto_url,
                    'cnh_categoria' => $disp->van?->motorista?->cnh_categoria,
                ],
            ];
        });

        $responsavel    = auth()->user()->responsavel;
        $idsPassageiros = $responsavel->passageiros()->pluck('passageiro.id_passageiro')->all();

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

        // IDs de disponibilidades já vinculadas (vínculo ativo)
        $idsVinculados = $idsPassageiros
            ? Vinculo::whereIn('id_passageiro', $idsPassageiros)
                ->where('status', 'ativo')
                ->with('disponibilidades')
                ->get()
                ->flatMap(fn ($v) => $v->disponibilidades->pluck('id_disponibilidade'))
                ->unique()->values()->all()
            : [];

        // IDs de disponibilidades com solicitação pendente
        $idsSolicitados = $idsPassageiros
            ? Solicitacao::where('id_responsavel', $responsavel->id_responsavel)
                ->whereIn('id_passageiro', $idsPassageiros)
                ->where('status', 'pendente')
                ->with('disponibilidades')
                ->get()
                ->flatMap(fn ($s) => $s->disponibilidades->pluck('id_disponibilidade'))
                ->unique()->values()->all()
            : [];

        return Inertia::render('Responsavel/Marketplace', [
            'disponibilidades' => $disponibilidades,
            'passageiros'      => $passageiros,
            'ids_vinculados'   => $idsVinculados,
            'ids_solicitados'  => $idsSolicitados,
            'filtros'          => [
                'turno'     => $turno,
                'cidade'    => $cidade,
                'bairro'    => $bairro,
                'escola'    => $escola,
                'motorista' => $motorista,
                'prefixo'   => $prefixo,
                'dias'      => array_values($dias),
            ],
        ]);
    }
}
