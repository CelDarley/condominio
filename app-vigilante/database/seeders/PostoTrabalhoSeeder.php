<?php

namespace Database\Seeders;

use App\Models\PostoTrabalho;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostoTrabalhoSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        PostoTrabalho::create([
            'nome' => 'Buritis',
            'descricao' => 'Posto de vigilância principal do condomínio Buritis',
            'ativo' => true,
        ]);

        PostoTrabalho::create([
            'nome' => 'Portaria Secundária',
            'descricao' => 'Posto de vigilância da portaria secundária',
            'ativo' => true,
        ]);

        PostoTrabalho::create([
            'nome' => 'Ronda Noturna',
            'descricao' => 'Posto de vigilância para ronda noturna',
            'ativo' => true,
        ]);
    }
}
