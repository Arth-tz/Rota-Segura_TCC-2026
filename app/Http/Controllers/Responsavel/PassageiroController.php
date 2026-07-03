<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Responsavel\StorePassageiroEnderecosRequest;
use App\Http\Requests\Responsavel\StorePassageiroEssencialRequest;
use App\Http\Requests\Responsavel\UpdatePassageiroRequest;
use App\Http\Requests\Responsavel\UpdatePassageiroEnderecosRequest;
use App\Models\Endereco;
use App\Models\Passageiro;
use App\Models\PassageiroEndereco;
use App\Models\Pessoa;
use App\Services\Geocoding\NominatimGeocodingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PassageiroController extends Controller
{
    public function __construct(
        private readonly NominatimGeocodingService $geocodingService
    ) {}

    // ─── CADASTRO INICIAL (ONBOARDING) ──────────────────────────────────────

    public function create(): Response
    {
        return Inertia::render('Responsavel/Passageiro/create');
    }

    public function storeEssencial(StorePassageiroEssencialRequest $request): RedirectResponse
    {
        $request->session()->put('cadastro_passageiro.essencial', $request->validated());
        return redirect()->route('responsavel.passageiros.create.enderecos');
    }

    public function createEnderecos(): RedirectResponse|Response
    {
        if (!session()->has('cadastro_passageiro.essencial')) {
            return redirect()->route('responsavel.passageiros.create')
                ->withErrors(['geral' => 'Preencha os dados essenciais primeiro.']);
        }
        return Inertia::render('Responsavel/Passageiro/enderecos');
    }

    public function store(StorePassageiroEnderecosRequest $request): RedirectResponse
    {
        $essencial = $request->session()->get('cadastro_passageiro.essencial');

        if (!$essencial) {
            return redirect()->route('responsavel.passageiros.create')
                ->withErrors(['geral' => 'Sessão expirada. Recomece o cadastro.']);
        }

        $dadosEnderecos = $this->enriquecerCoordenadasFaltantes($request->validated());

        try {
            DB::transaction(function () use ($dadosEnderecos, $essencial): void {
                $responsavel = auth()->user()->responsavel;

                if (!$responsavel) {
                    throw new \RuntimeException('Perfil de responsável não encontrado.');
                }

                $pessoa = Pessoa::create([
                    'nome'            => trim($essencial['nome']),
                    'cpf'             => $essencial['cpf'],
                    'data_nascimento' => $essencial['data_nascimento'],
                    'telefone'        => null,
                ]);

                $passageiro = Passageiro::create([
                    'id_pessoa'           => $pessoa->id_pessoa,
                    'observacoes_medicas' => $essencial['obs_medica'] ?? null,
                    'ativo'               => true,
                    'data_inscricao'      => now()->toDateString(),
                ]);

                $responsavel->passageiros()->attach($passageiro->id_passageiro, [
                    'data_inicio' => now()->toDateString(),
                    'data_fim'    => null,
                ]);

                $this->salvarEnderecos($passageiro, $dadosEnderecos);
            });

            $request->session()->forget('cadastro_passageiro');
            return redirect()->route('responsavel.dashboard');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['geral' => 'Erro ao cadastrar passageiro. Tente novamente.']);
        }
    }

    // ─── ADICIONAR PELO DASHBOARD ────────────────────────────────────────────

    public function adicionar(): Response
    {
        return Inertia::render('Responsavel/Passageiro/Adicionar');
    }

    public function storeCompleto(Request $request): RedirectResponse
    {
        $request->validate([
            'nome'            => 'required|string|min:2|max:150',
            'cpf'             => 'required|cpf|unique:pessoa,cpf',
            'data_nascimento' => 'required|date|before:today',
            'telefone'        => 'nullable|string|max:20',
            'obs_medica'      => 'nullable|string|max:5000',
            // endereços são opcionais ao adicionar pelo dashboard
            'embarques'    => 'nullable|array',
            'desembarques' => 'nullable|array',
            'residencia'   => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($request): void {
                $responsavel = auth()->user()->responsavel;

                if (!$responsavel) {
                    throw new \RuntimeException('Perfil de responsável não encontrado.');
                }

                $pessoa = Pessoa::create([
                    'nome'            => trim($request->nome),
                    'cpf'             => preg_replace('/\D/', '', $request->cpf),
                    'data_nascimento' => $request->data_nascimento,
                    'telefone'        => $request->telefone ? preg_replace('/\D/', '', $request->telefone) : null,
                ]);

                $passageiro = Passageiro::create([
                    'id_pessoa'           => $pessoa->id_pessoa,
                    'observacoes_medicas' => $request->obs_medica,
                    'ativo'               => true,
                    'data_inscricao'      => now()->toDateString(),
                ]);

                $responsavel->passageiros()->attach($passageiro->id_passageiro, [
                    'data_inicio' => now()->toDateString(),
                    'data_fim'    => null,
                ]);

                $dados = $request->all();

                if ($request->filled('embarques') || $request->filled('desembarques') || $request->filled('residencia')) {
                    $dados = $this->geocodificarEnderecos($dados);
                    $this->salvarEnderecosArray($passageiro, $dados);
                }

            });

            return redirect()->route('responsavel.dashboard')
                ->with('sucesso', 'Passageiro cadastrado com sucesso!');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['geral' => 'Erro ao cadastrar passageiro. Tente novamente.']);
        }
    }

    // ─── VISUALIZAR ──────────────────────────────────────────────────────────

    public function show(int $id): Response|RedirectResponse
    {
        $passageiro = $this->buscarPassageiroDoResponsavel($id);

        if (!$passageiro) {
            return redirect()->route('responsavel.dashboard')
                ->withErrors(['geral' => 'Passageiro não encontrado.']);
        }

        $passageiro->load([
            'pessoa',
            'enderecos.endereco',
            'vinculos.van.motorista.usuario.pessoa',
            'vinculos.disponibilidades.dias',
        ]);

        return Inertia::render('Responsavel/Passageiro/show', [
            'passageiro' => [
                'id_passageiro'       => $passageiro->id_passageiro,
                'nome'                => $passageiro->pessoa->nome,
                'cpf'                 => $passageiro->pessoa->cpf,
                'data_nascimento'     => $passageiro->pessoa->data_nascimento?->format('Y-m-d'),
                'telefone'            => $passageiro->pessoa->telefone,
                'foto_url'            => $passageiro->pessoa->foto_url,
                'observacoes_medicas' => $passageiro->observacoes_medicas,
                'ativo'               => $passageiro->ativo,
                'data_inscricao'      => $passageiro->data_inscricao?->format('Y-m-d'),
                'enderecos'           => $passageiro->enderecos->map(fn($pe) => [
                    'id_passageiro_endereco' => $pe->id_passageiro_endereco,
                    'tipo'                   => $pe->tipo,
                    'principal'              => $pe->principal,
                    'nome'                   => $pe->nome,
                    'endereco'               => $pe->endereco,
                ]),
                'vinculos'  => $passageiro->vinculos->map(fn($v) => [
                    'id_vinculo'  => $v->id_vinculo,
                    'status'      => $v->status,
                    'preco_total' => $v->preco_total,
                    'data_inicio' => $v->data_inicio?->format('Y-m-d'),
                    'data_fim'    => $v->data_fim?->format('Y-m-d'),
                    'van'         => [
                        'placa'  => $v->van?->placa,
                        'modelo' => $v->van?->modelo,
                        'nome_motorista' => $v->van?->motorista?->usuario?->pessoa?->nome,
                    ],
                ]),
            ],
        ]);
    }

    // ─── EDITAR DADOS PESSOAIS ───────────────────────────────────────────────

    public function edit(int $id): Response|RedirectResponse
    {
        $passageiro = $this->buscarPassageiroDoResponsavel($id);

        if (!$passageiro) {
            return redirect()->route('responsavel.dashboard')
                ->withErrors(['geral' => 'Passageiro não encontrado.']);
        }

        $passageiro->load('pessoa');

        return Inertia::render('Responsavel/Passageiro/Edit', [
            'passageiro' => [
                'id_passageiro'       => $passageiro->id_passageiro,
                'nome'                => $passageiro->pessoa->nome,
                'cpf'                 => $passageiro->pessoa->cpf,
                'data_nascimento'     => $passageiro->pessoa->data_nascimento?->format('Y-m-d'),
                'telefone'            => $passageiro->pessoa->telefone,
                'observacoes_medicas' => $passageiro->observacoes_medicas,
            ],
        ]);
    }

    public function update(UpdatePassageiroRequest $request, int $id): RedirectResponse
    {
        $passageiro = $this->buscarPassageiroDoResponsavel($id);

        if (!$passageiro) {
            return redirect()->route('responsavel.dashboard')
                ->withErrors(['geral' => 'Passageiro não encontrado.']);
        }

        try {
            DB::transaction(function () use ($request, $passageiro): void {
                $passageiro->pessoa->update([
                    'nome'            => trim($request->nome),
                    'data_nascimento' => $request->data_nascimento,
                    'telefone'        => $request->telefone,
                ]);

                $passageiro->update([
                    'observacoes_medicas' => $request->obs_medica,
                ]);
            });

            return redirect()->route('responsavel.passageiros.show', $id)
                ->with('sucesso', 'Dados atualizados com sucesso!');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['geral' => 'Erro ao atualizar. Tente novamente.']);
        }
    }

    // ─── EDITAR ENDEREÇOS ────────────────────────────────────────────────────

    public function editEnderecos(int $id): Response|RedirectResponse
    {
        $passageiro = $this->buscarPassageiroDoResponsavel($id);

        if (!$passageiro) {
            return redirect()->route('responsavel.dashboard')
                ->withErrors(['geral' => 'Passageiro não encontrado.']);
        }

        $passageiro->load(['pessoa', 'enderecos.endereco']);

        return Inertia::render('Responsavel/Passageiro/EditEnderecos', [
            'passageiro' => [
                'id_passageiro' => $passageiro->id_passageiro,
                'nome'          => $passageiro->pessoa->nome,
                'enderecos'     => $passageiro->enderecos->map(fn($pe) => [
                    'id_passageiro_endereco' => $pe->id_passageiro_endereco,
                    'tipo'                   => $pe->tipo,
                    'principal'              => $pe->principal,
                    'nome'                   => $pe->nome,
                    'endereco'               => $pe->endereco,
                ]),
            ],
        ]);
    }

    public function updateEnderecos(UpdatePassageiroEnderecosRequest $request, int $id): RedirectResponse
    {
        $passageiro = $this->buscarPassageiroDoResponsavel($id);

        if (!$passageiro) {
            return redirect()->route('responsavel.dashboard')
                ->withErrors(['geral' => 'Passageiro não encontrado.']);
        }

        try {
            DB::transaction(function () use ($request, $passageiro): void {
                $this->salvarEnderecosArray($passageiro, $request->validated(), substituir: true);

            });

            return redirect()->route('responsavel.passageiros.show', $id)
                ->with('sucesso', 'Endereços atualizados com sucesso!');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['geral' => 'Erro ao atualizar endereços. Tente novamente.']);
        }
    }

    // ─── DESATIVAR ───────────────────────────────────────────────────────────

    public function desativar(int $id): RedirectResponse
    {
        $passageiro = $this->buscarPassageiroDoResponsavel($id);

        if (!$passageiro) {
            return redirect()->route('responsavel.dashboard')
                ->withErrors(['geral' => 'Passageiro não encontrado.']);
        }

        $passageiro->update(['ativo' => false]);

        return redirect()->route('responsavel.dashboard')
            ->with('sucesso', 'Passageiro desativado com sucesso.');
    }

    // ─── HELPERS PRIVADOS ────────────────────────────────────────────────────

    /**
     * Busca passageiro garantindo que pertence ao responsável logado
     */
    private function buscarPassageiroDoResponsavel(int $id): ?Passageiro
    {
        $responsavel = auth()->user()->responsavel;

        if (!$responsavel) return null;

        return $responsavel->passageiros()
            ->where('passageiro.id_passageiro', $id)
            ->first();
    }

    /**
     * Salva endereços no formato prefixado (onboarding — etapa 2).
     * Converte o formato prefixado para o formato array e delega ao método unificado.
     */
    private function salvarEnderecos(Passageiro $passageiro, array $dados): void
    {
        $convertido = [];

        foreach (['embarque', 'desembarque', 'residencia'] as $prefixo) {
            if (empty($dados["{$prefixo}_logradouro"])) continue;

            $item = [
                'logradouro'  => $dados["{$prefixo}_logradouro"],
                'numero'      => $dados["{$prefixo}_numero"]      ?? null,
                'complemento' => $dados["{$prefixo}_complemento"] ?? null,
                'bairro'      => $dados["{$prefixo}_bairro"]      ?? '',
                'cidade'      => $dados["{$prefixo}_cidade"]      ?? '',
                'estado'      => $dados["{$prefixo}_estado"]      ?? '',
                'cep'         => $dados["{$prefixo}_cep"]         ?? '',
                'latitude'    => $dados["{$prefixo}_latitude"]    ?? null,
                'longitude'   => $dados["{$prefixo}_longitude"]   ?? null,
                'nome'        => $prefixo === 'desembarque' ? ($dados['desembarque_nome'] ?? null) : null,
            ];

            if ($prefixo === 'residencia') {
                $convertido['residencia'] = [$item];
            } else {
                $convertido["{$prefixo}s"][] = $item;
            }
        }

        $this->salvarEnderecosArray($passageiro, $convertido);
    }

    /**
     * Salva endereços no formato array (dashboard — adicionar/editar).
     * Formato esperado: embarques[], desembarques[], residencia[] (lista com 1 item).
     */
    private function salvarEnderecosArray(Passageiro $passageiro, array $dados, bool $substituir = false): void
    {
        if ($substituir) {
            PassageiroEndereco::where('id_passageiro', $passageiro->id_passageiro)->delete();
        }

        // residencia[] aceita lista mas só persiste o primeiro item válido
        $mapa = [
            'embarques'   => 'embarque',
            'desembarques' => 'desembarque',
            'residencia'  => 'residencia',
        ];

        foreach ($mapa as $chave => $tipo) {
            $lista = $dados[$chave] ?? [];

            // Garante que residencia seja sempre uma lista
            if ($chave === 'residencia' && is_array($lista) && !isset($lista[0])) {
                $lista = [$lista];
            }

            $primeiroValido = true;

            foreach ($lista as $endDados) {
                if (empty($endDados['logradouro'])) continue;

                if (empty($endDados['latitude']) || empty($endDados['longitude'])) {
                    $coords = $this->geocodingService->geocodeAddress($endDados);
                    $endDados['latitude']  = $coords['latitude']  ?? null;
                    $endDados['longitude'] = $coords['longitude'] ?? null;
                }

                $endereco = Endereco::create([
                    'logradouro'  => $endDados['logradouro'],
                    'numero'      => $endDados['numero']      ?? null,
                    'complemento' => $endDados['complemento'] ?? null,
                    'bairro'      => $endDados['bairro'],
                    'cidade'      => $endDados['cidade'],
                    'estado'      => strtoupper($endDados['estado']),
                    'cep'         => preg_replace('/\D/', '', $endDados['cep']),
                    'latitude'    => $endDados['latitude']    ?? null,
                    'longitude'   => $endDados['longitude']   ?? null,
                ]);

                PassageiroEndereco::create([
                    'id_passageiro' => $passageiro->id_passageiro,
                    'id_endereco'   => $endereco->id_endereco,
                    'tipo'          => $tipo,
                    'principal'     => $primeiroValido,
                    'nome'          => $endDados['nome'] ?? null,
                ]);

                // Para residência só salva o primeiro; para os demais marca principal apenas no primeiro
                if ($chave === 'residencia') break;
                $primeiroValido = false;
            }
        }
    }

    /**
     * Geocodifica endereços no formato prefixado (onboarding).
     */
    private function enriquecerCoordenadasFaltantes(array $dados): array
    {
        foreach (['residencia', 'embarque', 'desembarque'] as $prefixo) {
            if (empty($dados["{$prefixo}_logradouro"])) continue;

            if (!empty($dados["{$prefixo}_latitude"]) && !empty($dados["{$prefixo}_longitude"])) continue;

            $resultado = $this->geocodingService->geocodeAddress([
                'logradouro' => $dados["{$prefixo}_logradouro"] ?? null,
                'numero'     => $dados["{$prefixo}_numero"]     ?? null,
                'bairro'     => $dados["{$prefixo}_bairro"]     ?? null,
                'cidade'     => $dados["{$prefixo}_cidade"]     ?? null,
                'estado'     => $dados["{$prefixo}_estado"]     ?? null,
                'cep'        => $dados["{$prefixo}_cep"]        ?? null,
            ]);

            if ($resultado === null) continue;

            $dados["{$prefixo}_latitude"]  = $resultado['latitude'];
            $dados["{$prefixo}_longitude"] = $resultado['longitude'];
        }

        return $dados;
    }

    /**
     * Geocodifica endereços no formato array (dashboard).
     * Percorre embarques[], desembarques[] e residencia[] e preenche coordenadas ausentes.
     */
    private function geocodificarEnderecos(array $dados): array
    {
        foreach (['embarques', 'desembarques', 'residencia'] as $chave) {
            if (empty($dados[$chave])) continue;

            $lista = $dados[$chave];
            if (is_array($lista) && !isset($lista[0])) {
                $lista = [$lista];
            }

            foreach ($lista as $i => $end) {
                if (empty($end['logradouro'])) continue;
                if (!empty($end['latitude']) && !empty($end['longitude'])) continue;

                $coords = $this->geocodingService->geocodeAddress($end);
                if ($coords) {
                    $lista[$i]['latitude']  = $coords['latitude'];
                    $lista[$i]['longitude'] = $coords['longitude'];
                }
            }

            $dados[$chave] = $lista;
        }

        return $dados;
    }

    /**
     * Cria endereço a partir de dados com prefixo
     */
    private function criarEnderecoPorPrefixo(array $dados, string $prefixo): Endereco
    {
        return Endereco::create([
            'logradouro'  => $dados["{$prefixo}_logradouro"],
            'numero'      => $dados["{$prefixo}_numero"]      ?? null,
            'complemento' => $dados["{$prefixo}_complemento"] ?? null,
            'bairro'      => $dados["{$prefixo}_bairro"],
            'cidade'      => $dados["{$prefixo}_cidade"],
            'estado'      => $dados["{$prefixo}_estado"],
            'cep'         => $dados["{$prefixo}_cep"],
            'latitude'    => $dados["{$prefixo}_latitude"]    ?? null,
            'longitude'   => $dados["{$prefixo}_longitude"]   ?? null,
        ]);
    }
}
