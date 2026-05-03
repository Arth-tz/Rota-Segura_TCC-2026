<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\Usuario;
use App\Enums\UserRole;
use App\Http\Requests\Auth\RegisterResponsavelRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RegisterResponsavelController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/RegisterResponsavel');
    }

    public function store(RegisterResponsavelRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {

                $pessoa = Pessoa::create([
                    'nome'            => $request->nome,
                    'cpf'             => $request->cpf,
                    'data_nascimento' => $request->data_nascimento,
                    'telefone'        => $request->telefone,
                ]);

                $usuario = Usuario::create([
                    'id_pessoa'  => $pessoa->id_pessoa,
                    'email'      => $request->email,
                    'senha_hash' => Hash::make($request->password),
                    'role'       => UserRole::Responsavel,
                ]);

                $usuario->responsavel()->create([
                    'tipo_responsavel' => 'representante_legal',
                ]);

                Auth::login($usuario);
            });

            return redirect()->route('responsavel.passageiros.create');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['geral' => 'Erro ao criar conta. Tente novamente.']);
        }
    }
}
