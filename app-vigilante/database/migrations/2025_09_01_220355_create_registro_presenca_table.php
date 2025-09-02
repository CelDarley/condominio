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
        Schema::create('registro_presenca', function (Blueprint $table) {
            $table->id();
            
            // Referências
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('escala_id'); // Escala do dia
            $table->unsignedBigInteger('ponto_base_id');
            $table->unsignedBigInteger('cartao_programa_ponto_id')->nullable(); // Para saber qual ponto do programa
            
            // Dados do registro
            $table->date('data');
            $table->enum('tipo', ['chegada', 'saida']);
            $table->timestamp('data_hora_registro');
            
            // Localização (opcional para validar)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Observações
            $table->text('observacoes')->nullable();
            $table->enum('status', ['normal', 'atraso', 'antecipado'])->default('normal');
            
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('usuario_id')->references('id')->on('usuario')->onDelete('cascade');
            $table->foreign('escala_id')->references('id')->on('escala')->onDelete('cascade');
            $table->foreign('ponto_base_id')->references('id')->on('ponto_base')->onDelete('cascade');
            $table->foreign('cartao_programa_ponto_id')->references('id')->on('cartao_programa_pontos')->onDelete('set null');
            
            // Índices
            $table->index(['usuario_id', 'data']);
            $table->index(['escala_id', 'data']);
            $table->index(['ponto_base_id', 'data']);
            
            // Evitar duplicatas do mesmo tipo no mesmo ponto/usuário/data/hora
            $table->unique(['usuario_id', 'ponto_base_id', 'data', 'tipo', 'data_hora_registro'], 'unique_registro_presenca');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_presenca');
    }
};
