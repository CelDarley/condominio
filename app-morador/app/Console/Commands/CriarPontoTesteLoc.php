<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CriarPontoTesteLoc extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'teste:criar-ponto-localizacao';

    /**
     * The console command description.
     */
    protected $description = 'Criar ponto base de teste na localização -19.9720213,-43.9597552';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Criando ponto base de teste...');
        
        // Coordenadas do teste
        $latitude = -19.9720213;
        $longitude = -43.9597552;
        
        try {
            // Verificar se já existe
            $pontoExistente = DB::table('ponto_base')
                ->where('latitude', $latitude)
                ->where('longitude', $longitude)
                ->where('nome', 'Ponto Teste Localização')
                ->first();
                
            if ($pontoExistente) {
                $this->warn('Ponto de teste já existe!');
                $this->info("ID: {$pontoExistente->id}");
                return 0;
            }
            
            // Buscar um posto de trabalho para associar
            $posto = DB::table('posto_trabalho')->where('ativo', true)->first();
            
            if (!$posto) {
                $this->error('Nenhum posto de trabalho ativo encontrado!');
                return 1;
            }
            
            // Criar o ponto base
            $pontoId = DB::table('ponto_base')->insertGetId([
                'posto_trabalho_id' => $posto->id,
                'nome' => 'Ponto Teste Localização',
                'endereco' => 'Localização de teste para vigilante',
                'descricao' => 'Ponto base criado para teste de localização em tempo real',
                'latitude' => $latitude,
                'longitude' => $longitude,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info("✅ Ponto base de teste criado com sucesso!");
            $this->info("🗺️  ID: {$pontoId}");
            $this->info("📍 Coordenadas: {$latitude}, {$longitude}");
            $this->info("🏢 Posto: {$posto->nome}");
            $this->info("🌐 Acesse o dashboard em http://localhost:8002 para ver no mapa");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Erro ao criar ponto de teste: ' . $e->getMessage());
            return 1;
        }
    }
}
