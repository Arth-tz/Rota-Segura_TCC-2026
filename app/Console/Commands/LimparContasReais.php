<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimparContasReais extends Command
{
    protected $signature = 'contas:limpar {--force : Executa sem confirmacao interativa}';
    protected $description = 'Remove contas de usuarios reais, mantendo apenas contas de teste e admin';

    private array $emailsTeste = [
        'admin@rotasegura.com',
        'ana.motorista@teste.rotasegura',
        'carlos.motorista@teste.rotasegura',
        'rodrigo.motorista@teste.rotasegura',
        'marcos.responsavel@teste.rotasegura',
        'fernanda.responsavel@teste.rotasegura',
        'patricia.responsavel@teste.rotasegura',
        'motorista@teste.rotasegura',
        'maria@rotasegura.com',
    ];

    public function handle(): int
    {
        $contasReais = Usuario::whereNotIn('email', $this->emailsTeste)->get();

        if ($contasReais->isEmpty()) {
            $this->info('Nenhuma conta real encontrada. Banco ja esta limpo.');
            return 0;
        }

        $this->warn("Contas que serao removidas ({$contasReais->count()}):");
        $this->table(
            ['ID', 'Email', 'Role'],
            $contasReais->map(fn($u) => [$u->id_usuario, $u->email, $u->role])->toArray()
        );

        if (!$this->option('force') && !$this->confirm('Confirma a remocao dessas contas e todos os dados vinculados?')) {
            $this->info('Operacao cancelada.');
            return 0;
        }

        DB::transaction(function () use ($contasReais) {
            foreach ($contasReais as $usuario) {
                $this->deletarUsuario($usuario);
            }
        });

        $this->info("Removidas {$contasReais->count()} conta(s) com sucesso.");
        return 0;
    }

    private function deletarUsuario(Usuario $usuario): void
    {
        if ($motorista = $usuario->motorista) {
            if ($van = $motorista->van) {
                foreach ($van->disponibilidades as $disp) {
                    $disp->dias()->delete();
                    $disp->delete();
                }
                $van->delete();
            }
            $motorista->delete();
        }

        if ($responsavel = $usuario->responsavel) {
            foreach ($responsavel->passageiros as $passageiro) {
                DB::table('passageiro_endereco')->where('id_passageiro', $passageiro->id_passageiro)->delete();
                $passageiro->delete();
            }
            DB::table('responsavel_passageiro')->where('id_responsavel', $responsavel->id_responsavel)->delete();
            $responsavel->delete();
        }

        $idPessoa = $usuario->id_pessoa;
        $usuario->delete();

        if ($idPessoa) {
            DB::table('pessoa')->where('id_pessoa', $idPessoa)->delete();
        }

        $this->line("  Removido: {$usuario->email}");
    }
}
