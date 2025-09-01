<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user
                            {--name= : Nome do administrador}
                            {--email= : Email do administrador}
                            {--password= : Senha do administrador}
                            {--phone= : Telefone do administrador (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um novo usuário administrador no sistema SegCond';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Criando usuário administrador no SegCond...');
        $this->newLine();

        // Obter dados do usuário
        $name = $this->option('name') ?: $this->ask('Nome do administrador');
        $email = $this->option('email') ?: $this->ask('Email do administrador');
        $password = $this->option('password') ?: $this->secret('Senha do administrador');
        $phone = $this->option('phone') ?: $this->ask('Telefone (opcional)', null);

        // Validar dados
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:120',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Dados inválidos:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  • ' . $error);
            }
            return 1;
        }

        // Verificar se o email já existe
        $existingUser = DB::table('usuario')->where('email', $email)->first();
        if ($existingUser) {
            $this->error("❌ Já existe um usuário com o email: {$email}");
            return 1;
        }

        try {
            // Criar usuário administrador
            $userId = DB::table('usuario')->insertGetId([
                'nome' => $name,
                'email' => $email,
                'senha_hash' => Hash::make($password),
                'tipo' => 'admin',
                'ativo' => true,
                'telefone' => $phone,
            ]);

            $this->newLine();
            $this->info('✅ Usuário administrador criado com sucesso!');
            $this->newLine();

            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID', $userId],
                    ['Nome', $name],
                    ['Email', $email],
                    ['Tipo', 'admin'],
                    ['Status', 'ativo'],
                    ['Telefone', $phone ?: 'Não informado'],
                    ['Data de Criação', now()->format('d/m/Y H:i:s')],
                ]
            );

            $this->newLine();
            $this->info('🌐 Acesse o painel admin em: http://localhost:8000/admin');
            $this->info('📧 Use o email e senha informados para fazer login.');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erro ao criar usuário administrador: ' . $e->getMessage());
            return 1;
        }
    }
}
