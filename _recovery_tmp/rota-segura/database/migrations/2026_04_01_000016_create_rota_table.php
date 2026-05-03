<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rota', function (Blueprint $table) {
            $table->bigIncrements('id_rota');
            $table->unsignedBigInteger('id_van');
            $table->date('data');
            $table->enum('status', ['planejada', 'em_andamento', 'concluida', 'cancelada'])->default('planejada');
            $table->dateTime('horario_inicio_previsto')->nullable();
            $table->dateTime('horario_inicio_real')->nullable();
            $table->dateTime('horario_fim_previsto')->nullable();
            $table->dateTime('horario_fim_real')->nullable();
            $table->decimal('distancia_km', 10, 2)->nullable();
            $table->unsignedInteger('tempo_decorrido_minutos')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('id_van')->references('id_van')->on('van')->restrictOnDelete();
            $table->index(['id_van', 'data']);
            $table->index('status');
        });
    }
    public function down(): void { Schema::dropIfExists('rota'); }
};
