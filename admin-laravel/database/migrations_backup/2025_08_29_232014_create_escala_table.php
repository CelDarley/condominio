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
        Schema::create('escala', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->unsignedBigInteger('posto_trabalho_id');
            $table->unsignedBigInteger('usuario_id');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->time('horario_inicio');
            $table->time('horario_fim');
            $table->json('dias_semana'); // [1,2,3,4,5] para seg-sex
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->foreign('posto_trabalho_id')->references('id')->on('posto_trabalho')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuario')->onDelete('cascade');
            $table->index(['ativo', 'data_inicio', 'data_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escala');
    }
};
