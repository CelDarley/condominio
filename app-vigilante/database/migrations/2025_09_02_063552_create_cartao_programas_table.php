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
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->foreignId('posto_trabalho_id')->constrained('posto_trabalho')->onDelete('cascade');
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->integer('tempo_total_estimado')->nullable(); // em minutos
            $table->boolean('ativo')->default(true);
            $table->json('configuracoes')->nullable();
            $table->timestamps();
            
            $table->index(['posto_trabalho_id', 'ativo']);
            $table->index('ativo');
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
