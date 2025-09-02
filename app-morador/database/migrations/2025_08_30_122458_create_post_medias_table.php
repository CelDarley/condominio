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
        Schema::create('post_medias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->enum('tipo', ['imagem', 'video', 'audio']);
            $table->string('arquivo_path');
            $table->string('arquivo_nome');
            $table->string('mime_type');
            $table->bigInteger('tamanho'); // em bytes
            $table->json('metadata')->nullable(); // dimensões, duração, etc
            $table->integer('ordem')->default(0); // para ordenar múltiplas mídias
            $table->timestamps();
            
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->index(['post_id', 'ordem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_medias');
    }
};
