<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('motorista', function (Blueprint $table) {
            $table->bigIncrements('id_motorista');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->string('cnh_numero', 20)->unique();
            $table->string('cnh_categoria', 5);
            $table->date('cnh_validade');
            $table->string('cnh_foto_url', 255)->nullable();
            $table->enum('status_aprovacao', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('motivo_rejeicao')->nullable();
            $table->timestamp('data_avaliacao_documento')->nullable();
            $table->unsignedBigInteger('id_usuario_avaliador')->nullable();
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->restrictOnDelete()->restrictOnUpdate();
            $table->foreign('id_usuario_avaliador')->references('id_usuario')->on('usuario')->nullOnDelete();
            $table->index('status_aprovacao');
            $table->index('cnh_validade');
        });
    }
    public function down(): void { Schema::dropIfExists('motorista'); }
};
