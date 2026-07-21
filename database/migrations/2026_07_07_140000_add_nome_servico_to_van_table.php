<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('van', function (Blueprint $table) {
            $table->string('nome_servico', 150)->nullable()->after('placa');
        });
    }
    public function down(): void {
        Schema::table('van', function (Blueprint $table) {
            $table->dropColumn('nome_servico');
        });
    }
};
