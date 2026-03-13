<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('responsavel_id')->constrained('responsaveis')->cascadeOnDelete();
            $table->foreignId('motorista_id')->nullable()->constrained('motoristas')->nullOnDelete();

            $table->string('first_name');
            $table->string('last_name');

            $table->date('data_nascimento');

            $table->string('escola_nome');
            $table->string('escola_endereco');

            $table->enum('turno', ['manha', 'tarde', 'noite']);

            $table->string('serie_ano');

            $table->string('endereco_embarque');
            $table->string('endereco_desembarque');

            $table->string('obs_medica')->nullable();

            $table->string('ctt_emergencia')->nullable();
            $table->string('tel_emergencia')->nullable();

            $table->string('foto')->nullable();

            $table->boolean('ativo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
