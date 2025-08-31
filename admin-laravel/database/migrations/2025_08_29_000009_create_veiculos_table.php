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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->foreignId('morador_id')->constrained('moradores')->onDelete('cascade');
            $table->string('placa', 10);
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->string('cor', 30);
            $table->year('ano')->nullable();
            $table->enum('tipo', ['carro', 'moto', 'caminhao', 'van', 'outros'])->default('carro');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->unique('placa');
            $table->index(['morador_id', 'ativo']);
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
}; 