<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeders do sistema de condomínio...');
        
        // Ordem correta dos seeders para respeitar as dependências
        $this->call([
            // 1. Usuários (não depende de ninguém)
            UsuarioSeeder::class,
            
            // 2. Postos de trabalho e pontos base (não dependem de usuários)
            PostoTrabalhoSeeder::class,
            
            // 3. Moradores e veículos (não dependem de postos)
            MoradorSeeder::class,
            
            // 4. Cartões programa (dependem de postos de trabalho)
            CartaoProgramaSeeder::class,
            
            // 5. Escalas (dependem de usuários, postos e cartões)
            EscalaSeeder::class,
        ]);

        $this->command->info('✅ Todos os seeders foram executados com sucesso!');
        $this->command->info('');
        $this->command->info('📊 Resumo dos dados criados:');
        $this->command->info('   👥 Usuários: ' . \App\Models\Usuario::count() . ' (1 admin + vigilantes)');
        $this->command->info('   🏠 Moradores: ' . \App\Models\Morador::count());
        $this->command->info('   🚗 Veículos: ' . \App\Models\Veiculo::count());
        $this->command->info('   📍 Postos: ' . \App\Models\PostoTrabalho::count());
        $this->command->info('   📌 Pontos: ' . \App\Models\PontoBase::count());
        $this->command->info('   📋 Cartões: ' . \App\Models\CartaoPrograma::count());
        $this->command->info('   📅 Escalas: ' . \App\Models\Escala::count());
        $this->command->info('');
        $this->command->info('🔑 Credenciais de acesso:');
        $this->command->info('   Admin: admin@segcond.local / admin123');
        $this->command->info('   Vigilantes: [nome]@segcond.local / 123456');
        $this->command->info('   Moradores: [email cadastrado] / 123456');
    }
}
