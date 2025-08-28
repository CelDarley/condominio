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
                Schema::table('ponto_base', function (Blueprint $table) {
            // Remover campos desnecessários
            $table->dropColumn([
                'horario_inicio',
                'horario_fim',
                'tempo_permanencia',
                'ordem',
                'instrucoes'
            ]);

            // Adicionar novos campos
            $table->string('endereco', 255)->after('nome');
            $table->decimal('latitude', 10, 6)->nullable()->after('descricao');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
            $table->string('qr_code', 100)->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ponto_base', function (Blueprint $table) {
            // Restaurar campos antigos
            $table->time('horario_inicio')->after('descricao');
            $table->time('horario_fim')->after('horario_inicio');
            $table->integer('tempo_permanencia')->after('horario_fim');
            $table->text('instrucoes')->nullable()->after('tempo_permanencia');
            $table->integer('ordem')->default(0)->after('instrucoes');

            // Remover novos campos
            $table->dropColumn(['endereco', 'latitude', 'longitude', 'qr_code']);
        });
    }
};
