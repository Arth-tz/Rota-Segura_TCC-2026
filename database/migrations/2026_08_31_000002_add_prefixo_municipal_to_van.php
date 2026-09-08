<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('van', function (Blueprint $table) {
            // Número/código de autorização emitido pela prefeitura (ex: "47", "RS-23", "A-112")
            $table->string('prefixo_municipal', 20)->nullable()->after('autorizacao_municipal_validade');
        });
    }

    public function down(): void {
        Schema::table('van', function (Blueprint $table) {
            $table->dropColumn('prefixo_municipal');
        });
    }
};
