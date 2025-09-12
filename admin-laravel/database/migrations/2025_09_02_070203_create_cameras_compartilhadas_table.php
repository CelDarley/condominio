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
        Schema::create('cameras_compartilhadas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_morador');
            $table->string('apartamento');
            $table->string('titulo_camera');
            $table->text('descricao')->nullable();
            $table->string('url_imagem'); // URL da imagem da câmera
            $table->string('url_thumbnail')->nullable(); // URL do thumbnail (versão menor)
            $table->enum('tipo', ['entrada', 'varanda', 'garagem', 'area_comum', 'outros'])->default('outros');
            $table->boolean('ativa')->default(true);
            $table->boolean('compartilhada_vigilancia')->default(true);
            $table->timestamp('data_compartilhamento')->useCurrent();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index(['ativa', 'compartilhada_vigilancia']);
            $table->index('apartamento');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cameras_compartilhadas');
    }
};
