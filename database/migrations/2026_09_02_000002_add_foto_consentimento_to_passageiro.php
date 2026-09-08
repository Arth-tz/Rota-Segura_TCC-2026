<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('passageiro', function (Blueprint $table) {
            $table->boolean('foto_consentimento_lgpd')->default(false)->after('observacoes_medicas');
        });
    }
    public function down(): void {
        Schema::table('passageiro', function (Blueprint $table) {
            $table->dropColumn('foto_consentimento_lgpd');
        });
    }
};
