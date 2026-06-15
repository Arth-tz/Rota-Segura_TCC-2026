<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('solicitacao_disponibilidade', function (Blueprint $table) {
            $table->bigIncrements('id_solicitacao_disponibilidade');
            $table->unsignedBigInteger('id_solicitacao');
            $table->unsignedBigInteger('id_disponibilidade');
            $table->decimal('preco_mensal', 8, 2)->comment('Preço do trajeto no momento da solicitação');
            $table->timestamps();

            $table->unique(['id_solicitacao', 'id_disponibilidade'], 'uk_solicitacao_disponibilidade');
            $table->foreign('id_solicitacao')->references('id_solicitacao')->on('solicitacao')->cascadeOnDelete();
            $table->foreign('id_disponibilidade')->references('id_disponibilidade')->on('disponibilidade')->restrictOnDelete();
            $table->index('id_disponibilidade');
        });
    }

    public function down(): void { Schema::dropIfExists('solicitacao_disponibilidade'); }
};
