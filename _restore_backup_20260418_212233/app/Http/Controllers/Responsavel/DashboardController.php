<?php

namespace App\Http\Controllers\Responsavel;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('responsavel.alunos');
        $responsavel = $user->responsavel;
        $alunos = $responsavel->alunos;

        // Se não tem alunos, manda para cadastro
        if ($alunos->isEmpty()) {
            return redirect()->route('responsavel.alunos.create');
        }

        return Inertia::render('Responsavel/Dashboard');
    }
}