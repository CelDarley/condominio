<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacao_panicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('morador_id');
            $table->string('titulo')->default('Solicitação de Pânico');
            $table->text('descricao')->nullable();
            $table->enum('status', ['ativo', 'resolvido', 'cancelado'])->default('ativo');
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'critica'])->default('critica');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('endereco')->nullable();
            $table->timestamp('resolvido_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->foreign('morador_id')->references('id')->on('usuario')->onDelete('cascade');
            $table->index(['morador_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacao_panicos');
    }
};
