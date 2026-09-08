<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('van', function (Blueprint $table) {
            // Fotos adicionais
            $table->string('foto_lateral_esq_url', 255)->nullable()->after('foto_interior_url');
            $table->string('foto_lateral_dir_url', 255)->nullable()->after('foto_lateral_esq_url');

            // Autorização municipal (credenciamento da prefeitura — ex: prefixo SMTM em Canoas)
            $table->string('autorizacao_municipal_url', 255)->nullable()->after('seguro_validade');
            $table->date('autorizacao_municipal_validade')->nullable()->after('autorizacao_municipal_url');
        });
    }

    public function down(): void {
        Schema::table('van', function (Blueprint $table) {
            $table->dropColumn([
                'foto_lateral_esq_url',
                'foto_lateral_dir_url',
                'autorizacao_municipal_url',
                'autorizacao_municipal_validade',
            ]);
        });
    }
};
