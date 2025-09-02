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
        Schema::create('ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuario')->onDelete('cascade');
            $table->foreignId('posto_trabalho_id')->nullable()->constrained('posto_trabalho')->onDelete('set null');
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('tipo', ['incidente', 'manutencao', 'seguranca', 'outros'])->default('incidente');
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('status', ['aberta', 'em_andamento', 'resolvida', 'fechada'])->default('aberta');
            $table->json('anexos')->nullable(); // Array com caminhos dos arquivos
            $table->timestamp('data_ocorrencia')->useCurrent();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index(['usuario_id', 'status']);
            $table->index(['posto_trabalho_id', 'status']);
            $table->index(['data_ocorrencia', 'prioridade']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocorrencias');
    }
};
