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
        Schema::create('solicitacao_panicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('morador_id');
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['seguranca', 'medica', 'incendio', 'outro']);
            $table->enum('status', ['ativo', 'em_atendimento', 'resolvido']);
            $table->string('localizacao')->nullable();
            $table->json('coordenadas')->nullable();
            $table->timestamp('atendido_em')->nullable();
            $table->unsignedBigInteger('atendido_por')->nullable(); // ID do vigilante
            $table->text('observacoes_atendimento')->nullable();
            $table->timestamps();
            
            $table->foreign('morador_id')->references('id')->on('moradors')->onDelete('cascade');
            $table->foreign('atendido_por')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacao_panicos');
    }
};
