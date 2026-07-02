<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dias que o responsável escolheu na solicitação (subconjunto dos dias da disponibilidade)
        Schema::table('solicitacao_disponibilidade', function (Blueprint $table) {
            $table->json('dias_contratados')->nullable()->after('preco_mensal');
        });

        // Dias efetivamente contratados no vínculo (confirmados pelo motorista)
        Schema::table('vinculo_disponibilidade', function (Blueprint $table) {
            $table->json('dias_contratados')->nullable()->after('preco_mensal');
        });

        // Permite um passageiro ter registros de presença em dois vínculos no mesmo dia
        // (ex.: manhã com van A e tarde com van B) — remove unique(passageiro, data),
        // adiciona unique(passageiro, vinculo, data)
        Schema::table('disponibilidade_passageiro', function (Blueprint $table) {
            $table->dropUnique('disponibilidade_passageiro_id_passageiro_data_unique');
            $table->unique(['id_passageiro', 'id_vinculo', 'data'], 'dp_passageiro_vinculo_data_unique');
        });

        // Corrige enum turno: substitui 'noite' por 'integral'
        DB::statement("ALTER TABLE disponibilidade MODIFY COLUMN turno ENUM('manha','tarde','integral') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('solicitacao_disponibilidade', function (Blueprint $table) {
            $table->dropColumn('dias_contratados');
        });

        Schema::table('vinculo_disponibilidade', function (Blueprint $table) {
            $table->dropColumn('dias_contratados');
        });

        Schema::table('disponibilidade_passageiro', function (Blueprint $table) {
            $table->dropUnique('dp_passageiro_vinculo_data_unique');
            $table->unique(['id_passageiro', 'data'], 'disponibilidade_passageiro_id_passageiro_data_unique');
        });

        DB::statement("ALTER TABLE disponibilidade MODIFY COLUMN turno ENUM('manha','tarde','noite') NOT NULL");
    }
};
