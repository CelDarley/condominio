<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Morador;
use App\Models\Veiculo;

class MoradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar moradores de exemplo
        $moradores = [
            [
                'nome' => 'João Silva',
                'telefone' => '(11) 99999-1111',
                'endereco' => 'Rua A, 123 - Apto 101',
                'email' => 'joao.silva@email.com',
                'veiculos' => [
                    [
                        'placa' => 'ABC-1234',
                        'tipo' => 'Carro',
                        'cor' => 'Prata',
                        'marca' => 'Toyota',
                        'modelo' => 'Corolla'
                    ]
                ]
            ],
            [
                'nome' => 'Maria Santos',
                'telefone' => '(11) 99999-2222',
                'endereco' => 'Rua B, 456 - Apto 202',
                'email' => 'maria.santos@email.com',
                'veiculos' => [
                    [
                        'placa' => 'DEF-5678',
                        'tipo' => 'Moto',
                        'cor' => 'Vermelha',
                        'marca' => 'Honda',
                        'modelo' => 'CG 150'
                    ]
                ]
            ],
            [
                'nome' => 'Pedro Oliveira',
                'telefone' => '(11) 99999-3333',
                'endereco' => 'Rua C, 789 - Apto 303',
                'email' => 'pedro.oliveira@email.com',
                'veiculos' => [
                    [
                        'placa' => 'GHI-9012',
                        'tipo' => 'Carro',
                        'cor' => 'Preto',
                        'marca' => 'Volkswagen',
                        'modelo' => 'Golf'
                    ]
                ]
            ]
        ];

        foreach ($moradores as $moradorData) {
            $veiculos = $moradorData['veiculos'];
            unset($moradorData['veiculos']);
            
            $morador = Morador::create($moradorData);
            
            foreach ($veiculos as $veiculoData) {
                $veiculoData['morador_id'] = $morador->id;
                Veiculo::create($veiculoData);
            }
        }
    }
}
