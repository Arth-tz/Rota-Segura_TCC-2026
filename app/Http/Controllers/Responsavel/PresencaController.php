<?php

namespace App\Http\Controllers\Responsavel;

use App\Http\Controllers\Controller;
use App\Models\DisponibilidadePassageiro;
use App\Models\Vinculo;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PresencaController extends Controller
{
    private const MAPA_DIA = ['0'=>'dom','1'=>'seg','2'=>'ter','3'=>'qua','4'=>'qui','5'=>'sex','6'=>'sab'];

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_vinculo'   => 'required|integer',
            'data_falta'   => 'required|date|after_or_equal:today',
            'motivo_falta' => 'nullable|in:doenca,feriado,viagem,evento,outro',
        ], [
            'data_falta.after_or_equal' => 'Só é possível marcar falta para hoje ou datas futuras.',
        ]);

        $responsavel = auth()->user()->responsavel;

        // Garante que o vínculo pertence a um passageiro deste responsável
        $vinculo = Vinculo::where('id_vinculo', $validated['id_vinculo'])
            ->where('status', 'ativo')
            ->whereHas('passageiro.responsaveis', fn ($q) =>
                $q->where('responsavel.id_responsavel', $responsavel->id_responsavel)
            )
            ->with('disponibilidades')
            ->firstOrFail();

        // Valida que o dia da semana está nos dias_contratados do vínculo
        $diaAbrev       = self::MAPA_DIA[Carbon::parse($validated['data_falta'])->format('w')];
        $diasContratados = collect();

        foreach ($vinculo->disponibilidades as $disp) {
            $dias = json_decode($disp->pivot->dias_contratados ?? '[]', true);
            $diasContratados = $diasContratados->merge($dias);
        }

        if (!$diasContratados->contains($diaAbrev)) {
            return back()->withErrors(['data_falta' => 'Este dia não faz parte do trajeto contratado.']);
        }

        DisponibilidadePassageiro::updateOrCreate(
            [
                'id_passageiro' => $vinculo->id_passageiro,
                'id_vinculo'    => $vinculo->id_vinculo,
                'data'          => $validated['data_falta'],
            ],
            [
                'vai'          => false,
                'motivo_falta' => $validated['motivo_falta'] ?? null,
                'marcado_por'  => auth()->id(),
            ]
        );

        return back()->with('sucesso', 'Falta marcada com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $responsavel = auth()->user()->responsavel;

        $presenca = DisponibilidadePassageiro::where('id_disponibilidade_passageiro', $id)
            ->whereHas('vinculo.passageiro.responsaveis', fn ($q) =>
                $q->where('responsavel.id_responsavel', $responsavel->id_responsavel)
            )
            ->firstOrFail();

        $presenca->delete();

        return back()->with('sucesso', 'Falta cancelada.');
    }
}
