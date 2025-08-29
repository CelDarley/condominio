<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartaoPrograma;
use App\Models\CartaoProgramaPonto;
use App\Models\PostoTrabalho;
use App\Models\PontoBase;

class CartaoProgramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existem cartões para evitar duplicação
        if (CartaoPrograma::count() > 0) {
            $this->command->info('Cartões programa já existem. Pulando seeder...');
            return;
        }

        // Verificar se existem postos de trabalho
        $postos = PostoTrabalho::where('ativo', true)->get();
        if ($postos->isEmpty()) {
            $this->command->error('Nenhum posto de trabalho encontrado. Execute PostoTrabalhoSeeder primeiro.');
            return;
        }

        // Criar cartões programa para cada posto
        foreach ($postos as $posto) {
            $pontos = $posto->pontosBase()->where('ativo', true)->get();
            
            if ($pontos->isEmpty()) {
                continue;
            }

            // Cartão programa diurno
            $cartaoDiurno = CartaoPrograma::create([
                'nome' => "Programa Diurno - {$posto->nome}",
                'descricao' => "Rotina de segurança diurna para {$posto->nome}",
                'posto_trabalho_id' => $posto->id,
                'horario_inicio' => '06:00',
                'horario_fim' => '18:00',
                'tempo_total_estimado' => $pontos->count() * 15, // 15 min por ponto
                'ativo' => true,
                'configuracoes' => json_encode([
                    'notificacoes' => true,
                    'gps_obrigatorio' => true,
                    'foto_obrigatoria' => false
                ])
            ]);

            // Cartão programa noturno
            $cartaoNoturno = CartaoPrograma::create([
                'nome' => "Programa Noturno - {$posto->nome}",
                'descricao' => "Rotina de segurança noturna para {$posto->nome}",
                'posto_trabalho_id' => $posto->id,
                'horario_inicio' => '18:00',
                'horario_fim' => '06:00',
                'tempo_total_estimado' => $pontos->count() * 20, // 20 min por ponto (mais tempo à noite)
                'ativo' => true,
                'configuracoes' => json_encode([
                    'notificacoes' => true,
                    'gps_obrigatorio' => true,
                    'foto_obrigatoria' => true
                ])
            ]);

            // Adicionar pontos aos cartões
            foreach ($pontos as $index => $ponto) {
                // Pontos para cartão diurno
                CartaoProgramaPonto::create([
                    'cartao_programa_id' => $cartaoDiurno->id,
                    'ponto_base_id' => $ponto->id,
                    'ordem' => $index + 1,
                    'tempo_permanencia' => 5, // 5 minutos
                    'tempo_deslocamento' => $index === 0 ? 0 : 3, // 3 min entre pontos
                    'instrucoes_especificas' => "Verificar {$ponto->nome} - rotina diurna",
                    'obrigatorio' => true
                ]);

                // Pontos para cartão noturno
                CartaoProgramaPonto::create([
                    'cartao_programa_id' => $cartaoNoturno->id,
                    'ponto_base_id' => $ponto->id,
                    'ordem' => $index + 1,
                    'tempo_permanencia' => 8, // 8 minutos (mais tempo à noite)
                    'tempo_deslocamento' => $index === 0 ? 0 : 5, // 5 min entre pontos
                    'instrucoes_especificas' => "Verificar {$ponto->nome} - rotina noturna com atenção redobrada",
                    'obrigatorio' => true
                ]);
            }
        }

        // Cartão especial para fins de semana
        $postoPortaria = PostoTrabalho::where('nome', 'like', '%Portaria%')->first();
        if ($postoPortaria) {
            $cartaoFimSemana = CartaoPrograma::create([
                'nome' => "Programa Fim de Semana - {$postoPortaria->nome}",
                'descricao' => "Rotina especial para fins de semana e feriados",
                'posto_trabalho_id' => $postoPortaria->id,
                'horario_inicio' => '08:00',
                'horario_fim' => '20:00',
                'tempo_total_estimado' => 60,
                'ativo' => true,
                'configuracoes' => json_encode([
                    'notificacoes' => true,
                    'gps_obrigatorio' => false,
                    'foto_obrigatoria' => false
                ])
            ]);

            // Adicionar apenas pontos principais para fim de semana
            $pontosPrincipais = $postoPortaria->pontosBase()
                ->where('ativo', true)
                ->take(2)
                ->get();

            foreach ($pontosPrincipais as $index => $ponto) {
                CartaoProgramaPonto::create([
                    'cartao_programa_id' => $cartaoFimSemana->id,
                    'ponto_base_id' => $ponto->id,
                    'ordem' => $index + 1,
                    'tempo_permanencia' => 10,
                    'tempo_deslocamento' => $index === 0 ? 0 : 5,
                    'instrucoes_especificas' => "Verificação simplificada de {$ponto->nome}",
                    'obrigatorio' => true
                ]);
            }
        }

        $this->command->info('Cartões programa criados com sucesso!');
    }
}
