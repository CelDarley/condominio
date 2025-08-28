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
            $table->id();
            $table->unsignedBigInteger('cartao_programa_id');
            $table->integer('ponto_base_id');
            $table->foreign('cartao_programa_id')->references('id')->on('cartao_programas')->onDelete('cascade');
            $table->foreign('ponto_base_id')->references('id')->on('ponto_base')->onDelete('cascade');
            $table->integer('ordem'); // Ordem do ponto na sequência do cartão
            $table->integer('tempo_permanencia')->default(10); // Tempo em minutos no ponto
            $table->integer('tempo_deslocamento')->default(5); // Tempo em minutos para próximo ponto
            $table->text('instrucoes_especificas')->nullable(); // Instruções específicas para este cartão
            $table->boolean('obrigatorio')->default(true); // Se a verificação é obrigatória
            $table->timestamps();
            
            // Chave composta única
            $table->unique(['cartao_programa_id', 'ponto_base_id'], 'cartao_ponto_unique');
            $table->unique(['cartao_programa_id', 'ordem'], 'cartao_ordem_unique');
            
            // Índices
            $table->index(['cartao_programa_id', 'ordem']);
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
