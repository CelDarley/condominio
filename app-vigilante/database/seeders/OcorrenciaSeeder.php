<?php

namespace Database\Seeders;

use App\Models\Ocorrencia;
use App\Models\Usuario;
use App\Models\PostoTrabalho;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OcorrenciaSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Buscar primeiro usuário vigilante
        $vigilante = Usuario::where('tipo', 'vigilante')->first();
        $posto = PostoTrabalho::first();
        
        if (!$vigilante || !$posto) {
            $this->command->warn('Usuário vigilante ou posto não encontrado. Execute primeiro os seeders de Usuario e PostoTrabalho.');
            return;
        }

        // Ocorrência 1 - Incidente de segurança
        Ocorrencia::create([
            'usuario_id' => $vigilante->id,
            'posto_trabalho_id' => $posto->id,
            'titulo' => 'Tentativa de invasão na madrugada',
            'descricao' => 'Por volta das 02:30h foi identificada uma tentativa de invasão pelo portão secundário. O indivíduo foi flagrado tentando forçar a fechadura. Ao perceber a presença do vigilante, o suspeito fugiu rapidamente. Foram acionadas as câmeras de segurança e foi feito contato com a polícia militar. O portão não apresentou danos significativos, apenas pequenos riscos na fechadura.',
            'tipo' => 'seguranca',
            'prioridade' => 'alta',
            'status' => 'resolvida',
            'data_ocorrencia' => now()->subDays(2),
            'observacoes' => 'Polícia compareceu ao local às 03:15h. Boletim de ocorrência registrado sob nº 2025/001234.'
        ]);

        // Ocorrência 2 - Manutenção
        Ocorrencia::create([
            'usuario_id' => $vigilante->id,
            'posto_trabalho_id' => $posto->id,
            'titulo' => 'Lâmpada queimada na área de lazer',
            'descricao' => 'Durante a ronda das 20h foi constatado que uma das lâmpadas da área de lazer está queimada, deixando uma área escura próxima ao playground. É necessário providenciar a troca para manter a segurança e iluminação adequada do local.',
            'tipo' => 'manutencao',
            'prioridade' => 'media',
            'status' => 'aberta',
            'data_ocorrencia' => now()->subDays(1),
        ]);

        // Ocorrência 3 - Incidente veicular
        Ocorrencia::create([
            'usuario_id' => $vigilante->id,
            'posto_trabalho_id' => $posto->id,
            'titulo' => 'Colisão leve no estacionamento',
            'descricao' => 'Às 14:30h ocorreu uma colisão leve entre dois veículos no estacionamento interno. O veículo Corsa prata placa ABC-1234 bateu na lateral traseira do Civic preto placa XYZ-9876 durante manobra de estacionamento. Ambos os condutores são moradores do condomínio. Os danos foram apenas nos para-choques, sem ferimentos. Os condutores entraram em acordo amigável.',
            'tipo' => 'incidente',
            'prioridade' => 'baixa',
            'status' => 'fechada',
            'data_ocorrencia' => now()->subHours(6),
            'observacoes' => 'Acordo amigável firmado entre as partes. Sem necessidade de acionamento do seguro.'
        ]);

        // Ocorrência 4 - Outros
        Ocorrencia::create([
            'usuario_id' => $vigilante->id,
            'posto_trabalho_id' => null, // Sem posto específico
            'titulo' => 'Animal ferido encontrado no jardim',
            'descricao' => 'Durante a ronda matinal foi encontrado um gato ferido próximo ao jardim central. O animal apresenta ferimento na pata traseira direita, aparentemente causado por arame ou objeto cortante. O gato não tem coleira ou identificação. Foi providenciado um local seguro temporário e água para o animal.',
            'tipo' => 'outros',
            'prioridade' => 'media',
            'status' => 'em_andamento',
            'data_ocorrencia' => now()->subHours(2),
            'observacoes' => 'Contatada a clínica veterinária Dr. Bichos para remoção do animal às 10h.'
        ]);

        // Ocorrência 5 - Segurança urgente
        Ocorrencia::create([
            'usuario_id' => $vigilante->id,
            'posto_trabalho_id' => $posto->id,
            'titulo' => 'Vazamento de gás no bloco B',
            'descricao' => 'Morador do apartamento 203 reportou forte cheiro de gás no corredor do 2º andar do bloco B. Ao chegar no local foi constatado vazamento aparente vindo do apartamento 205. O morador não estava presente. Foi acionado o botão de emergência do gás, isolada a área e chamados os bombeiros. Todos os moradores do andar foram orientados a deixar temporariamente seus apartamentos.',
            'tipo' => 'seguranca',
            'prioridade' => 'urgente',
            'status' => 'resolvida',
            'data_ocorrencia' => now()->subMinutes(30),
            'observacoes' => 'Bombeiros localizaram vazamento em registro do fogão. Problema solucionado às 09:45h. Moradores liberados para retornar.'
        ]);
    }
}
