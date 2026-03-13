<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    //eu tinha deixado todos campos de endereco sem nullable, ou seja, não tinha como fazer uma tela de cadastro mais simples, sem tudo isso
    public function up(): void
    {
        Schema::table('responsaveis', function (Blueprint $table){
            $table->string('endereco')->nullable()->change();
            $table->string('cidade')->nullable()->change();
            $table->char('estado', 2)->nullable()->change();
            $table->string('cep')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responsaveis', function (Blueprint $table){
            $table->string('endereco')->nullable(false)->change();
            $table->string('cidade')->nullable(false)->change();
            $table->char('estado', 2)->nullable(false)->change();
            $table->string('cep')->nullable(false)->change();
        });
    }
};
