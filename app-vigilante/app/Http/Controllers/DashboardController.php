<?php

namespace App\Http\Controllers;

use App\Models\Escala;
use App\Models\EscalaDiaria;
use App\Models\PostoTrabalho;
use App\Models\CartaoPrograma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Middleware será aplicado diretamente nas rotas

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Obter data base (hoje por padrão ou data passada via parâmetro)
        $dataBase = $request->get('data') ? 
            \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('data')) : 
            now();
        
        // Usar a nova lógica de escala efetiva (que considera ajustes diários)
        $escala = EscalaDiaria::getEscalaVigilante($dataBase->format('Y-m-d'), $user->id);

        $postos = collect();
        $cartaoPrograma = null;

        if ($escala) {
            $postos = collect([$escala->postoTrabalho]);
            $cartaoPrograma = $escala->cartaoPrograma;
        }

        // Gerar array de 7 dias centrado na data atual
        $diasCarrossel = [];
        for ($i = -3; $i <= 3; $i++) {
            $data = $dataBase->copy()->addDays($i);
            
            // Verificar se há escala para este dia (considerando ajustes)
            $escalaData = EscalaDiaria::getEscalaVigilante($data->format('Y-m-d'), $user->id);
            
            $diasCarrossel[] = [
                'data' => $data->format('Y-m-d'),
                'nome' => $this->getNomeDiaCurto($data->dayOfWeek == 0 ? 6 : $data->dayOfWeek - 1),
                'dia' => $data->day,
                'e_hoje' => $data->isToday(),
                'e_selecionado' => $data->isSameDay($dataBase),
                'tem_escala' => $escalaData ? true : false,
                'tem_ajuste' => $escalaData && isset($escalaData->tem_ajuste) && $escalaData->tem_ajuste
            ];
        }

        return view('dashboard.index', compact(
            'user', 
            'escala', 
            'postos', 
            'cartaoPrograma', 
            'diasCarrossel',
            'dataBase'
        ));
    }

    public function postosPorData(Request $request, $data)
    {
        $user = Auth::user();
        
        // Usar a nova lógica de escala efetiva
        $escala = EscalaDiaria::getEscalaVigilante($data, $user->id);

        if ($escala) {
            $posto = $escala->postoTrabalho;
            $cartaoPrograma = $escala->cartaoPrograma;
            
            $result = [
                'posto' => $posto ? [
                    'id' => $posto->id,
                    'nome' => $posto->nome,
                    'descricao' => $posto->descricao
                ] : null,
                'cartao_programa' => $cartaoPrograma ? [
                    'nome' => $cartaoPrograma->nome,
                    'descricao' => $cartaoPrograma->descricao,
                    'horario_inicio' => $cartaoPrograma->getHorarioInicioFormatado(),
                    'horario_fim' => $cartaoPrograma->getHorarioFimFormatado()
                ] : null,
                'tem_ajuste' => isset($escala->tem_ajuste) && $escala->tem_ajuste,
                'info_ajuste' => isset($escala->ajuste_diario) ? [
                    'motivo' => $escala->ajuste_diario->motivo,
                    'usuario_original' => $escala->ajuste_diario->usuarioOriginal->nome ?? 'N/A'
                ] : null
            ];
            
            return response()->json($result);
        }

        return response()->json(['posto' => null, 'cartao_programa' => null, 'tem_ajuste' => false]);
    }

    public function getNomeDia($dia)
    {
        $dias = [
            0 => 'Segunda-feira',
            1 => 'Terça-feira', 
            2 => 'Quarta-feira',
            3 => 'Quinta-feira',
            4 => 'Sexta-feira',
            5 => 'Sábado',
            6 => 'Domingo'
        ];

        return $dias[$dia] ?? 'Dia inválido';
    }

    public function getNomeDiaCurto($dia)
    {
        $dias = [
            0 => 'Seg',
            1 => 'Ter',
            2 => 'Qua',
            3 => 'Qui',
            4 => 'Sex',
            5 => 'Sáb',
            6 => 'Dom'
        ];

        return $dias[$dia] ?? '?';
    }
} 