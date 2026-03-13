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
        Schema::create('motoristas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('cnh_numero');
            $table->enum('cnh_categoria', ['A', 'B', 'C', 'D', 'E']);
            $table->date('cnh_validade');
            $table->string('cnh_foto')->nullable();
            $table->string('crlv')->nullable();
            $table->string('ant_crim')->nullable();
            $table->enum('status_aprov', ['pendente', 'aprovado', 'rejetado'])->default('pendente');
            $table->date('data_aprov')->nullable();    

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motoristas');
    }
};
