<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('responsavel', function (Blueprint $table) {
            $table->bigIncrements('id_responsavel');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->enum('tipo_responsavel', ['pai', 'mae', 'tutor', 'representante_legal', 'autoresponsavel'])->default('pai');
            $table->string('telefone_emergencia', 20)->nullable();
            $table->date('data_responsavel_ate')->nullable();
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->restrictOnDelete()->restrictOnUpdate();
            $table->index('tipo_responsavel');
        });
    }
    public function down(): void { Schema::dropIfExists('responsavel'); }
};
