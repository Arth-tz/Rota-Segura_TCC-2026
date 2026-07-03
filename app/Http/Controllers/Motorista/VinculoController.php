<?php

namespace App\Http\Controllers\Motorista;

use App\Http\Controllers\Controller;
use App\Models\Vinculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VinculoController extends Controller
{
    public function reordenar(Request $request, int $idDisponibilidade): JsonResponse
    {
        $validated = $request->validate([
            'ordem'   => 'required|array|min:1',
            'ordem.*' => 'required|integer',
        ]);

        $van = auth()->user()->motorista?->van;
        if (!$van) {
            return response()->json(['error' => 'Van não encontrada.'], 403);
        }

        // Garante que todos os vínculos pertencem à van deste motorista
        $idsVinculos = $validated['ordem'];
        $count = Vinculo::where('id_van', $van->id_van)
            ->whereIn('id_vinculo', $idsVinculos)
            ->count();

        if ($count !== count($idsVinculos)) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        // Atualiza a ordem de cada vínculo no pivot
        foreach ($idsVinculos as $posicao => $idVinculo) {
            DB::table('vinculo_disponibilidade')
                ->where('id_vinculo', $idVinculo)
                ->where('id_disponibilidade', $idDisponibilidade)
                ->update(['ordem' => $posicao + 1]);
        }

        return response()->json(['sucesso' => true]);
    }

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
