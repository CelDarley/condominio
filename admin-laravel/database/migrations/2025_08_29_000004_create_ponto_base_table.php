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
        Schema::create('ponto_base', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->string('endereco')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('qr_code')->nullable();
            $table->foreignId('posto_trabalho_id')->constrained('posto_trabalho')->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['ativo', 'nome']);
            $table->index('posto_trabalho_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ponto_base');
    }
};
