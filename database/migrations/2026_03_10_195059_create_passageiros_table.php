<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passageiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('responsavel_id')->constrained('responsaveis')->cascadeOnDelete();
            $table->foreignId('motorista_id')->nullable()->constrained('motoristas')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('data_nascimento')->nullable();
            $table->string('escola_nome')->nullable();
            $table->string('escola_endereco')->nullable();
            $table->decimal('escola_lat', 10, 7)->nullable();
            $table->decimal('escola_lng', 10, 7)->nullable();
            $table->enum('turno', ['manha', 'tarde', 'noite'])->nullable();
            $table->string('serie_ano')->nullable();
            $table->string('endereco_embarque')->nullable();
            $table->decimal('embarque_lat', 10, 7)->nullable();
            $table->decimal('embarque_lng', 10, 7)->nullable();
            $table->string('endereco_desembarque')->nullable();
            $table->decimal('desembarque_lat', 10, 7)->nullable();
            $table->decimal('desembarque_lng', 10, 7)->nullable();
            $table->string('obs_medica')->nullable();
            $table->string('ctt_emergencia')->nullable();
            $table->string('tel_emergencia')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passageiros');
    }
};