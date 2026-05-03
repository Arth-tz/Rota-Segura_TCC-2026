<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('passageiro_endereco', function (Blueprint $table) {
            $table->bigIncrements('id_passageiro_endereco');
            $table->unsignedBigInteger('id_passageiro');
            $table->unsignedBigInteger('id_endereco');
            $table->enum('tipo', ['embarque', 'desembarque', 'residencia']);
            $table->boolean('principal')->default(false);
            $table->timestamps();

            $table->unique(['id_passageiro', 'id_endereco', 'tipo']);
            $table->foreign('id_passageiro')->references('id_passageiro')->on('passageiro')->cascadeOnDelete();
            $table->foreign('id_endereco')->references('id_endereco')->on('endereco')->restrictOnDelete();
            $table->index(['id_passageiro', 'principal']);
        });
    }
    public function down(): void { Schema::dropIfExists('passageiro_endereco'); }
};
