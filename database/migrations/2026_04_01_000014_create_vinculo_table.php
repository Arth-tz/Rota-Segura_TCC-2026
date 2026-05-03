<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vinculo', function (Blueprint $table) {
            $table->bigIncrements('id_vinculo');
            $table->unsignedBigInteger('id_van');
            $table->unsignedBigInteger('id_passageiro');
            $table->unsignedBigInteger('id_solicitacao')->unique();
            $table->enum('status', ['ativo', 'suspenso', 'encerrado'])->default('ativo');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('id_van')->references('id_van')->on('van')->restrictOnDelete();
            $table->foreign('id_passageiro')->references('id_passageiro')->on('passageiro')->restrictOnDelete();
            $table->foreign('id_solicitacao')->references('id_solicitacao')->on('solicitacao')->restrictOnDelete();
            $table->index(['id_passageiro', 'status']);
            $table->index('status');
        });
    }
    public function down(): void { Schema::dropIfExists('vinculo'); }
};
