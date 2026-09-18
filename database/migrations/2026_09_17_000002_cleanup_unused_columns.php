<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // van
        Schema::table('van', function (Blueprint $table) {
            $table->dropIndex(['status_operacional']);
            $table->dropColumn([
                'status_operacional',
                'ipva_comprovante_url',
                'ipva_comprovante_data',
                'data_ultima_inspecao',
                'proxima_inspecao_prevista',
            ]);
        });

        // motorista
        Schema::table('motorista', function (Blueprint $table) {
            $table->dropColumn('renach_url');
        });

        // solicitacao
        Schema::table('solicitacao', function (Blueprint $table) {
            $table->dropForeign(['cancelado_por']);
            $table->dropColumn(['cancelado_em', 'cancelado_por']);
        });

        // rota
        Schema::table('rota', function (Blueprint $table) {
            $table->dropColumn(['distancia_km', 'tempo_decorrido_minutos']);
        });

        // localizacao
        Schema::table('localizacao', function (Blueprint $table) {
            $table->dropColumn(['altitude', 'numero_satelites']);
        });

        // responsavel
        Schema::table('responsavel', function (Blueprint $table) {
            $table->dropColumn('data_responsavel_ate');
        });

        // parada_passageiro
        Schema::table('parada_passageiro', function (Blueprint $table) {
            $table->dropColumn('metodo_confirmacao');
        });

        // Recriar a view sem o filtro status_operacional
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
            WHERE v.documentacao_completa = true
            AND d.ativa = true
            GROUP BY v.id_van, d.id_disponibilidade
        ");
    }

    public function down(): void
    {
        Schema::table('van', function (Blueprint $table) {
            $table->enum('status_operacional', ['ativa', 'manutencao', 'inativa'])->default('ativa');
            $table->string('ipva_comprovante_url', 255)->nullable();
            $table->date('ipva_comprovante_data')->nullable();
            $table->date('data_ultima_inspecao')->nullable();
            $table->date('proxima_inspecao_prevista')->nullable();
        });

        Schema::table('motorista', function (Blueprint $table) {
            $table->string('renach_url', 500)->nullable();
        });

        Schema::table('solicitacao', function (Blueprint $table) {
            $table->dateTime('cancelado_em')->nullable();
            $table->unsignedBigInteger('cancelado_por')->nullable();
            $table->foreign('cancelado_por')->references('id_usuario')->on('usuario')->nullOnDelete();
        });

        Schema::table('rota', function (Blueprint $table) {
            $table->decimal('distancia_km', 10, 2)->nullable();
            $table->unsignedInteger('tempo_decorrido_minutos')->nullable();
        });

        Schema::table('localizacao', function (Blueprint $table) {
            $table->decimal('altitude', 8, 2)->nullable();
            $table->unsignedInteger('numero_satelites')->nullable();
        });

        Schema::table('responsavel', function (Blueprint $table) {
            $table->date('data_responsavel_ate')->nullable();
        });

        Schema::table('parada_passageiro', function (Blueprint $table) {
            $table->enum('metodo_confirmacao', ['manual', 'geo', 'qrcode'])->nullable();
        });
    }
};
