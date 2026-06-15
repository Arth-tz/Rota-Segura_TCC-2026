<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vinculo_disponibilidade', function (Blueprint $table) {
            $table->bigIncrements('id_vinculo_disponibilidade');
            $table->unsignedBigInteger('id_vinculo');
            $table->unsignedBigInteger('id_disponibilidade');
            $table->decimal('preco_mensal', 8, 2)->comment('Preço do trajeto acordado no vínculo');
            $table->timestamps();

            $table->unique(['id_vinculo', 'id_disponibilidade'], 'uk_vinculo_disponibilidade');
            $table->foreign('id_vinculo')->references('id_vinculo')->on('vinculo')->cascadeOnDelete();
            $table->foreign('id_disponibilidade')->references('id_disponibilidade')->on('disponibilidade')->restrictOnDelete();
            $table->index('id_disponibilidade');
        });
    }

    public function down(): void { Schema::dropIfExists('vinculo_disponibilidade'); }
};
