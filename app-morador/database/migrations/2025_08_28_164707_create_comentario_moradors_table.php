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
        Schema::create('comentario_moradors', function (Blueprint $table) {
            $table->id();
            $table->text('conteudo');
            $table->unsignedBigInteger('morador_id');
            $table->unsignedBigInteger('alerta_id')->nullable(); // Se for comentário em um alerta específico
            $table->enum('tipo', ['geral', 'alerta', 'sugestao', 'reclamacao']);
            $table->boolean('publico')->default(true); // Se outros moradores podem ver
            $table->timestamps();
            
            $table->foreign('morador_id')->references('id')->on('moradors')->onDelete('cascade');
            $table->foreign('alerta_id')->references('id')->on('alertas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentario_moradors');
    }
};
