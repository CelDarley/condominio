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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->string('nome', 100);
            $table->string('email', 120)->unique();
            $table->string('senha_hash', 255);
            $table->enum('tipo', ['admin', 'vigilante', 'morador']);
            $table->string('telefone', 20)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps(); // created_at, updated_at
            
            $table->index(['email', 'ativo']);
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
}; 