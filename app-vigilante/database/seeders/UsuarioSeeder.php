<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Usuários vigilantes
        Usuario::create([
            'nome' => 'Marcio Roberto',
            'email' => 'marcio@gmail.com',
            'senha_hash' => Hash::make('123456'),
            'tipo' => 'vigilante',
            'ativo' => true,
            'telefone' => '(11) 99999-9999',
            'data_criacao' => now(),
        ]);

        Usuario::create([
            'nome' => 'João Silva',
            'email' => 'joao@vigilante.com',
            'senha_hash' => Hash::make('123456'),
            'tipo' => 'vigilante',
            'ativo' => true,
            'telefone' => '(11) 98888-8888',
            'data_criacao' => now(),
        ]);

        Usuario::create([
            'nome' => 'Carlos Santos',
            'email' => 'carlos@vigilante.com',
            'senha_hash' => Hash::make('123456'),
            'tipo' => 'vigilante',
            'ativo' => true,
            'telefone' => '(11) 97777-7777',
            'data_criacao' => now(),
        ]);

        // Usuário admin
        Usuario::create([
            'nome' => 'Administrador',
            'email' => 'admin@sistema.com',
            'senha_hash' => Hash::make('admin123'),
            'tipo' => 'admin',
            'ativo' => true,
            'telefone' => '(11) 96666-6666',
            'data_criacao' => now(),
        ]);
    }
}
