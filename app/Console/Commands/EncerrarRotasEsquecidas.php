<?php

namespace App\Console\Commands;

use App\Models\Rota;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class EncerrarRotasEsquecidas extends Command
{
    protected $signature   = 'rotas:encerrar-esquecidas
                                {--horas=3 : Horas sem GPS para considerar rota abandonada}
                                {--dry-run : Mostra o que seria encerrado sem alterar o banco}';

    protected $description = 'Encerra rotas em_andamento cujo GPS parou há mais de N horas';

    public function handle(): int
    {
        $horas  = (int) $this->option('horas');
        $dryRun = $this->option('dry-run');
        $limite = Carbon::now()->subHours($horas);

        // Rotas em_andamento iniciadas há mais de N horas
        $rotas = Rota::where('status', 'em_andamento')
            ->where('horario_inicio_real', '<', $limite)
            ->with(['paradas.passageiros'])
            ->get();

        if ($rotas->isEmpty()) {
            $this->info('Nenhuma rota esquecida encontrada.');
            return self::SUCCESS;
        }

        $this->table(
            ['id_rota', 'id_van', 'Iniciou em', 'Última posição GPS'],
            $rotas->map(fn($r) => [
                $r->id_rota,
                $r->id_van,
                $r->horario_inicio_real?->format('d/m H:i'),
                $r->ultimaLocalizacao?->timestamp_captura
                    ? Carbon::parse($r->ultimaLocalizacao->timestamp_captura)->format('d/m H:i')
                    : 'sem GPS',
            ])
        );

        if ($dryRun) {
            $this->warn("--dry-run ativo: nenhuma alteração feita.");
            return self::SUCCESS;
        }

        $agora = Carbon::now();

        foreach ($rotas as $rota) {
            // Marca passageiros pendentes como encerrados pelo sistema
            foreach ($rota->paradas as $parada) {
                foreach ($parada->passageiros as $passageiro) {
                    $pivot = $passageiro->pivot;

                    if ($parada->tipo === 'embarque' && is_null($pivot->embarque_em)) {
                        $parada->passageiros()->updateExistingPivot($passageiro->id_passageiro, [
                            'embarque_em'         => $agora,
                            'marcado_por'         => 'sistema',
                            'metodo_confirmacao'  => 'manual',
                        ]);
                    } elseif ($parada->tipo === 'desembarque' && is_null($pivot->desembarque_em)) {
                        $parada->passageiros()->updateExistingPivot($passageiro->id_passageiro, [
                            'desembarque_em'      => $agora,
                            'marcado_por'         => 'sistema',
                            'metodo_confirmacao'  => 'manual',
                        ]);
                    }
                }
            }

            // Encerra a rota
            $rota->update([
                'status'                   => 'concluida',
                'horario_fim_real'         => $agora,
                'observacoes'              => trim(($rota->observacoes ?? '') .
                    "\n[Sistema] Encerrada automaticamente após {$horas}h sem atividade."),
            ]);

            $this->line("  ✓ Rota #{$rota->id_rota} encerrada (van {$rota->id_van})");
        }

        $this->info("{$rotas->count()} rota(s) encerrada(s).");
        return self::SUCCESS;
    }
}
