<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('disponibilidade_parada', function (Blueprint $table) {
            $table->bigIncrements('id_disponibilidade_parada');
            $table->unsignedBigInteger('id_disponibilidade');
            $table->unsignedBigInteger('id_endereco');
            $table->unsignedInteger('ordem');
            $table->enum('tipo', ['embarque', 'desembarque']);
            $table->time('horario_previsto');
            $table->boolean('ativa')->default(true);
            $table->timestamps();

            $table->unique(['id_disponibilidade', 'ordem']);
            $table->foreign('id_disponibilidade')->references('id_disponibilidade')->on('disponibilidade')->cascadeOnDelete();
            $table->foreign('id_endereco')->references('id_endereco')->on('endereco')->restrictOnDelete();
            $table->index(['id_disponibilidade', 'ordem']);
        });
    }
    public function down(): void { Schema::dropIfExists('disponibilidade_parada'); }
};
