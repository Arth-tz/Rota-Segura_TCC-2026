<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('solicitacao', function (Blueprint $table) {
            $table->bigIncrements('id_solicitacao');
            $table->unsignedBigInteger('id_van');
            $table->unsignedBigInteger('id_passageiro');
            $table->unsignedBigInteger('id_responsavel')->nullable();
            $table->unsignedBigInteger('id_usuario_solicitante');
            $table->enum('tipo_solicitante', ['responsavel', 'passageiro', 'admin'])->default('responsavel');
            $table->enum('status', ['pendente', 'aceita', 'recusada', 'cancelada'])->default('pendente');
            $table->text('mensagem')->nullable();
            $table->text('motivo_recusa')->nullable();
            $table->dateTime('data_solicitacao')->useCurrent();
            $table->dateTime('data_resposta')->nullable();
            $table->dateTime('cancelado_em')->nullable();
            $table->unsignedBigInteger('cancelado_por')->nullable();
            $table->timestamps();

            $table->foreign('id_van')->references('id_van')->on('van')->restrictOnDelete();
            $table->foreign('id_passageiro')->references('id_passageiro')->on('passageiro')->restrictOnDelete();
            $table->foreign('id_responsavel')->references('id_responsavel')->on('responsavel')->nullOnDelete();
            $table->foreign('id_usuario_solicitante')->references('id_usuario')->on('usuario')->restrictOnDelete();
            $table->foreign('cancelado_por')->references('id_usuario')->on('usuario')->nullOnDelete();
            $table->index(['id_van', 'status']);
            $table->index(['id_responsavel', 'status']);
            $table->index('data_solicitacao');
        });
    }
    public function down(): void { Schema::dropIfExists('solicitacao'); }
};
