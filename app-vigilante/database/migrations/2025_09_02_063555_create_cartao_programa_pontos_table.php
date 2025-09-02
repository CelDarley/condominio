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
            $table->foreignId('cartao_programa_id')->constrained('cartao_programas')->onDelete('cascade');
            $table->foreignId('ponto_base_id')->constrained('ponto_base')->onDelete('cascade');
            $table->integer('ordem');
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->integer('tempo_permanencia')->nullable(); // em minutos
            $table->integer('tempo_deslocamento')->nullable(); // em minutos
            $table->integer('tempo_estimado')->nullable(); // em minutos
            $table->text('instrucoes_especificas')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('obrigatorio')->default(true);
            $table->timestamps();
            
            $table->index(['cartao_programa_id', 'ordem']);
            $table->unique(['cartao_programa_id', 'ponto_base_id', 'ordem'], 'cartao_ponto_ordem_unique');
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
