<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('motorista', function (Blueprint $table) {
            $table->string('certidao_antecedentes_url', 500)->nullable()->after('cnh_foto_url');
        });
    }
    public function down(): void {
        Schema::table('motorista', function (Blueprint $table) {
            $table->dropColumn('certidao_antecedentes_url');
        });
    }
};
