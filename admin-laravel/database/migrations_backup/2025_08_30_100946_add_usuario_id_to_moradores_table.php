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
        Schema::table('moradores', function (Blueprint $table) {
            // Adicionar coluna usuario_id como foreign key
            $table->integer('usuario_id')->nullable()->after('id');
            
            // Não vamos adicionar a constraint aqui pois pode dar erro
            // de dados inconsistentes. Faremos isso após popular os dados.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moradores', function (Blueprint $table) {
            $table->dropColumn('usuario_id');
        });
    }
};
