<?php

namespace App\Http\Controllers;

use App\Models\Escala;
use App\Models\EscalaDiaria;
use App\Models\PostoTrabalho;
use App\Models\CartaoPrograma;
use App\Models\Ocorrencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Middleware será aplicado diretamente nas rotas

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Verificar se o usuário está autenticado
            if (!$user) {
                return redirect()->route('login')->with('error', 'Usuário não autenticado.');
            }
            
            // Obter data base (hoje por padrão ou data passada via parâmetro)
            $dataBase = $request->get('data') ? 
                \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('data')) : 
                now();
            
            // Usar a nova lógica de escala efetiva (que considera ajustes diários)
            $escala = EscalaDiaria::getEscalaVigilante($dataBase->format('Y-m-d'), $user->id);
        } catch (\Exception $e) {
            // Em caso de erro, retornar dados vazios para não quebrar a view
            \Log::error('Erro no dashboard: ' . $e->getMessage());
            
            $escala = null;
            $dataBase = now();
            $user = Auth::user(); // Garantir que user está definido
        }

        $postos = collect();
        $cartaoPrograma = null;

        if ($escala) {
            $postos = collect([$escala->postoTrabalho]);
            // Buscar cartão programa pela escala
            $cartaoPrograma = $escala->cartaoPrograma ?? null;
        }

        // Gerar array de 7 dias centrado na data atual
        $diasCarrossel = [];
        for ($i = -3; $i <= 3; $i++) {
            $data = $dataBase->copy()->addDays($i);
            
            // Verificar se há escala para este dia (considerando ajustes) apenas se user existe
            $escalaData = $user ? EscalaDiaria::getEscalaVigilante($data->format('Y-m-d'), $user->id) : null;
            
            $diasCarrossel[] = [
                'data' => $data->format('Y-m-d'),
                'nome' => $this->getNomeDiaCurto($data->dayOfWeek == 0 ? 6 : $data->dayOfWeek - 1),
                'dia' => $data->day,
                'e_hoje' => $data->isToday(),
                'e_selecionado' => $data->format('Y-m-d') === $dataBase->format('Y-m-d'),
                'tem_ajuste' => $escalaData ? true : false, // Simplificado por enquanto
            ];
        }

        // Contar ocorrências abertas do usuário
        $ocorrenciasAbertas = Ocorrencia::where('usuario_id', $user->id)
            ->whereIn('status', ['aberta', 'em_andamento'])
            ->count();

        return view('dashboard.index-simple', [
            'user' => $user,
            'escalaDiaria' => $escala,
            'postos' => $postos,
            'cartaoPrograma' => $cartaoPrograma,
            'diasCarrossel' => $diasCarrossel,
            'dataBase' => $dataBase,
            'ocorrenciasAbertas' => $ocorrenciasAbertas
        ]);
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