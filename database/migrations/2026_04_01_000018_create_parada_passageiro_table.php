<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parada_passageiro', function (Blueprint $table) {
            $table->bigIncrements('id_parada_passageiro');
            $table->unsignedBigInteger('id_parada');
            $table->unsignedBigInteger('id_passageiro');
            $table->timestamps();

            $table->unique(['id_parada', 'id_passageiro']);
            $table->foreign('id_parada')->references('id_parada')->on('parada')->cascadeOnDelete();
            $table->foreign('id_passageiro')->references('id_passageiro')->on('passageiro')->restrictOnDelete();
            $table->index('id_passageiro');
        });
    }
    public function down(): void { Schema::dropIfExists('parada_passageiro'); }
};
