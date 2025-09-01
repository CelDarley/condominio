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
            // Adicionar campos que estão faltando
            if (!Schema::hasColumn('ponto_base', 'endereco')) {
                $table->string('endereco')->nullable()->after('descricao');
            }

            if (!Schema::hasColumn('ponto_base', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('endereco');
            }

            if (!Schema::hasColumn('ponto_base', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }

            if (!Schema::hasColumn('ponto_base', 'qr_code')) {
                $table->string('qr_code')->nullable()->after('longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ponto_base', function (Blueprint $table) {
            // Remover campos adicionados
            $table->dropColumn(['endereco', 'latitude', 'longitude', 'qr_code']);
        });
    }
};
