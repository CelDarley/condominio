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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->json('coordenadas_atual')->nullable(); // latitude e longitude atual
            $table->timestamp('ultima_atualizacao_localizacao')->nullable();
            $table->boolean('online')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['coordenadas_atual', 'ultima_atualizacao_localizacao', 'online']);
        });
    }
};
