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
        Schema::create('cartao_programas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100); // Nome do cartão programa
            $table->text('descricao')->nullable(); // Descrição do programa
            $table->integer('posto_trabalho_id'); // Relacionado a um posto
            $table->foreign('posto_trabalho_id')->references('id')->on('posto_trabalho')->onDelete('cascade');
            $table->time('horario_inicio')->default('08:00'); // Horário de início das atividades
            $table->time('horario_fim')->default('18:00'); // Horário de fim das atividades
            $table->integer('tempo_total_estimado')->default(0); // Tempo total em minutos (calculado)
            $table->boolean('ativo')->default(true); // Se o cartão está ativo
            $table->json('configuracoes')->nullable(); // Configurações adicionais em JSON
            $table->timestamps();
            
            // Índices
            $table->index(['posto_trabalho_id', 'ativo']);
            $table->index('nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartao_programas');
    }
};
