<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Responsavel\StorePassageiroEnderecosRequest;
use App\Http\Requests\Responsavel\StorePassageiroEssencialRequest;
use App\Models\Destino;
use App\Models\Endereco;
use App\Models\Passageiro;
use App\Models\PassageiroEndereco;
use App\Models\Pessoa;
use App\Services\Geocoding\NominatimGeocodingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PassageiroController extends Controller
{
    public function __construct(
        private readonly NominatimGeocodingService $geocodingService
    ) {
    }

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
                ->withErrors(['geral' => 'Sessao expirada. Recomece o cadastro do passageiro.']);
        }

        $dadosEnderecos = $this->enriquecerCoordenadasFaltantes($request->validated());

        try {
            DB::transaction(function () use ($dadosEnderecos, $essencial): void {
                $responsavel = auth()->user()->responsavel;

                if (!$responsavel) {
                    throw new \RuntimeException('Perfil de responsavel nao encontrado.');
                }

                $pessoa = Pessoa::create([
                    'nome' => trim($essencial['nome']),
                    'cpf' => $essencial['cpf'],
                    'data_nascimento' => $essencial['data_nascimento'],
                    'telefone' => null,
                ]);

                $passageiro = Passageiro::create([
                    'id_pessoa' => $pessoa->id_pessoa,
                    'observacoes_medicas' => $essencial['obs_medica'] ?? null,
                    'ativo' => true,
                    'data_inscricao' => now()->toDateString(),
                ]);

                $responsavel->passageiros()->attach($passageiro->id_passageiro, [
                    'data_inicio' => now()->toDateString(),
                    'data_fim' => null,
                ]);

                $residencia = $this->criarEnderecoPorPrefixo($dadosEnderecos, 'residencia');
                $embarque = $this->criarEnderecoPorPrefixo($dadosEnderecos, 'embarque');
                $desembarque = $this->criarEnderecoPorPrefixo($dadosEnderecos, 'desembarque');
                $destinoEndereco = $this->criarEnderecoPorPrefixo($dadosEnderecos, 'destino');

                PassageiroEndereco::create([
                    'id_passageiro' => $passageiro->id_passageiro,
                    'id_endereco' => $residencia->id_endereco,
                    'tipo' => 'residencia',
                    'principal' => true,
                ]);

                PassageiroEndereco::create([
                    'id_passageiro' => $passageiro->id_passageiro,
                    'id_endereco' => $embarque->id_endereco,
                    'tipo' => 'embarque',
                    'principal' => true,
                ]);

                PassageiroEndereco::create([
                    'id_passageiro' => $passageiro->id_passageiro,
                    'id_endereco' => $desembarque->id_endereco,
                    'tipo' => 'desembarque',
                    'principal' => true,
                ]);

                Destino::create([
                    'id_endereco' => $destinoEndereco->id_endereco,
                    'nome' => $dadosEnderecos['destino_nome'],
                    'tipo' => $dadosEnderecos['destino_tipo'],
                    'ativo' => true,
                ]);
            });

            $request->session()->forget('cadastro_passageiro');

            return redirect()->route('responsavel.dashboard');
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['geral' => 'Erro ao cadastrar passageiro. Tente novamente.']);
        }
    }

    private function enriquecerCoordenadasFaltantes(array $dados): array
    {
        foreach (['residencia', 'embarque', 'desembarque', 'destino'] as $prefixo) {
            $latitude = $dados["{$prefixo}_latitude"] ?? null;
            $longitude = $dados["{$prefixo}_longitude"] ?? null;

            if ($latitude !== null && $longitude !== null) {
                continue;
            }

            $resultado = $this->geocodingService->geocodeAddress([
                'logradouro' => $dados["{$prefixo}_logradouro"] ?? null,
                'numero' => $dados["{$prefixo}_numero"] ?? null,
                'bairro' => $dados["{$prefixo}_bairro"] ?? null,
                'cidade' => $dados["{$prefixo}_cidade"] ?? null,
                'estado' => $dados["{$prefixo}_estado"] ?? null,
                'cep' => $dados["{$prefixo}_cep"] ?? null,
            ]);

            if ($resultado === null) {
                continue;
            }

            $dados["{$prefixo}_latitude"] ??= $resultado['latitude'];
            $dados["{$prefixo}_longitude"] ??= $resultado['longitude'];
        }

        return $dados;
    }

    private function criarEnderecoPorPrefixo(array $dados, string $prefixo): Endereco
    {
        return Endereco::create([
            'logradouro' => $dados["{$prefixo}_logradouro"],
            'numero' => $dados["{$prefixo}_numero"] ?? null,
            'complemento' => $dados["{$prefixo}_complemento"] ?? null,
            'bairro' => $dados["{$prefixo}_bairro"],
            'cidade' => $dados["{$prefixo}_cidade"],
            'estado' => $dados["{$prefixo}_estado"],
            'cep' => $dados["{$prefixo}_cep"],
            'latitude' => $dados["{$prefixo}_latitude"] ?? null,
            'longitude' => $dados["{$prefixo}_longitude"] ?? null,
        ]);
    }
}

