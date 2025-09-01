<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existem usuários para evitar duplicação
        if (Usuario::count() > 0) {
            $this->command->info('Usuários já existem. Pulando seeder...');
            return;
        }

        // Criar usuário admin principal
        Usuario::create([
            'nome' => 'Administrador',
            'email' => 'admin@segcond.local',
            'senha_hash' => Hash::make('admin123'),
            'tipo' => 'admin',
            'telefone' => '(31) 99999-0001',
            'ativo' => true,
        ]);

        // Criar usuários vigilantes de exemplo
        $vigilantes = [
            [
                'nome' => 'João Silva',
                'email' => 'joao@segcond.local',
                'telefone' => '(31) 99999-1001',
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'maria@segcond.local',
                'telefone' => '(31) 99999-1002',
            ],
            [
                'nome' => 'Pedro Oliveira',
                'email' => 'pedro@segcond.local',
                'telefone' => '(31) 99999-1003',
            ],
            [
                'nome' => 'Ana Costa',
                'email' => 'ana@segcond.local',
                'telefone' => '(31) 99999-1004',
            ],
            [
                'nome' => 'Carlos Ferreira',
                'email' => 'carlos@segcond.local',
                'telefone' => '(31) 99999-1005',
            ]
        ];

        foreach ($vigilantes as $vigilante) {
            Usuario::create([
                'nome' => $vigilante['nome'],
                'email' => $vigilante['email'],
                'senha_hash' => Hash::make('123456'),
                'tipo' => 'vigilante',
                'telefone' => $vigilante['telefone'],
                'ativo' => true,
            ]);
        }

        $this->command->info('Usuários criados com sucesso!');
    }
}
