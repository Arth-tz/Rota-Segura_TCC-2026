<?php

namespace App\Http\Controllers\Responsavel;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class AlunoController extends Controller
{
    public function create(){
        return Inertia::render('Responsavel/Aluno/create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'first_name'           => 'required|string|max:255',
            'last_name'            => 'required|string|max:255',
            'data_nascimento'      => 'required|date',
            'escola_nome'          => 'required|string|max:255',
            'escola_endereco'      => 'required|string|max:255',
            'turno'                => 'required|in:manha,tarde,noite',
            'serie_ano'            => 'required|string|max:50',
            'endereco_embarque'    => 'required|string|max:255',
            'endereco_desembarque' => 'required|string|max:255',
            'obs_medica'           => 'nullable|string',
            'ctt_emergencia'       => 'nullable|string|max:255',
            'tel_emergencia'       => 'nullable|string|max:20',
        ]);

        try {
            DB::transaction(function () use ($validated) {
            $responsavel = auth()->user()->responsavel;
            $responsavel->alunos()->create($validated);
        });

            return redirect()->route('responsavel.dashboard');

        } catch (\Exception $e) {
            return back()->withErrors(['geral' => 'Erro ao cadastrar aluno. Tente novamente.']);
        }
    }
}

