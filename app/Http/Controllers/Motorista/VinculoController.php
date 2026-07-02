<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\Vinculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VinculoController extends Controller
{
    public function encerrar(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'motivo_encerramento' => 'nullable|string|max:500',
        ]);

        $van = auth()->user()->motorista?->van;

        if (!$van) {
            return back()->withErrors(['geral' => 'Van não encontrada.']);
        }

        $vinculo = Vinculo::where('id_van', $van->id_van)
            ->where('status', 'ativo')
            ->findOrFail($id);

        $vinculo->update([
            'status'       => 'encerrado',
            'data_fim'     => now()->toDateString(),
            'observacoes'  => $validated['motivo_encerramento'] ?? null,
        ]);

        return back()->with('sucesso', 'Vínculo encerrado.');
    }
}
