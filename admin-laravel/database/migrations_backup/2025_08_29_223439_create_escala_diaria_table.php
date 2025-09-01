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
            $table->date('data'); // Data específica do ajuste
            $table->unsignedInteger('escala_original_id'); // Escala semanal original
            $table->unsignedInteger('usuario_original_id'); // Usuário originalmente escalado
            $table->unsignedInteger('usuario_substituto_id'); // Usuário substituto
            $table->unsignedInteger('posto_trabalho_id'); // Posto de trabalho
            $table->unsignedBigInteger('cartao_programa_id')->nullable(); // Cartão programa específico
            $table->text('motivo')->nullable(); // Motivo da substituição
            $table->enum('status', ['ativo', 'cancelado'])->default('ativo');
            $table->unsignedInteger('criado_por'); // Admin que fez a alteração
            $table->timestamps();

            // Índices
            $table->index(['data', 'status']);
            $table->index(['posto_trabalho_id', 'data']);
            $table->index(['escala_original_id']);
            $table->index(['usuario_original_id']);
            $table->index(['usuario_substituto_id']);
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
