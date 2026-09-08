<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('van', function (Blueprint $table) {
            $table->string('foto_verso_url', 255)->nullable()->after('foto_url');
            $table->string('foto_interior_url', 255)->nullable()->after('foto_verso_url');
        });
    }
    public function down(): void {
        Schema::table('van', function (Blueprint $table) {
            $table->dropColumn(['foto_verso_url', 'foto_interior_url']);
        });
    }
};
