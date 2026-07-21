<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class PerfilController extends Controller
{
    public function edit(): Response
    {
        $usuario  = auth()->user()->load('pessoa', 'motorista');
        $motorista = $usuario->motorista;

        return Inertia::render('Motorista/Perfil/Editar', [
            'dados' => [
                'nome'             => $usuario->pessoa?->nome,
                'cpf'              => $usuario->pessoa?->cpf,
                'data_nascimento'  => $usuario->pessoa?->data_nascimento?->format('Y-m-d'),
                'email'            => $usuario->email,
                'telefone'         => $usuario->pessoa?->telefone,
                'foto_url'         => $usuario->pessoa?->foto_url,
                'cnh_numero'       => $motorista?->cnh_numero,
                'cnh_categoria'    => $motorista?->cnh_categoria,
                'cnh_validade'     => $motorista?->cnh_validade?->format('Y-m-d'),
                'status_aprovacao' => $motorista?->status_aprovacao,
                'motivo_rejeicao'  => $motorista?->motivo_rejeicao,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $usuario  = auth()->user()->load('pessoa', 'motorista');

        $dados = $request->validate([
            'nome'      => 'required|string|max:150',
            'telefone'  => 'nullable|string|max:20',
            'email'     => 'required|email|max:150|unique:usuario,email,' . $usuario->id_usuario . ',id_usuario',
            'senha'     => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'email.unique'     => 'Este e-mail já está em uso.',
            'senha.min'        => 'A senha deve ter pelo menos 8 caracteres.',
            'senha.confirmed'  => 'As senhas não coincidem.',
        ]);

        $usuario->pessoa->update([
            'nome'     => $dados['nome'],
            'telefone' => $dados['telefone'] ? preg_replace('/\D/', '', $dados['telefone']) : null,
        ]);

        $usuario->email = $dados['email'];

        if (!empty($dados['senha'])) {
            $usuario->senha_hash = Hash::make($dados['senha']);
        }

        $usuario->save();

        return redirect()->route('motorista.perfil.edit')
            ->with('sucesso', 'Perfil atualizado com sucesso!');
    }

    public function uploadFoto(Request $request): RedirectResponse
    {
        $usuario   = auth()->user()->load('pessoa', 'motorista');
        $motorista = $usuario->motorista;

        $request->validate([
            'foto' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'foto.required' => 'Selecione uma imagem.',
            'foto.image'    => 'O arquivo deve ser uma imagem.',
            'foto.max'      => 'A imagem deve ter no máximo 2 MB.',
        ]);

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            Storage::disk('public')->delete("motoristas/{$motorista->id_motorista}/foto.{$ext}");
        }

        $ext  = $request->file('foto')->getClientOriginalExtension();
        $path = $request->file('foto')->storeAs("motoristas/{$motorista->id_motorista}", "foto.{$ext}", 'public');
        $usuario->pessoa->update(['foto_url' => Storage::disk('public')->url($path)]);

        return back()->with('sucesso', 'Foto atualizada com sucesso!');
    }
}
