<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('solicitacao', function (Blueprint $table) {
            $table->enum('tipo', ['nova', 'alteracao'])->default('nova')->after('tipo_solicitante');
            $table->unsignedBigInteger('id_vinculo_alterado')->nullable()->after('tipo');
            $table->foreign('id_vinculo_alterado')->references('id_vinculo')->on('vinculo')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('solicitacao', function (Blueprint $table) {
            $table->dropForeign(['id_vinculo_alterado']);
            $table->dropColumn(['tipo', 'id_vinculo_alterado']);
        });
    }
};
