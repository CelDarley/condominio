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
        Schema::create('escala', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->foreignId('posto_trabalho_id')->constrained('posto_trabalho')->onDelete('cascade');
            $table->foreignId('cartao_programa_id')->nullable()->constrained('cartao_programas')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuario')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->json('dias_semana'); // Array com dias da semana (0-6)
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index(['usuario_id', 'ativo']);
            $table->index(['posto_trabalho_id', 'ativo']);
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escala');
    }
};
