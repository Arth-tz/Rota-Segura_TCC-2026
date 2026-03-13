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
        Schema::create('vans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorista_id')->constrained('motoristas')->cascadeOnDelete();
            $table->string('placa', 7)->unique();
            $table->string('modelo');
            $table->string('marca');
            $table->year('ano_fabricacao');
            $table->string('cor');
            $table->unsignedSmallInteger('capacidade');
            $table->string('crlv_num');
            $table->date('crlv_val');
            $table->string('seguro_num');
            $table->date('seguro_val');
            $table->date('ult_rev');
            $table->date('prox_rev')->nullable();
            $table->string('foto_van')->nullable();
            $table->boolean('disponivel')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vans');
    }
};
