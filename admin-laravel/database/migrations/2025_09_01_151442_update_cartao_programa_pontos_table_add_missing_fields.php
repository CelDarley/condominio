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
        Schema::table('cartao_programa_pontos', function (Blueprint $table) {
            // Adicionar campos que estão faltando para compatibilidade com o código
            if (!Schema::hasColumn('cartao_programa_pontos', 'tempo_permanencia')) {
                $table->integer('tempo_permanencia')->default(10)->after('ordem')->comment('Tempo de permanência em minutos');
            }
            
            if (!Schema::hasColumn('cartao_programa_pontos', 'tempo_deslocamento')) {
                $table->integer('tempo_deslocamento')->default(5)->after('tempo_permanencia')->comment('Tempo de deslocamento em minutos');
            }
            
            if (!Schema::hasColumn('cartao_programa_pontos', 'instrucoes_especificas')) {
                $table->text('instrucoes_especificas')->nullable()->after('tempo_deslocamento')->comment('Instruções específicas para este ponto');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cartao_programa_pontos', function (Blueprint $table) {
            // Remover campos adicionados
            $table->dropColumn(['tempo_permanencia', 'tempo_deslocamento', 'instrucoes_especificas']);
        });
    }
};
