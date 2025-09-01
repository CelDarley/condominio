<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Escala;
use App\Models\Usuario;
use App\Models\PostoTrabalho;
use App\Models\CartaoPrograma;

class EscalaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existem escalas para evitar duplicação
        if (Escala::count() > 0) {
            $this->command->info('Escalas já existem. Pulando seeder...');
            return;
        }

        // Verificar se existem usuários vigilantes
        $vigilantes = Usuario::where('tipo', 'vigilante')->where('ativo', true)->get();
        if ($vigilantes->isEmpty()) {
            $this->command->error('Nenhum vigilante encontrado. Execute UsuarioSeeder primeiro.');
            return;
        }

        // Verificar se existem postos de trabalho
        $postos = PostoTrabalho::where('ativo', true)->get();
        if ($postos->isEmpty()) {
            $this->command->error('Nenhum posto de trabalho encontrado. Execute PostoTrabalhoSeeder primeiro.');
            return;
        }

        // Obter cartões programa disponíveis
        $cartoesPrograma = CartaoPrograma::where('ativo', true)->get();

        // Definir escalas para cada vigilante
        $escalasData = [
            // João Silva - Portaria Principal
            [
                'vigilante' => 'joao@segcond.local',
                'escalas' => [
                    ['posto' => 'Portaria Principal', 'dias' => [0, 2, 4], 'cartao' => 'Programa Diurno'], // Seg, Qua, Sex
                ]
            ],
            // Maria Santos - Ronda Interna
            [
                'vigilante' => 'maria@segcond.local',
                'escalas' => [
                    ['posto' => 'Ronda Interna', 'dias' => [1, 3], 'cartao' => 'Programa Diurno'], // Ter, Qui
                    ['posto' => 'Ronda Interna', 'dias' => [5], 'cartao' => 'Programa Noturno'], // Sáb
                ]
            ],
            // Pedro Oliveira - Ronda Externa
            [
                'vigilante' => 'pedro@segcond.local',
                'escalas' => [
                    ['posto' => 'Ronda Externa', 'dias' => [0, 1, 2], 'cartao' => 'Programa Noturno'], // Seg, Ter, Qua
                ]
            ],
            // Ana Costa - Portaria Principal
            [
                'vigilante' => 'ana@segcond.local',
                'escalas' => [
                    ['posto' => 'Portaria Principal', 'dias' => [1, 3], 'cartao' => 'Programa Diurno'], // Ter, Qui
                    ['posto' => 'Portaria Principal', 'dias' => [6], 'cartao' => 'Programa Fim de Semana'], // Dom
                ]
            ],
            // Carlos Ferreira - Múltiplos postos
            [
                'vigilante' => 'carlos@segcond.local',
                'escalas' => [
                    ['posto' => 'Ronda Interna', 'dias' => [0, 4], 'cartao' => 'Programa Noturno'], // Seg, Sex
                    ['posto' => 'Ronda Externa', 'dias' => [5, 6], 'cartao' => 'Programa Diurno'], // Sáb, Dom
                ]
            ]
        ];

        foreach ($escalasData as $vigilanteData) {
            $vigilante = Usuario::where('email', $vigilanteData['vigilante'])->first();

            if (!$vigilante) {
                $this->command->warn("Vigilante {$vigilanteData['vigilante']} não encontrado.");
                continue;
            }

            foreach ($vigilanteData['escalas'] as $escalaData) {
                $posto = $postos->where('nome', $escalaData['posto'])->first();

                if (!$posto) {
                    $this->command->warn("Posto {$escalaData['posto']} não encontrado.");
                    continue;
                }

                // Encontrar cartão programa correspondente
                $cartaoPrograma = $cartoesPrograma
                    ->where('posto_trabalho_id', $posto->id)
                    ->filter(function ($cartao) use ($escalaData) {
                        return strpos($cartao->nome, $escalaData['cartao']) !== false;
                    })
                    ->first();

                foreach ($escalaData['dias'] as $diaSemana) {
                    // Verificar se já existe escala para este vigilante neste dia
                    $escalaExistente = Escala::where('usuario_id', $vigilante->id)
                        ->whereJsonContains('dias_semana', $diaSemana)
                        ->where('ativo', true)
                        ->first();

                    if ($escalaExistente) {
                        $this->command->warn("Escala já existe para {$vigilante->nome} no dia {$diaSemana}");
                        continue;
                    }

                    Escala::create([
                        'nome' => "Escala {$vigilante->nome} - {$posto->nome}",
                        'usuario_id' => $vigilante->id,
                        'posto_trabalho_id' => $posto->id,
                        'cartao_programa_id' => $cartaoPrograma ? $cartaoPrograma->id : null,
                        'data_inicio' => now()->format('Y-m-d'),
                        'horario_inicio' => '08:00',
                        'horario_fim' => '18:00',
                        'dias_semana' => [$diaSemana],
                        'ativo' => true
                    ]);

                    $this->command->info("Escala criada: {$vigilante->nome} - {$posto->nome} - Dia {$diaSemana}");
                }
            }
        }

        $this->command->info('Escalas criadas com sucesso!');
    }
}
