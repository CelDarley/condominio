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
        Schema::table('alertas', function (Blueprint $table) {
            // Alterar o tipo da coluna de int unsigned para bigint unsigned
            $table->unsignedBigInteger('usuario_id')->nullable()->change();
            
            // Adicionar a foreign key
            $table->foreign('usuario_id')->references('id')->on('usuario')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            // Remover a foreign key
            $table->dropForeign(['usuario_id']);
            
            // Voltar para o tipo anterior
            $table->unsignedInteger('usuario_id')->nullable()->change();
        });
    }
};
