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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->text('conteudo')->nullable();
            $table->enum('tipo', ['texto', 'imagem', 'video', 'audio'])->default('texto');
            $table->boolean('ativo')->default(true);
            $table->integer('likes')->default(0);
            $table->integer('comentarios_count')->default(0);
            $table->json('metadata')->nullable(); // Para dados extras como localização, etc
            $table->timestamps();
            
            $table->foreign('usuario_id')->references('id')->on('usuario')->onDelete('cascade');
            $table->index(['ativo', 'created_at']);
            $table->index('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
