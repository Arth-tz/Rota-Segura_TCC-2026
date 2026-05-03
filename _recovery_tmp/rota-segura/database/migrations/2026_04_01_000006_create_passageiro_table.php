<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('passageiro', function (Blueprint $table) {
            $table->bigIncrements('id_passageiro');
            $table->unsignedBigInteger('id_pessoa')->unique();
            $table->unsignedBigInteger('id_usuario')->nullable()->unique();
            $table->text('observacoes_medicas')->nullable();
            $table->boolean('ativo')->default(true);
            $table->date('data_inscricao')->default(now());
            $table->timestamps();

            $table->foreign('id_pessoa')->references('id_pessoa')->on('pessoa')->restrictOnDelete()->restrictOnUpdate();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->nullOnDelete();
            $table->index('ativo');
        });
    }
    public function down(): void { Schema::dropIfExists('passageiro'); }
};
