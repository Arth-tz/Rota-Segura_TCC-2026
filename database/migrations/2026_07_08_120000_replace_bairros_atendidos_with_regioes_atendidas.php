<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('disponibilidade', function (Blueprint $table) {
            $table->json('regioes_atendidas')->nullable()->after('bairros_atendidos');
        });

        // Backfill: agrupa os bairros existentes sob a cidade padrão de Canoas.
        DB::table('disponibilidade')->orderBy('id_disponibilidade')->each(function ($disp) {
            $bairros = json_decode($disp->bairros_atendidos ?? '[]', true) ?: [];
            $regioes = $bairros ? [['cidade' => 'Canoas', 'bairros' => $bairros]] : [];

            DB::table('disponibilidade')
                ->where('id_disponibilidade', $disp->id_disponibilidade)
                ->update(['regioes_atendidas' => json_encode($regioes)]);
        });

        Schema::table('disponibilidade', function (Blueprint $table) {
            $table->dropColumn('bairros_atendidos');
        });
    }

    public function down(): void {
        Schema::table('disponibilidade', function (Blueprint $table) {
            $table->json('bairros_atendidos')->nullable()->after('escolas_atendidas');
        });

        DB::table('disponibilidade')->orderBy('id_disponibilidade')->each(function ($disp) {
            $regioes = json_decode($disp->regioes_atendidas ?? '[]', true) ?: [];
            $bairros = collect($regioes)->flatMap(fn ($r) => $r['bairros'] ?? [])->unique()->values()->all();

            DB::table('disponibilidade')
                ->where('id_disponibilidade', $disp->id_disponibilidade)
                ->update(['bairros_atendidos' => json_encode($bairros)]);
        });

        Schema::table('disponibilidade', function (Blueprint $table) {
            $table->dropColumn('regioes_atendidas');
        });
    }
};
