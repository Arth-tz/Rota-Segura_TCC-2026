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
        Schema::table('disponibilidade', function (Blueprint $table) {
            $table->json('bairros_atendidos')->nullable()->after('capacidade_total');
            $table->json('escolas_atendidas')->nullable()->after('bairros_atendidos');
        });
    }

    public function down(): void
    {
        Schema::table('disponibilidade', function (Blueprint $table) {
            $table->dropColumn(['bairros_atendidos', 'escolas_atendidas']);
        });
    }
};
