<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::statement("
            CREATE EVENT IF NOT EXISTS evt_limpar_localizacao_antiga
            ON SCHEDULE EVERY 1 DAY
            DO
            BEGIN
                DELETE FROM localizacao
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
            END
        ");

        DB::statement("
            CREATE OR REPLACE VIEW vw_vans_disponiveis AS
            SELECT
                v.id_van,
                v.placa,
                m.id_usuario as id_motorista,
                p.nome as nome_motorista,
                p.foto_url as foto_motorista,
                v.modelo,
                v.capacidade_passageiros,
                d.id_disponibilidade,
                d.nome as nome_trajeto,
                d.turno,
                d.preco_mensal,
                d.capacidade_total,
                GROUP_CONCAT(DISTINCT dd.dia_semana ORDER BY FIELD(dd.dia_semana,'seg','ter','qua','qui','sex','sab','dom') SEPARATOR ',') as dias_semana,
                COUNT(DISTINCT vi.id_vinculo) as passageiros_confirmados,
                d.capacidade_total - COUNT(DISTINCT vi.id_vinculo) as vagas_disponiveis
            FROM van v
            JOIN motorista m ON v.id_motorista = m.id_motorista
            JOIN usuario u ON m.id_usuario = u.id_usuario
            JOIN pessoa p ON u.id_pessoa = p.id_pessoa
            JOIN disponibilidade d ON v.id_van = d.id_van
            JOIN disponibilidade_dia dd ON d.id_disponibilidade = dd.id_disponibilidade
            LEFT JOIN vinculo_disponibilidade vd ON d.id_disponibilidade = vd.id_disponibilidade
            LEFT JOIN vinculo vi ON vd.id_vinculo = vi.id_vinculo AND vi.status = 'ativo'
            WHERE v.status_operacional = 'ativa'
            AND v.documentacao_completa = true
            AND d.ativa = true
            GROUP BY v.id_van, d.id_disponibilidade
        ");

        DB::statement("
            CREATE OR REPLACE VIEW vw_vinculo_detalhes AS
            SELECT
                vi.id_vinculo,
                vi.id_van,
                van.placa,
                vi.id_passageiro,
                p.nome as nome_passageiro,
                p.telefone,
                m.id_usuario as id_motorista,
                pm.nome as nome_motorista,
                pm.telefone as telefone_motorista,
                pm.foto_url as foto_motorista,
                vi.status,
                vi.data_inicio,
                vi.data_fim
            FROM vinculo vi
            JOIN van ON vi.id_van = van.id_van
            JOIN motorista m ON van.id_motorista = m.id_motorista
            JOIN usuario u ON m.id_usuario = u.id_usuario
            JOIN pessoa pm ON u.id_pessoa = pm.id_pessoa
            JOIN passageiro pas ON vi.id_passageiro = pas.id_passageiro
            JOIN pessoa p ON pas.id_pessoa = p.id_pessoa
        ");

        DB::statement("
            CREATE OR REPLACE VIEW vw_responsavel_passageiros AS
            SELECT
                r.id_responsavel,
                u.id_usuario,
                pr.nome as nome_responsavel,
                u.email,
                pas.id_passageiro,
                pp.nome as nome_passageiro,
                pp.data_nascimento,
                rp.data_inicio,
                rp.data_fim
            FROM responsavel r
            JOIN usuario u ON r.id_usuario = u.id_usuario
            JOIN pessoa pr ON u.id_pessoa = pr.id_pessoa
            JOIN responsavel_passageiro rp ON r.id_responsavel = rp.id_responsavel
            JOIN passageiro pas ON rp.id_passageiro = pas.id_passageiro
            JOIN pessoa pp ON pas.id_pessoa = pp.id_pessoa
            WHERE rp.data_fim IS NULL OR rp.data_fim >= CURDATE()
        ");
    }

    public function down(): void {
        DB::statement("DROP EVENT IF EXISTS evt_limpar_localizacao_antiga");
        DB::statement("DROP VIEW IF EXISTS vw_vans_disponiveis");
        DB::statement("DROP VIEW IF EXISTS vw_vinculo_detalhes");
        DB::statement("DROP VIEW IF EXISTS vw_responsavel_passageiros");
    }
};
