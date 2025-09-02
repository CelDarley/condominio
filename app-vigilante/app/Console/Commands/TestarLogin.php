<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestarLogin extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'teste:login';

    /**
     * The console command description.
     */
    protected $description = 'Testar login e verificar usuários disponíveis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Testando sistema de login...');
        
        // Verificar usuários vigilantes disponíveis
        $vigilantes = Usuario::where('tipo', 'vigilante')->where('ativo', true)->get();
        
        if ($vigilantes->isEmpty()) {
            $this->warn('⚠️  Nenhum usuário vigilante encontrado!');
            
            // Criar usuário de teste
            $this->info('📝 Criando usuário vigilante de teste...');
            
            $usuario = Usuario::create([
                'nome' => 'Vigilante Teste',
                'email' => 'vigilante@teste.com',
                'senha_hash' => Hash::make('123456'),
                'tipo' => 'vigilante',
                'telefone' => '(11) 99999-9999',
                'ativo' => true,
            ]);
            
            $this->info("✅ Usuário criado: {$usuario->email} / senha: 123456");
        } else {
            $this->info('👥 Usuários vigilantes disponíveis:');
            foreach ($vigilantes as $vigilante) {
                $this->line("   📧 {$vigilante->email} - {$vigilante->nome}");
            }
        }
        
        // Verificar configuração de sessão
        $this->info('⚙️  Configurações de sessão:');
        $this->line('   Driver: ' . config('session.driver'));
        $this->line('   Lifetime: ' . config('session.lifetime') . ' minutos');
        
        // Testar URL de login
        $this->info('🌐 URLs de teste:');
        $this->line('   Login: http://localhost:8001/login');
        $this->line('   Alt: http://localhost:8001/auth/login');
        
        $this->info('🎯 Dicas para resolver erro 419:');
        $this->line('   1. Limpe cookies do navegador');
        $this->line('   2. Tente em janela anônima');
        $this->line('   3. Verifique se JavaScript está habilitado');
        $this->line('   4. Use F12 > Network para ver requisições');
        
        $this->info('🔧 Correções implementadas para erro 419:');
        $this->line('   ✅ Middleware CSRF atualizado');
        $this->line('   ✅ Rotas de presença excluídas da verificação CSRF');
        $this->line('   ✅ Tratamento de erro 419 em JavaScript');
        $this->line('   ✅ Renovação automática de token CSRF');
        $this->line('   ✅ Headers X-Requested-With adicionados');
        
        $this->info('🧪 Para testar:');
        $this->line('   1. Acesse: http://localhost:8001/login');
        $this->line('   2. Faça login com: marcio@gmail.com');
        $this->line('   3. Vá para a página do posto');
        $this->line('   4. Tente registrar chegada/saída');
        $this->line('   5. Verifique console do navegador (F12)');
        
        $this->info('🔄 Novos ciclos implementados:');
        $this->line('   ✅ 10 verificações ao longo do turno');
        $this->line('   ✅ Horários específicos para cada ponto');
        $this->line('   ✅ Múltiplas ocorrências dos mesmos locais');
        $this->line('   ✅ Instruções específicas para cada verificação');
        
        $this->info('📍 Exemplo de ciclos:');
        $this->line('   18:00-18:40 Bifurcação Rua Gurupi (teste)');
        $this->line('   19:00-19:45 Portaria Principal (ver veículos)');
        $this->line('   20:30-20:55 Portaria Principal (Ver atras das árvores)');
        $this->line('   21:45-22:25 Bifurcação Rua Gurupi (teste)');
        $this->line('   ... e mais 6 verificações até 05:55');
        
        return 0;
    }
}
