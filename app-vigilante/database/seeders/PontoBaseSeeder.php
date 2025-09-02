<?php

namespace Database\Seeders;

use App\Models\PontoBase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PontoBaseSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Pontos do posto Buritis (ID 1)
        PontoBase::create([
            'posto_trabalho_id' => 1,
            'nome' => 'Portaria Principal',
            'endereco' => 'Entrada principal do condomínio',
            'descricao' => 'Ponto de controle da entrada principal',
            'latitude' => -19.9191,
            'longitude' => -43.9386,
            'ativo' => true,
            'data_criacao' => now(),
        ]);

        PontoBase::create([
            'posto_trabalho_id' => 1,
            'nome' => 'Bifurcação Rua Gurupi',
            'endereco' => 'Bifurcação da Rua Gurupi',
            'descricao' => 'Ponto de controle na bifurcação',
            'latitude' => -19.9195,
            'longitude' => -43.9390,
            'ativo' => true,
            'data_criacao' => now(),
        ]);

        PontoBase::create([
            'posto_trabalho_id' => 1,
            'nome' => 'Área de Lazer',
            'endereco' => 'Área de lazer do condomínio',
            'descricao' => 'Verificação da área de lazer e piscina',
            'latitude' => -19.9188,
            'longitude' => -43.9392,
            'ativo' => true,
            'data_criacao' => now(),
        ]);

        // Pontos adicionais para outros postos
        PontoBase::create([
            'posto_trabalho_id' => 2,
            'nome' => 'Portaria Secundária',
            'endereco' => 'Entrada secundária',
            'descricao' => 'Controle da entrada secundária',
            'ativo' => true,
            'data_criacao' => now(),
        ]);

        PontoBase::create([
            'posto_trabalho_id' => 3,
            'nome' => 'Perímetro Norte',
            'endereco' => 'Perímetro norte do condomínio',
            'descricao' => 'Verificação do perímetro norte',
            'ativo' => true,
            'data_criacao' => now(),
        ]);
    }
}
