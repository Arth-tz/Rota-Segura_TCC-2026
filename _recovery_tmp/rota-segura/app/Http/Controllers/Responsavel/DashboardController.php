<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response|\Illuminate\Http\RedirectResponse
    {
        $usuario     = auth()->user()->load('responsavel.passageiros');
        $responsavel = $usuario->responsavel;

        if (!$responsavel) {
            abort(403, 'Perfil de responsável não encontrado.');
        }

        $passageiros = $responsavel->passageiros;

        if ($passageiros->isEmpty()) {
            return redirect()->route('responsavel.passageiros.create');
        }

        return Inertia::render('Responsavel/Dashboard', [
            'passageiros' => $passageiros,
        ]);
    }
}
