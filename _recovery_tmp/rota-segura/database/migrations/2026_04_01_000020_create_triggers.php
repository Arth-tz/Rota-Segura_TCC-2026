<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::statement("
            CREATE TRIGGER tr_motorista_verificar_role
            BEFORE INSERT ON motorista
            FOR EACH ROW
            BEGIN
                DECLARE u_role VARCHAR(50);
                SELECT role INTO u_role FROM usuario WHERE id_usuario = NEW.id_usuario;
                IF u_role != 'motorista' THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Usuário deve ter role motorista';
                END IF;
            END
        ");

        DB::statement("
            CREATE TRIGGER tr_responsavel_verificar_role
            BEFORE INSERT ON responsavel
            FOR EACH ROW
            BEGIN
                DECLARE u_role VARCHAR(50);
                SELECT role INTO u_role FROM usuario WHERE id_usuario = NEW.id_usuario;
                IF u_role != 'responsavel' THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Usuário deve ter role responsavel';
                END IF;
            END
        ");

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

        DB::statement("
            CREATE TRIGGER tr_vinculo_verificar_ativo_duplicado
            BEFORE INSERT ON vinculo
            FOR EACH ROW
            BEGIN
                DECLARE contador INT;
                IF NEW.status = 'ativo' THEN
                    SELECT COUNT(*) INTO contador
                    FROM vinculo
                    WHERE id_passageiro = NEW.id_passageiro
                    AND status = 'ativo';
                    IF contador > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Passageiro já possui vínculo ativo';
                    END IF;
                END IF;
            END
        ");

        DB::statement("
            CREATE TRIGGER tr_van_calcular_documentacao_completa
            BEFORE UPDATE ON van
            FOR EACH ROW
            BEGIN
                SET NEW.documentacao_completa = (
                    NEW.crlv_url IS NOT NULL
                    AND NEW.crlv_validade IS NOT NULL
                    AND NEW.crlv_validade > CURDATE()
                );
            END
        ");
    }

    public function down(): void {
        DB::statement("DROP TRIGGER IF EXISTS tr_motorista_verificar_role");
        DB::statement("DROP TRIGGER IF EXISTS tr_responsavel_verificar_role");
        DB::statement("DROP TRIGGER IF EXISTS tr_passageiro_verificar_role");
        DB::statement("DROP TRIGGER IF EXISTS tr_vinculo_verificar_ativo_duplicado");
        DB::statement("DROP TRIGGER IF EXISTS tr_van_calcular_documentacao_completa");
    }
};
