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
            $table->id();
            $table->date('data');
            $table->foreignId('escala_original_id')->nullable()->constrained('escala')->onDelete('cascade');
            $table->foreignId('usuario_original_id')->constrained('usuario')->onDelete('cascade');
            $table->foreignId('usuario_substituto_id')->nullable()->constrained('usuario')->onDelete('set null');
            $table->foreignId('posto_trabalho_id')->constrained('posto_trabalho')->onDelete('cascade');
            $table->foreignId('cartao_programa_id')->nullable()->constrained('cartao_programas')->onDelete('set null');
            $table->text('motivo')->nullable(); // Motivo de alteração/substituição
            $table->enum('status', ['ativo', 'substituicao', 'folga', 'falta'])->default('ativo');
            $table->foreignId('criado_por')->constrained('usuario')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['data', 'usuario_original_id']);
            $table->index(['data', 'posto_trabalho_id']);
            $table->index(['usuario_original_id', 'data']);
            $table->unique(['data', 'posto_trabalho_id'], 'escala_diaria_posto_data_unique');
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
