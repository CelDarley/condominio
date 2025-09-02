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
        Schema::table('cartao_programa_pontos', function (Blueprint $table) {
            // Tornar os campos horario_inicio e horario_fim nullable
            $table->time('horario_inicio')->nullable()->change();
            $table->time('horario_fim')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cartao_programa_pontos', function (Blueprint $table) {
            // Voltar os campos para not null
            $table->time('horario_inicio')->nullable(false)->change();
            $table->time('horario_fim')->nullable(false)->change();
        });
    }
};
