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
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('tipo', ['seguranca', 'manutencao', 'geral', 'emergencia']);
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'critica']);
            $table->enum('status', ['ativo', 'resolvido', 'cancelado']);
            $table->unsignedBigInteger('usuario_id')->nullable(); // ID do vigilante que criou
            $table->string('localizacao')->nullable();
            $table->json('coordenadas')->nullable(); // latitude e longitude
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuario')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
