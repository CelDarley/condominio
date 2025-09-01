<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ponto_base', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome', 100);
            $table->string('endereco');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('posto_trabalho_id');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index(['posto_trabalho_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ponto_base');
    }
};
