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
            // Verificar e remover campos desnecessários apenas se existirem
            if (Schema::hasColumn('ponto_base', 'horario_inicio')) {
                $table->dropColumn('horario_inicio');
            }
            if (Schema::hasColumn('ponto_base', 'horario_fim')) {
                $table->dropColumn('horario_fim');
            }
            if (Schema::hasColumn('ponto_base', 'tempo_permanencia')) {
                $table->dropColumn('tempo_permanencia');
            }
            if (Schema::hasColumn('ponto_base', 'ordem')) {
                $table->dropColumn('ordem');
            }
            if (Schema::hasColumn('ponto_base', 'instrucoes')) {
                $table->dropColumn('instrucoes');
            }

            // Adicionar novos campos apenas se não existirem
            if (!Schema::hasColumn('ponto_base', 'endereco')) {
                $table->string('endereco', 255)->after('nome');
            }
            if (!Schema::hasColumn('ponto_base', 'latitude')) {
                $table->decimal('latitude', 10, 6)->nullable()->after('descricao');
            }
            if (!Schema::hasColumn('ponto_base', 'longitude')) {
                $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('ponto_base', 'qr_code')) {
                $table->string('qr_code', 100)->nullable()->after('longitude');
            }
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
