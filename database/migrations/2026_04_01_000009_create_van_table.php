<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('van', function (Blueprint $table) {
            $table->bigIncrements('id_van');
            $table->unsignedBigInteger('id_motorista');
            $table->string('placa', 10)->unique();
            $table->string('modelo', 100)->nullable();
            $table->string('marca', 100)->nullable();
            $table->year('ano_fabricacao')->nullable();
            $table->string('cor', 50)->nullable();
            $table->unsignedInteger('capacidade_passageiros');
            $table->enum('status_aprovacao', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->enum('status_operacional', ['ativa', 'manutencao', 'inativa'])->default('ativa');
            $table->text('motivo_rejeicao')->nullable();
            $table->unsignedBigInteger('id_usuario_avaliador')->nullable();
            $table->timestamp('data_avaliacao')->nullable();
            $table->string('foto_url', 255)->nullable();
            $table->string('crlv_url', 255)->nullable();
            $table->date('crlv_validade')->nullable();
            $table->string('seguro_url', 255)->nullable();
            $table->date('seguro_validade')->nullable();
            $table->string('ipva_comprovante_url', 255)->nullable();
            $table->date('ipva_comprovante_data')->nullable();
            $table->boolean('documentacao_completa')->default(false);
            $table->date('data_ultima_inspecao')->nullable();
            $table->date('proxima_inspecao_prevista')->nullable();
            $table->timestamps();

            $table->foreign('id_motorista')->references('id_motorista')->on('motorista')->restrictOnDelete();
            $table->foreign('id_usuario_avaliador')->references('id_usuario')->on('usuario')->nullOnDelete();
            $table->index('status_aprovacao');
            $table->index('status_operacional');
            $table->index('crlv_validade');
            $table->index('documentacao_completa');
        });
    }
    public function down(): void { Schema::dropIfExists('van'); }
};
