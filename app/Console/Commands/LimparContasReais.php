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
        $contasReais = DB::table('usuario')
            ->whereNotIn('email', $this->emailsTeste)
            ->get(['id_usuario', 'email', 'role']);

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

        $ids   = $contasReais->pluck('id_usuario');
        $count = 0;

        DB::transaction(function () use ($ids, &$count) {
            foreach (Usuario::whereIn('id_usuario', $ids)->get() as $usuario) {
                $this->deletarUsuario($usuario);
                $count++;
            }
        });

        $this->info("Removidas {$count} conta(s) com sucesso.");
        return 0;
    }

    private function deletarUsuario(Usuario $usuario): void
    {
        $idUsuario = $usuario->id_usuario;
        $idPessoa  = $usuario->id_pessoa;

        if ($motorista = $usuario->motorista) {
            if ($van = $motorista->van) {
                $idVan = $van->id_van;

                // vinculo referencia van com RESTRICT; deletar antes
                // vinculo_disponibilidade cascadeia ao deletar vinculo
                DB::table('vinculo')->where('id_van', $idVan)->delete();

                // solicitacao referencia van com RESTRICT; deletar apos vinculo
                // solicitacao_disponibilidade cascadeia ao deletar solicitacao
                DB::table('solicitacao')->where('id_van', $idVan)->delete();

                // disponibilidade_dia cascadeia ao deletar disponibilidade
                DB::table('disponibilidade')->where('id_van', $idVan)->delete();

                $van->delete();
            }
            $motorista->delete();
        }

        if ($responsavel = $usuario->responsavel) {
            $passageiroIds = DB::table('responsavel_passageiro')
                ->where('id_responsavel', $responsavel->id_responsavel)
                ->pluck('id_passageiro');

            if ($passageiroIds->isNotEmpty()) {
                // vinculo e solicitacao referenciam passageiro com RESTRICT
                DB::table('vinculo')->whereIn('id_passageiro', $passageiroIds)->delete();
                DB::table('solicitacao')->whereIn('id_passageiro', $passageiroIds)->delete();
                DB::table('passageiro_endereco')->whereIn('id_passageiro', $passageiroIds)->delete();

                $pessoaIds = DB::table('passageiro')
                    ->whereIn('id_passageiro', $passageiroIds)
                    ->pluck('id_pessoa');

                DB::table('passageiro')->whereIn('id_passageiro', $passageiroIds)->delete();

                if ($pessoaIds->isNotEmpty()) {
                    DB::table('pessoa')->whereIn('id_pessoa', $pessoaIds)->delete();
                }
            }

            DB::table('responsavel_passageiro')
                ->where('id_responsavel', $responsavel->id_responsavel)
                ->delete();
            $responsavel->delete();
        }

        // Solicitacoes onde este usuario e solicitante (RESTRICT antes de deletar usuario)
        DB::table('solicitacao')->where('id_usuario_solicitante', $idUsuario)->delete();

        $usuario->delete();

        if ($idPessoa) {
            DB::table('pessoa')->where('id_pessoa', $idPessoa)->delete();
        }

        $this->line("  Removido: {$usuario->email}");
    }
}
