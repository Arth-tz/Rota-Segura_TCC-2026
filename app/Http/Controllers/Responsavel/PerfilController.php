<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class PerfilController extends Controller
{
    public function edit(): Response
    {
        $usuario    = auth()->user()->load('pessoa', 'responsavel');
        $responsavel = $usuario->responsavel;

        return Inertia::render('Responsavel/Perfil/Editar', [
            'dados' => [
                'nome'               => $usuario->pessoa?->nome,
                'cpf'                => $usuario->pessoa?->cpf,
                'data_nascimento'    => $usuario->pessoa?->data_nascimento?->format('Y-m-d'),
                'email'              => $usuario->email,
                'telefone'           => $usuario->pessoa?->telefone,
                'tipo_responsavel'   => $responsavel?->tipo_responsavel,
                'telefone_emergencia'=> $responsavel?->telefone_emergencia,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $usuario     = auth()->user()->load('pessoa', 'responsavel');
        $responsavel = $usuario->responsavel;

        $dados = $request->validate([
            'nome'                => 'required|string|max:150',
            'telefone'            => 'nullable|string|max:20',
            'email'               => 'required|email|max:150|unique:usuario,email,' . $usuario->id_usuario . ',id_usuario',
            'telefone_emergencia' => 'nullable|string|max:20',
            'senha'               => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'email.unique'    => 'Este e-mail já está em uso.',
            'senha.min'       => 'A senha deve ter pelo menos 8 caracteres.',
            'senha.confirmed' => 'As senhas não coincidem.',
        ]);

        $usuario->pessoa->update([
            'nome'     => $dados['nome'],
            'telefone' => $dados['telefone'] ? preg_replace('/\D/', '', $dados['telefone']) : null,
        ]);

        $responsavel?->update([
            'telefone_emergencia' => $dados['telefone_emergencia']
                ? preg_replace('/\D/', '', $dados['telefone_emergencia'])
                : null,
        ]);

        $usuario->email = $dados['email'];

        if (!empty($dados['senha'])) {
            $usuario->senha_hash = Hash::make($dados['senha']);
        }

        $usuario->save();

        return redirect()->route('responsavel.perfil.edit')
            ->with('sucesso', 'Perfil atualizado com sucesso!');
    }
}
