<?php
// MIGRATION 1: pessoa
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pessoa', function (Blueprint $table) {
            $table->bigIncrements('id_pessoa');
            $table->string('nome', 150);
            $table->char('cpf', 11)->unique();
            $table->date('data_nascimento');
            $table->string('telefone', 20)->nullable();
            $table->string('foto_url', 255)->nullable();
            $table->timestamp('foto_upload_data')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->index('cpf');
            $table->index('ativo');
        });
    }
    public function down(): void { Schema::dropIfExists('pessoa'); }
};
