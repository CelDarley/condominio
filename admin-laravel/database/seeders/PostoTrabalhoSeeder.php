<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostoTrabalho;
use App\Models\PontoBase;

class PostoTrabalhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existem postos para evitar duplicação
        if (PostoTrabalho::count() > 0) {
            $this->command->info('Postos de trabalho já existem. Pulando seeder...');
            return;
        }

        // Criar postos de trabalho
        $postos = [
            [
                'nome' => 'Portaria Principal',
                'descricao' => 'Portaria de entrada e saída principal do condomínio',
                'pontos' => [
                    [
                        'nome' => 'Guarita Principal',
                        'endereco' => 'Rua Principal, 100 - Entrada',
                        'descricao' => 'Controle de acesso principal',
                        'latitude' => -19.9029,
                        'longitude' => -43.9572
                    ],
                    [
                        'nome' => 'Cancela de Veículos',
                        'endereco' => 'Rua Principal, 100 - Estacionamento',
                        'descricao' => 'Controle de entrada e saída de veículos',
                        'latitude' => -19.9030,
                        'longitude' => -43.9573
                    ]
                ]
            ],
            [
                'nome' => 'Ronda Interna',
                'descricao' => 'Ronda de segurança nas áreas internas do condomínio',
                'pontos' => [
                    [
                        'nome' => 'Área de Lazer',
                        'endereco' => 'Área Interna - Piscina',
                        'descricao' => 'Verificação da área de lazer e piscina',
                        'latitude' => -19.9025,
                        'longitude' => -43.9575
                    ],
                    [
                        'nome' => 'Playground',
                        'endereco' => 'Área Interna - Playground',
                        'descricao' => 'Verificação do playground infantil',
                        'latitude' => -19.9027,
                        'longitude' => -43.9574
                    ],
                    [
                        'nome' => 'Garagem Subsolo',
                        'endereco' => 'Subsolo - Garagem',
                        'descricao' => 'Ronda na garagem do subsolo',
                        'latitude' => -19.9031,
                        'longitude' => -43.9571
                    ]
                ]
            ],
            [
                'nome' => 'Ronda Externa',
                'descricao' => 'Ronda de segurança no perímetro externo',
                'pontos' => [
                    [
                        'nome' => 'Muro Norte',
                        'endereco' => 'Perímetro Norte',
                        'descricao' => 'Verificação do muro norte',
                        'latitude' => -19.9020,
                        'longitude' => -43.9570
                    ],
                    [
                        'nome' => 'Muro Sul',
                        'endereco' => 'Perímetro Sul',
                        'descricao' => 'Verificação do muro sul',
                        'latitude' => -19.9035,
                        'longitude' => -43.9576
                    ],
                    [
                        'nome' => 'Portão de Serviço',
                        'endereco' => 'Rua Lateral, s/n',
                        'descricao' => 'Controle do portão de serviço',
                        'latitude' => -19.9033,
                        'longitude' => -43.9570
                    ]
                ]
            ]
        ];

        foreach ($postos as $postoData) {
            $posto = PostoTrabalho::create([
                'nome' => $postoData['nome'],
                'descricao' => $postoData['descricao'],
                'ativo' => true
            ]);

            // Criar pontos base para este posto
            foreach ($postoData['pontos'] as $ordem => $pontoData) {
                PontoBase::create([
                    'posto_id' => $posto->id,
                    'nome' => $pontoData['nome'],
                    'endereco' => $pontoData['endereco'],
                    'descricao' => $pontoData['descricao'],
                    'latitude' => $pontoData['latitude'],
                    'longitude' => $pontoData['longitude'],
                    'ordem' => $ordem + 1,
                    'ativo' => true
                ]);
            }
        }

        $this->command->info('Postos de trabalho e pontos base criados com sucesso!');
    }
}
