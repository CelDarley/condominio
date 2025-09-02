<?php

namespace Database\Seeders;

use App\Models\CameraCompartilhada;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CameraCompartilhadaSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Câmeras do Apartamento 101 - Maria Silva
        CameraCompartilhada::create([
            'nome_morador' => 'Maria Silva',
            'apartamento' => '101',
            'titulo_camera' => 'Entrada Principal Apto 101',
            'descricao' => 'Câmera posicionada na porta de entrada do apartamento para monitoramento de acesso.',
            'url_imagem' => 'https://picsum.photos/800/600?random=1',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=1',
            'tipo' => 'entrada',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subDays(5),
            'observacoes' => 'Visão clara do corredor e elevador.'
        ]);

        CameraCompartilhada::create([
            'nome_morador' => 'Maria Silva',
            'apartamento' => '101',
            'titulo_camera' => 'Varanda Apto 101',
            'descricao' => 'Monitoramento da varanda com vista para área comum.',
            'url_imagem' => 'https://picsum.photos/800/600?random=2',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=2',
            'tipo' => 'varanda',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subDays(4),
        ]);

        // Câmeras do Apartamento 205 - João Santos
        CameraCompartilhada::create([
            'nome_morador' => 'João Santos',
            'apartamento' => '205',
            'titulo_camera' => 'Garagem Vaga 15',
            'descricao' => 'Câmera de segurança da vaga de garagem nº 15.',
            'url_imagem' => 'https://picsum.photos/800/600?random=3',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=3',
            'tipo' => 'garagem',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subDays(3),
            'observacoes' => 'Monitoramento 24h da vaga e veículos próximos.'
        ]);

        CameraCompartilhada::create([
            'nome_morador' => 'João Santos',
            'apartamento' => '205',
            'titulo_camera' => 'Entrada Apto 205',
            'descricao' => 'Segurança da entrada do apartamento 205.',
            'url_imagem' => 'https://picsum.photos/800/600?random=4',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=4',
            'tipo' => 'entrada',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subDays(2),
        ]);

        // Câmeras do Apartamento 308 - Ana Costa
        CameraCompartilhada::create([
            'nome_morador' => 'Ana Costa',
            'apartamento' => '308',
            'titulo_camera' => 'Varanda Vista Piscina',
            'descricao' => 'Câmera com vista privilegiada da área de lazer e piscina.',
            'url_imagem' => 'https://picsum.photos/800/600?random=5',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=5',
            'tipo' => 'area_comum',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subDays(1),
            'observacoes' => 'Excelente visão da área de lazer, piscina e playground.'
        ]);

        CameraCompartilhada::create([
            'nome_morador' => 'Ana Costa',
            'apartamento' => '308',
            'titulo_camera' => 'Porta de Entrada 308',
            'descricao' => 'Monitoramento da entrada do apartamento.',
            'url_imagem' => 'https://picsum.photos/800/600?random=6',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=6',
            'tipo' => 'entrada',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subHours(12),
        ]);

        CameraCompartilhada::create([
            'nome_morador' => 'Ana Costa',
            'apartamento' => '308',
            'titulo_camera' => 'Corredor 3º Andar',
            'descricao' => 'Vista do corredor do terceiro andar.',
            'url_imagem' => 'https://picsum.photos/800/600?random=7',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=7',
            'tipo' => 'area_comum',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subHours(6),
        ]);

        // Câmeras do Apartamento 412 - Carlos Pereira
        CameraCompartilhada::create([
            'nome_morador' => 'Carlos Pereira',
            'apartamento' => '412',
            'titulo_camera' => 'Garagem Vaga 28',
            'descricao' => 'Segurança da vaga 28 no subsolo.',
            'url_imagem' => 'https://picsum.photos/800/600?random=8',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=8',
            'tipo' => 'garagem',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subHours(3),
            'observacoes' => 'Região mais escura do subsolo, importante para segurança.'
        ]);

        // Câmeras do Apartamento 503 - Fernanda Lima
        CameraCompartilhada::create([
            'nome_morador' => 'Fernanda Lima',
            'apartamento' => '503',
            'titulo_camera' => 'Sacada Cobertura',
            'descricao' => 'Vista panorâmica da cobertura com visão de todo o condomínio.',
            'url_imagem' => 'https://picsum.photos/800/600?random=9',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=9',
            'tipo' => 'outros',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subHours(1),
            'observacoes' => 'Visão estratégica de todo o entorno do prédio.'
        ]);

        CameraCompartilhada::create([
            'nome_morador' => 'Fernanda Lima',
            'apartamento' => '503',
            'titulo_camera' => 'Entrada Cobertura',
            'descricao' => 'Acesso à cobertura e escada de emergência.',
            'url_imagem' => 'https://picsum.photos/800/600?random=10',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=10',
            'tipo' => 'entrada',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subMinutes(30),
        ]);

        // Câmeras do Apartamento 105 - Roberto Alves
        CameraCompartilhada::create([
            'nome_morador' => 'Roberto Alves',
            'apartamento' => '105',
            'titulo_camera' => 'Varanda Térrea',
            'descricao' => 'Monitoramento do jardim e entrada lateral.',
            'url_imagem' => 'https://picsum.photos/800/600?random=11',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=11',
            'tipo' => 'varanda',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subMinutes(15),
            'observacoes' => 'Vista do jardim e acesso lateral do prédio.'
        ]);

        CameraCompartilhada::create([
            'nome_morador' => 'Roberto Alves',
            'apartamento' => '105',
            'titulo_camera' => 'Portão de Serviço',
            'descricao' => 'Câmera direcionada para o portão de serviço.',
            'url_imagem' => 'https://picsum.photos/800/600?random=12',
            'url_thumbnail' => 'https://picsum.photos/200/150?random=12',
            'tipo' => 'area_comum',
            'ativa' => true,
            'compartilhada_vigilancia' => true,
            'data_compartilhamento' => now()->subMinutes(5),
        ]);
    }
}
