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
        Schema::create('moradores', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->foreignId('usuario_id')->nullable()->constrained('usuario')->onDelete('set null');
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('telefone')->nullable();
            $table->string('endereco');
            $table->string('apartamento');
            $table->string('bloco')->nullable();
            $table->string('cpf')->unique();
            $table->string('password');
            $table->boolean('ativo')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index(['email', 'ativo']);
            $table->index(['apartamento', 'bloco']);
            $table->index('cpf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moradores');
    }
}; 