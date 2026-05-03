<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('destino', function (Blueprint $table) {
            $table->bigIncrements('id_destino');
            $table->unsignedBigInteger('id_endereco')->unique();
            $table->string('nome', 150);
            $table->enum('tipo', ['escola', 'atividade', 'outro']);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('id_endereco')->references('id_endereco')->on('endereco')->restrictOnDelete();
            $table->index('tipo');
            $table->index('ativo');
        });
    }
    public function down(): void { Schema::dropIfExists('destino'); }
};
