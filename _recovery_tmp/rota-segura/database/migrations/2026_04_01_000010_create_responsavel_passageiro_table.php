<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('responsavel_passageiro', function (Blueprint $table) {
            $table->bigIncrements('id_responsavel_passageiro');
            $table->unsignedBigInteger('id_responsavel');
            $table->unsignedBigInteger('id_passageiro');
            $table->date('data_inicio')->default(now());
            $table->date('data_fim')->nullable();
            $table->timestamps();

            $table->foreign('id_responsavel')->references('id_responsavel')->on('responsavel')->cascadeOnDelete();
            $table->foreign('id_passageiro')->references('id_passageiro')->on('passageiro')->cascadeOnDelete();
            $table->index(['id_passageiro', 'data_fim']);
        });
    }
    public function down(): void { Schema::dropIfExists('responsavel_passageiro'); }
};
