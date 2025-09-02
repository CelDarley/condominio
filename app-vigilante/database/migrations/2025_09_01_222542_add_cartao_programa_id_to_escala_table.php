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
        Schema::table('escala', function (Blueprint $table) {
            // Adicionar cartao_programa_id se não existir
            if (!Schema::hasColumn('escala', 'cartao_programa_id')) {
                $table->unsignedBigInteger('cartao_programa_id')->nullable()->after('posto_trabalho_id');
                
                // Adicionar foreign key se a tabela cartao_programas existir
                if (Schema::hasTable('cartao_programas')) {
                    $table->foreign('cartao_programa_id')
                          ->references('id')
                          ->on('cartao_programas')
                          ->onDelete('set null');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escala', function (Blueprint $table) {
            // Remover foreign key e coluna cartao_programa_id se existir
            if (Schema::hasColumn('escala', 'cartao_programa_id')) {
                $table->dropForeign(['cartao_programa_id']);
                $table->dropColumn('cartao_programa_id');
            }
        });
    }
};
