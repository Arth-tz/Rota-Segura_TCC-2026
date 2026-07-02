<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // A coluna id_usuario foi removida da tabela passageiro,
        // mas a trigger original ainda a referenciava — causando erro no INSERT.
        // Passageiros não têm login próprio, portanto a trigger é simplesmente removida.
        DB::statement("DROP TRIGGER IF EXISTS tr_passageiro_verificar_role");
    }

    public function down(): void
    {
        // Recria a trigger original (com o bug) ao reverter — apenas para rollback histórico.
        DB::statement("
            CREATE TRIGGER tr_passageiro_verificar_role
            BEFORE INSERT ON passageiro
            FOR EACH ROW
            BEGIN
                DECLARE u_role VARCHAR(50);
                IF NEW.id_usuario IS NOT NULL THEN
                    SELECT role INTO u_role FROM usuario WHERE id_usuario = NEW.id_usuario;
                    IF u_role != 'passageiro' THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Usuário de passageiro deve ter role passageiro';
                    END IF;
                END IF;
            END
        ");
    }
};
