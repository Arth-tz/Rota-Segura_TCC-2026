<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->unsignedBigInteger('id_pessoa')->unique();
            $table->string('email', 150)->unique();
            $table->string('senha_hash', 255);
            $table->enum('role', ['admin', 'motorista', 'responsavel', 'passageiro'])->default('passageiro');
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_login')->nullable();
            $table->unsignedInteger('tentativas_falhas')->default(0);
            $table->timestamp('bloqueado_ate')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_pessoa')->references('id_pessoa')->on('pessoa')->restrictOnDelete()->restrictOnUpdate();
            $table->index('ativo');
            $table->index('role');
            $table->index(['email', 'ativo']);
        });
    }
    public function down(): void { Schema::dropIfExists('usuario'); }
};
