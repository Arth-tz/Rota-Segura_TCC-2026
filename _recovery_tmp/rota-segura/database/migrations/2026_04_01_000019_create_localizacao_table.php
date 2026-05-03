<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('localizacao', function (Blueprint $table) {
            $table->bigIncrements('id_localizacao');
            $table->unsignedBigInteger('id_rota');
            $table->unsignedBigInteger('id_van');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('altitude', 8, 2)->nullable();
            $table->decimal('precisao_metros', 6, 2)->nullable();
            $table->enum('fonte_localizacao', ['gps', 'rede', 'fusao'])->default('gps');
            $table->unsignedInteger('numero_satelites')->nullable();
            $table->dateTime('timestamp_captura');
            $table->timestamps();

            $table->foreign('id_rota')->references('id_rota')->on('rota')->cascadeOnDelete();
            $table->foreign('id_van')->references('id_van')->on('van')->restrictOnDelete();
            $table->index(['id_rota', 'timestamp_captura']);
            $table->index(['id_van', 'timestamp_captura']);
            $table->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('localizacao'); }
};
