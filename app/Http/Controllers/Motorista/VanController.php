<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\Van;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class VanController extends Controller
{
    public function create(): Response
    {
        $motorista = auth()->user()->motorista;

        if ($motorista && $motorista->van) {
            return redirect()->route('motorista.dashboard')
                ->with('aviso', 'Você já possui uma van cadastrada.');
        }

        return Inertia::render('Motorista/Van/Criar');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'placa'                 => ['required', 'string', 'max:10', 'unique:van,placa', 'regex:/^[A-Z]{3}[0-9][A-Z0-9][0-9]{2}$/'],
            'nome_servico'          => ['nullable', 'string', 'max:150'],
            'marca'                 => ['required', 'string', 'max:100'],
            'modelo'                => ['required', 'string', 'max:100'],
            'ano_fabricacao'        => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'cor'                   => ['required', 'string', 'max:50'],
            'capacidade_passageiros'=> ['required', 'integer', 'min:1', 'max:30'],
            'foto'                  => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'placa.required'                  => 'A placa é obrigatória.',
            'placa.unique'                    => 'Esta placa já está cadastrada.',
            'placa.regex'                     => 'Placa inválida. Use o formato ABC1234 ou ABC1D23.',
            'nome_servico.max'                => 'O nome do serviço pode ter no máximo 150 caracteres.',
            'marca.required'                  => 'A marca é obrigatória.',
            'modelo.required'                 => 'O modelo é obrigatório.',
            'ano_fabricacao.required'         => 'O ano de fabricação é obrigatório.',
            'ano_fabricacao.min'              => 'Ano de fabricação inválido (mínimo 1990).',
            'ano_fabricacao.max'              => 'Ano de fabricação inválido.',
            'cor.required'                    => 'A cor é obrigatória.',
            'capacidade_passageiros.required' => 'A capacidade é obrigatória.',
            'capacidade_passageiros.min'      => 'Capacidade mínima: 1 passageiro.',
            'capacidade_passageiros.max'      => 'Capacidade máxima: 30 passageiros.',
        ]);

        $motorista = auth()->user()->motorista;

        if (!$motorista) {
            return back()->withErrors(['geral' => 'Perfil de motorista não encontrado.']);
        }

        if ($motorista->van) {
            return redirect()->route('motorista.dashboard')
                ->with('aviso', 'Você já possui uma van cadastrada.');
        }

        $van = DB::transaction(function () use ($dados, $motorista) {
            return Van::create([
                'id_motorista'          => $motorista->id_motorista,
                'placa'                 => strtoupper($dados['placa']),
                'nome_servico'          => $dados['nome_servico'] ?? null,
                'marca'                 => $dados['marca'],
                'modelo'                => $dados['modelo'],
                'ano_fabricacao'        => $dados['ano_fabricacao'],
                'cor'                   => $dados['cor'],
                'capacidade_passageiros'=> $dados['capacidade_passageiros'],
                'status_aprovacao'      => 'pendente',
                'status_operacional'    => 'ativa',
                'documentacao_completa' => false,
            ]);
        });

        if ($request->hasFile('foto')) {
            $ext  = $request->file('foto')->getClientOriginalExtension();
            $path = $request->file('foto')->storeAs("vans/{$van->id_van}", "foto.{$ext}", 'public');
            $van->update(['foto_url' => Storage::disk('public')->url($path)]);
        }

        return redirect()->route('motorista.dashboard')
            ->with('sucesso', 'Van cadastrada com sucesso! Aguarde a aprovação do administrador.');
    }

    public function edit(): Response|RedirectResponse
    {
        $motorista = auth()->user()->motorista;
        $van = $motorista?->van;

        if (!$van) {
            return redirect()->route('motorista.van.create')
                ->with('aviso', 'Cadastre sua van primeiro.');
        }

        return Inertia::render('Motorista/Van/Editar', [
            'van' => [
                'id_van'                => $van->id_van,
                'placa'                 => $van->placa,
                'nome_servico'          => $van->nome_servico,
                'marca'                 => $van->marca,
                'modelo'                => $van->modelo,
                'ano_fabricacao'        => $van->ano_fabricacao,
                'cor'                   => $van->cor,
                'capacidade_passageiros'=> $van->capacidade_passageiros,
                'foto_url'              => $van->foto_url,
                'status_aprovacao'      => $van->status_aprovacao,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $motorista = auth()->user()->motorista;
        $van = $motorista?->van;

        if (!$van) {
            return redirect()->route('motorista.van.create');
        }

        $dados = $request->validate([
            'nome_servico'          => ['nullable', 'string', 'max:150'],
            'marca'                 => ['required', 'string', 'max:100'],
            'modelo'                => ['required', 'string', 'max:100'],
            'ano_fabricacao'        => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'cor'                   => ['required', 'string', 'max:50'],
            'capacidade_passageiros'=> ['required', 'integer', 'min:1', 'max:30'],
            'foto'                  => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $van->update([
            'nome_servico'          => $dados['nome_servico'] ?? null,
            'marca'                 => $dados['marca'],
            'modelo'                => $dados['modelo'],
            'ano_fabricacao'        => $dados['ano_fabricacao'],
            'cor'                   => $dados['cor'],
            'capacidade_passageiros'=> $dados['capacidade_passageiros'],
        ]);

        if ($request->hasFile('foto')) {
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                Storage::disk('public')->delete("vans/{$van->id_van}/foto.{$ext}");
            }
            $ext  = $request->file('foto')->getClientOriginalExtension();
            $path = $request->file('foto')->storeAs("vans/{$van->id_van}", "foto.{$ext}", 'public');
            $van->update(['foto_url' => Storage::disk('public')->url($path)]);
        }

        return redirect()->route('motorista.van.edit')
            ->with('sucesso', 'Van atualizada com sucesso!');
    }

    public function uploadFoto(Request $request): RedirectResponse
    {
        $motorista = auth()->user()->motorista;
        $van = $motorista?->van;

        if (!$van) {
            return redirect()->route('motorista.dashboard');
        }

        $request->validate([
            'foto' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ]);

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            Storage::disk('public')->delete("vans/{$van->id_van}/foto.{$ext}");
        }

        $ext  = $request->file('foto')->getClientOriginalExtension();
        $path = $request->file('foto')->storeAs("vans/{$van->id_van}", "foto.{$ext}", 'public');
        $van->update(['foto_url' => Storage::disk('public')->url($path)]);

        return back()->with('sucesso', 'Foto atualizada com sucesso!');
    }
}
