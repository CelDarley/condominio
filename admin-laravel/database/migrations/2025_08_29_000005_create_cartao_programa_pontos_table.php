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
        Schema::create('cartao_programa_pontos', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->foreignId('cartao_programa_id')->constrained('cartao_programas')->onDelete('cascade');
            $table->foreignId('ponto_base_id')->constrained('ponto_base')->onDelete('cascade');
            $table->integer('ordem'); // Ordem do ponto no cartão
            $table->time('horario_inicio');
            $table->time('horario_fim');
            $table->integer('tempo_estimado')->default(0); // em minutos
            $table->text('observacoes')->nullable();
            $table->boolean('obrigatorio')->default(true);
            $table->timestamps();
            
            $table->index(['cartao_programa_id', 'ordem']);
            $table->index(['ponto_base_id']);
            
            // Garantir que ordem seja única por cartão programa
            $table->unique(['cartao_programa_id', 'ordem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartao_programa_pontos');
    }
}; 