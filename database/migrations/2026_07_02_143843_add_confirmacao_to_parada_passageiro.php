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
        Schema::table('parada_passageiro', function (Blueprint $table) {
            $table->timestamp('embarque_em')->nullable()->after('id_passageiro');
            $table->timestamp('desembarque_em')->nullable()->after('embarque_em');
            $table->enum('marcado_por', ['motorista', 'sistema', 'responsavel'])->nullable()->after('desembarque_em');
            $table->enum('metodo_confirmacao', ['manual', 'geo', 'qrcode'])->nullable()->after('marcado_por');
        });
    }

    public function down(): void
    {
        Schema::table('parada_passageiro', function (Blueprint $table) {
            $table->dropColumn(['embarque_em', 'desembarque_em', 'marcado_por', 'metodo_confirmacao']);
        });
    }
};
