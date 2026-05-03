<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\Passageiro;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PassageiroController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Responsavel/Passageiro/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome'            => 'required|string|min:2|max:255',
            'cpf'             => 'required|cpf|unique:pessoa,cpf',
            'data_nascimento' => 'required|date|before:today',
            'obs_medica'      => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $responsavel = auth()->user()->responsavel;

                if (!$responsavel) {
                    throw new \RuntimeException('Perfil de responsável não encontrado.');
                }

                $pessoa = Pessoa::create([
                    'nome'            => trim($validated['nome']),
                    'cpf'             => preg_replace('/\D/', '', $validated['cpf']),
                    'data_nascimento' => $validated['data_nascimento'],
                    'telefone'        => null,
                ]);

                $passageiro = Passageiro::create([
                    'id_pessoa'           => $pessoa->id_pessoa,
                    'observacoes_medicas' => $validated['obs_medica'] ?? null,
                    'ativo'               => true,
                    'data_inscricao'      => now()->toDateString(),
                ]);

                $responsavel->passageiros()->attach($passageiro->id_passageiro, [
                    'data_inicio' => now()->toDateString(),
                    'data_fim'    => null,
                ]);
            });

            return redirect()->route('responsavel.dashboard');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['geral' => 'Erro ao cadastrar passageiro. Tente novamente.']);
        }
    }
}
