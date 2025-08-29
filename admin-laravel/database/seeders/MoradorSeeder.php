<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Morador;
use App\Models\Veiculo;
use Illuminate\Support\Facades\Hash;

class MoradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existem moradores para evitar duplicação
        if (Morador::count() > 0) {
            $this->command->info('Moradores já existem. Pulando seeder...');
            return;
        }

        // Criar moradores de exemplo
        $moradores = [
            [
                'nome' => 'Roberto Silva',
                'email' => 'roberto@email.com',
                'telefone' => '(31) 99888-1001',
                'endereco' => 'Condomínio Residencial Gurupi',
                'apartamento' => '101',
                'bloco' => 'A',
                'cpf' => '111.222.333-44',
                'veiculos' => [
                    ['marca' => 'Toyota', 'modelo' => 'Corolla', 'placa' => 'ABC-1234', 'cor' => 'Prata'],
                    ['marca' => 'Honda', 'modelo' => 'Civic', 'placa' => 'DEF-5678', 'cor' => 'Preto']
                ]
            ],
            [
                'nome' => 'Fernanda Costa',
                'email' => 'fernanda@email.com',
                'telefone' => '(31) 99888-1002',
                'endereco' => 'Condomínio Residencial Gurupi',
                'apartamento' => '102',
                'bloco' => 'A',
                'cpf' => '222.333.444-55',
                'veiculos' => [
                    ['marca' => 'Volkswagen', 'modelo' => 'Golf', 'placa' => 'GHI-9012', 'cor' => 'Branco']
                ]
            ],
            [
                'nome' => 'Marcos Oliveira',
                'email' => 'marcos@email.com',
                'telefone' => '(31) 99888-1003',
                'endereco' => 'Condomínio Residencial Gurupi',
                'apartamento' => '201',
                'bloco' => 'B',
                'cpf' => '333.444.555-66',
                'veiculos' => [
                    ['marca' => 'Ford', 'modelo' => 'Focus', 'placa' => 'JKL-3456', 'cor' => 'Azul']
                ]
            ],
            [
                'nome' => 'Claudia Santos',
                'email' => 'claudia@email.com',
                'telefone' => '(31) 99888-1004',
                'endereco' => 'Condomínio Residencial Gurupi',
                'apartamento' => '202',
                'bloco' => 'B',
                'cpf' => '444.555.666-77',
                'veiculos' => []
            ],
            [
                'nome' => 'Eduardo Ferreira',
                'email' => 'eduardo@email.com',
                'telefone' => '(31) 99888-1005',
                'endereco' => 'Condomínio Residencial Gurupi',
                'apartamento' => '301',
                'bloco' => 'C',
                'cpf' => '555.666.777-88',
                'veiculos' => [
                    ['marca' => 'Chevrolet', 'modelo' => 'Onix', 'placa' => 'MNO-7890', 'cor' => 'Vermelho'],
                    ['marca' => 'Fiat', 'modelo' => 'Palio', 'placa' => 'PQR-1234', 'cor' => 'Verde']
                ]
            ]
        ];

        foreach ($moradores as $moradorData) {
            $morador = Morador::create([
                'nome' => $moradorData['nome'],
                'email' => $moradorData['email'],
                'telefone' => $moradorData['telefone'],
                'endereco' => $moradorData['endereco'],
                'apartamento' => $moradorData['apartamento'],
                'bloco' => $moradorData['bloco'],
                'cpf' => $moradorData['cpf'],
                'password' => Hash::make('123456'),
                'ativo' => true
            ]);

            // Criar veículos para este morador
            foreach ($moradorData['veiculos'] as $veiculoData) {
                Veiculo::create([
                    'morador_id' => $morador->id,
                    'marca' => $veiculoData['marca'],
                    'modelo' => $veiculoData['modelo'],
                    'placa' => $veiculoData['placa'],
                    'cor' => $veiculoData['cor']
                ]);
            }
        }

        $this->command->info('Moradores e veículos criados com sucesso!');
    }
}
