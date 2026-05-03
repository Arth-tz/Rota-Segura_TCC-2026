<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parada', function (Blueprint $table) {
            $table->bigIncrements('id_parada');
            $table->unsignedBigInteger('id_rota');
            $table->unsignedBigInteger('id_endereco');
            $table->unsignedInteger('ordem');
            $table->enum('tipo', ['embarque', 'desembarque']);
            $table->time('horario_previsto')->nullable();
            $table->time('horario_real')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['id_rota', 'ordem']);
            $table->foreign('id_rota')->references('id_rota')->on('rota')->cascadeOnDelete();
            $table->foreign('id_endereco')->references('id_endereco')->on('endereco')->restrictOnDelete();
            $table->index('id_rota');
        });
    }
    public function down(): void { Schema::dropIfExists('parada'); }
};
