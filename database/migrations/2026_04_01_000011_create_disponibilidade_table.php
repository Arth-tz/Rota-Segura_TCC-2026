<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('disponibilidade', function (Blueprint $table) {
            $table->bigIncrements('id_disponibilidade');
            $table->unsignedBigInteger('id_van');
            $table->string('nome', 100)->comment('Ex: Manhã ida, Tarde volta, Treino ter/qui');
            $table->enum('turno', ['manha', 'tarde', 'noite']);
            $table->decimal('preco_mensal', 8, 2);
            $table->unsignedInteger('capacidade_total');
            $table->boolean('ativa')->default(true);
            $table->timestamps();

            $table->foreign('id_van')->references('id_van')->on('van')->cascadeOnDelete();
            $table->index(['id_van', 'ativa']);
        });
    }
    public function down(): void { Schema::dropIfExists('disponibilidade'); }
};
