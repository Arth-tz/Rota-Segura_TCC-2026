<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $usuario   = auth()->user()->load('motorista.van');
        $motorista = $usuario->motorista;

        return Inertia::render('Motorista/Dashboard', [
            'motorista' => $motorista,
            'van'       => $motorista?->van,
        ]);
    }
}
