<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('solicitacao_disponibilidade', function (Blueprint $table) {
            if (!Schema::hasColumn('solicitacao_disponibilidade', 'dias_contratados')) {
                $table->json('dias_contratados')->nullable()->after('preco_mensal');
            }
            $table->decimal('preco_mensal', 8, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('solicitacao_disponibilidade', function (Blueprint $table) {
            $table->dropColumn('dias_contratados');
            $table->decimal('preco_mensal', 8, 2)->nullable(false)->change();
        });
    }
};
