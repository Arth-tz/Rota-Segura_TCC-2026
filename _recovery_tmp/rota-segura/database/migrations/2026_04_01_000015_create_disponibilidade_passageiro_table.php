<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('disponibilidade_passageiro', function (Blueprint $table) {
            $table->bigIncrements('id_disponibilidade_passageiro');
            $table->unsignedBigInteger('id_passageiro');
            $table->unsignedBigInteger('id_vinculo');
            $table->date('data');
            $table->boolean('vai')->default(true);
            $table->enum('motivo_falta', ['doenca', 'feriado', 'viagem', 'evento', 'outro'])->nullable();
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('marcado_por');
            $table->timestamps();

            $table->unique(['id_passageiro', 'data']);
            $table->foreign('id_passageiro')->references('id_passageiro')->on('passageiro')->cascadeOnDelete();
            $table->foreign('id_vinculo')->references('id_vinculo')->on('vinculo')->restrictOnDelete();
            $table->foreign('marcado_por')->references('id_usuario')->on('usuario')->restrictOnDelete();
            $table->index(['id_passageiro', 'data']);
            $table->index(['id_vinculo', 'vai']);
        });
    }
    public function down(): void { Schema::dropIfExists('disponibilidade_passageiro'); }
};
