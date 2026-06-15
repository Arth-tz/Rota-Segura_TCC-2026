<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('disponibilidade_dia', function (Blueprint $table) {
            $table->bigIncrements('id_disponibilidade_dia');
            $table->unsignedBigInteger('id_disponibilidade');
            $table->enum('dia_semana', ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom']);
            $table->timestamps();

            $table->unique(['id_disponibilidade', 'dia_semana'], 'uk_disponibilidade_dia');
            $table->foreign('id_disponibilidade')
                ->references('id_disponibilidade')
                ->on('disponibilidade')
                ->cascadeOnDelete();
            $table->index('dia_semana');
        });
    }

    public function down(): void { Schema::dropIfExists('disponibilidade_dia'); }
};
