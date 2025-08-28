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
            // Verificar se a coluna posto_id existe e renomear para posto_trabalho_id
            if (Schema::hasColumn('escala', 'posto_id')) {
                $table->renameColumn('posto_id', 'posto_trabalho_id');
            }
            
            // Adicionar cartao_programa_id se não existir
            if (!Schema::hasColumn('escala', 'cartao_programa_id')) {
                $table->unsignedBigInteger('cartao_programa_id')->nullable()->after('posto_trabalho_id');
                $table->foreign('cartao_programa_id')->references('id')->on('cartao_programas')->onDelete('set null');
            }
            
            // Adicionar timestamps se não existir
            if (!Schema::hasColumn('escala', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escala', function (Blueprint $table) {
            // Remover foreign key e coluna cartao_programa_id
            if (Schema::hasColumn('escala', 'cartao_programa_id')) {
                $table->dropForeign(['cartao_programa_id']);
                $table->dropColumn('cartao_programa_id');
            }
            
            // Renomear de volta posto_trabalho_id para posto_id
            if (Schema::hasColumn('escala', 'posto_trabalho_id')) {
                $table->renameColumn('posto_trabalho_id', 'posto_id');
            }
            
            // Remover timestamps
            $table->dropTimestamps();
        });
    }
};
