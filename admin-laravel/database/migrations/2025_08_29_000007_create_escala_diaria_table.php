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
        Schema::create('escala_diaria', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->date('data'); // Data específica do ajuste
            $table->foreignId('escala_original_id')->constrained('escala')->onDelete('cascade');
            $table->foreignId('usuario_original_id')->constrained('usuario')->onDelete('cascade');
            $table->foreignId('usuario_substituto_id')->constrained('usuario')->onDelete('cascade');
            $table->foreignId('posto_trabalho_id')->constrained('posto_trabalho')->onDelete('cascade');
            $table->foreignId('cartao_programa_id')->nullable()->constrained('cartao_programas')->onDelete('set null');
            $table->text('motivo')->nullable(); // Motivo da substituição
            $table->enum('status', ['ativo', 'cancelado'])->default('ativo');
            $table->foreignId('criado_por')->constrained('usuario')->onDelete('cascade');
            $table->timestamps();

            // Índices para performance
            $table->index(['data', 'status']);
            $table->index(['posto_trabalho_id', 'data']);
            $table->index(['escala_original_id']);
            $table->index(['usuario_original_id']);
            $table->index(['usuario_substituto_id']);
            
            // Garantir que não há duplicação de ajuste para mesma escala/data
            $table->unique(['escala_original_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escala_diaria');
    }
}; 