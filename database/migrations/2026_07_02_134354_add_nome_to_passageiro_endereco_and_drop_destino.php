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
        Schema::table('passageiro_endereco', function (Blueprint $table) {
            $table->string('nome', 150)->nullable()->after('principal')
                ->comment('Nome do local (ex: Escola Municipal ABC)');
        });

        Schema::dropIfExists('destino');
    }

    public function down(): void
    {
        Schema::table('passageiro_endereco', function (Blueprint $table) {
            $table->dropColumn('nome');
        });

        Schema::create('destino', function (Blueprint $table) {
            $table->bigIncrements('id_destino');
            $table->unsignedBigInteger('id_endereco')->unique();
            $table->string('nome', 150);
            $table->enum('tipo', ['escola', 'atividade', 'outro']);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->foreign('id_endereco')->references('id_endereco')->on('endereco');
        });
    }
};
