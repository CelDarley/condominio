<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EscalaDiaria;
use App\Models\Escala;
use App\Models\Usuario;
use App\Models\PostoTrabalho;
use App\Models\CartaoPrograma;
use Carbon\Carbon;

class EscalaDiariaController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $mes = $request->get('mes', now()->month);
        $ano = $request->get('ano', now()->year);
        $vigilanteSelecionado = $request->get('vigilante');

        $dataInicio = Carbon::create($ano, $mes, 1);
        $dataFim = $dataInicio->copy()->endOfMonth();

        // Buscar ajustes do mês
        $ajustes = EscalaDiaria::with(['usuarioOriginal', 'usuarioSubstituto', 'postoTrabalho'])
            ->porPeriodo($dataInicio, $dataFim)
            ->ativos()
            ->get()
            ->groupBy('data');

        // Buscar todos os vigilantes para o filtro
        $vigilantes = Usuario::where('tipo', 'vigilante')
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('admin.escala-diaria.index', compact('mes', 'ano', 'ajustes', 'dataInicio', 'dataFim', 'vigilantes', 'vigilanteSelecionado'));
    }

    public function calendario(Request $request)
    {
        $data = $request->get('data', now()->format('Y-m-d'));
        $dataCarbon = Carbon::parse($data);

        // Obter escalas efetivas para este dia
        $escalasEfetivas = EscalaDiaria::getEscalaEfetiva($data);

        // Buscar todos os usuários disponíveis para substituição
        $usuariosDisponiveis = Usuario::where('ativo', true)
            ->where('tipo', 'vigilante')
            ->orderBy('nome')
            ->get();

        // Buscar postos de trabalho
        $postos = PostoTrabalho::ativos()->get();

        return response()->json([
            'data' => $data,
            'data_formatada' => $dataCarbon->format('d/m/Y'),
            'dia_semana' => $dataCarbon->locale('pt_BR')->dayName,
            'escalas' => $escalasEfetivas->map(function ($escala) {
                return [
                    'id' => $escala->id,
                    'usuario' => [
                        'id' => $escala->usuario->id,
                        'nome' => $escala->usuario->nome
                    ],
                    'posto' => [
                        'id' => $escala->postoTrabalho->id,
                        'nome' => $escala->postoTrabalho->nome
                    ],
                    'cartao_programa' => $escala->cartaoPrograma ? [
                        'id' => $escala->cartaoPrograma->id,
                        'nome' => $escala->cartaoPrograma->nome,
                        'horario' => $escala->cartaoPrograma->horario_inicio . ' - ' . $escala->cartaoPrograma->horario_fim
                    ] : null,
                    'tem_ajuste' => isset($escala->ajuste_diario),
                    'ajuste_diario' => isset($escala->ajuste_diario) ? [
                        'id' => $escala->ajuste_diario->id,
                        'motivo' => $escala->ajuste_diario->motivo,
                        'usuario_original' => $escala->ajuste_diario->usuarioOriginal->nome
                    ] : null
                ];
            }),
            'usuarios_disponiveis' => $usuariosDisponiveis->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nome' => $user->nome
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|date',
            'escala_original_id' => 'required|exists:escala,id',
            'usuario_substituto_id' => 'required|exists:usuario,id',
            'cartao_programa_id' => 'nullable|exists:cartao_programas,id',
            'motivo' => 'nullable|string|max:500'
        ]);

        try {
            // Buscar escala original
            $escalaOriginal = Escala::findOrFail($request->escala_original_id);

            // Verificar se já existe ajuste para esta escala nesta data
            $ajusteExistente = EscalaDiaria::where('data', $request->data)
                ->where('escala_original_id', $request->escala_original_id)
                ->where('status', 'ativo')
                ->first();

            if ($ajusteExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe um ajuste para esta escala nesta data.'
                ], 422);
            }

            // Verificar se o cartão programa pertence ao posto
            if ($request->cartao_programa_id) {
                $cartaoPrograma = CartaoPrograma::find($request->cartao_programa_id);
                if ($cartaoPrograma && $cartaoPrograma->posto_trabalho_id != $escalaOriginal->posto_trabalho_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'O cartão programa selecionado não pertence ao posto de trabalho.'
                    ], 422);
                }
            }

            // Criar ajuste diário
            $ajuste = EscalaDiaria::create([
                'data' => $request->data,
                'escala_original_id' => $request->escala_original_id,
                'usuario_original_id' => $escalaOriginal->usuario_id,
                'usuario_substituto_id' => $request->usuario_substituto_id,
                'posto_trabalho_id' => $escalaOriginal->posto_trabalho_id,
                'cartao_programa_id' => $request->cartao_programa_id ?: $escalaOriginal->cartao_programa_id,
                'motivo' => $request->motivo,
                'status' => 'ativo',
                'criado_por' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ajuste diário criado com sucesso!',
                'ajuste' => $ajuste->load(['usuarioOriginal', 'usuarioSubstituto', 'postoTrabalho', 'cartaoPrograma'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao criar ajuste diário', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, EscalaDiaria $escalaDiaria)
    {
        $request->validate([
            'usuario_substituto_id' => 'required|exists:usuario,id',
            'cartao_programa_id' => 'nullable|exists:cartao_programas,id',
            'motivo' => 'nullable|string|max:500'
        ]);

        try {
            // Verificar se o cartão programa pertence ao posto
            if ($request->cartao_programa_id) {
                $cartaoPrograma = CartaoPrograma::find($request->cartao_programa_id);
                if ($cartaoPrograma && $cartaoPrograma->posto_trabalho_id != $escalaDiaria->posto_trabalho_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'O cartão programa selecionado não pertence ao posto de trabalho.'
                    ], 422);
                }
            }

            $escalaDiaria->update([
                'usuario_substituto_id' => $request->usuario_substituto_id,
                'cartao_programa_id' => $request->cartao_programa_id,
                'motivo' => $request->motivo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ajuste diário atualizado com sucesso!',
                'ajuste' => $escalaDiaria->load(['usuarioOriginal', 'usuarioSubstituto', 'postoTrabalho', 'cartaoPrograma'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar ajuste diário', [
                'error' => $e->getMessage(),
                'ajuste_id' => $escalaDiaria->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(EscalaDiaria $escalaDiaria)
    {
        try {
            $escalaDiaria->update(['status' => 'cancelado']);

            return response()->json([
                'success' => true,
                'message' => 'Ajuste diário cancelado com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao cancelar ajuste diário', [
                'error' => $e->getMessage(),
                'ajuste_id' => $escalaDiaria->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cartoesPrograma(Request $request)
    {
        $postoId = $request->get('posto_id');

        if (!$postoId) {
            return response()->json([]);
        }

        $cartoes = CartaoPrograma::where('posto_trabalho_id', $postoId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'horario_inicio', 'horario_fim']);

        return response()->json($cartoes);
    }

        public function escalasVigilante($vigilanteId, $ano, $mes)
    {
        try {
            \Log::info('Buscando escalas do vigilante', [
                'vigilante_id' => $vigilanteId,
                'ano' => $ano,
                'mes' => $mes
            ]);

            $dataInicio = Carbon::create($ano, $mes, 1);
            $dataFim = $dataInicio->copy()->endOfMonth();
            
            $escalasVigilante = [];

            // Percorrer todos os dias do mês
            $dataAtual = $dataInicio->copy();
            while ($dataAtual <= $dataFim) {
                $diaSemana = $dataAtual->dayOfWeek;
                // Ajustar para padrão brasileiro (0 = segunda, 6 = domingo)
                $diaSemanaDb = ($diaSemana == 0) ? 6 : $diaSemana - 1;

                // Verificar se o vigilante tem escala semanal neste dia
                $escalaSemanal = Escala::with(['usuario', 'postoTrabalho', 'cartaoPrograma'])
                    ->whereJsonContains('dias_semana', $diaSemanaDb)
                    ->where('usuario_id', $vigilanteId)
                    ->where('ativo', true)
                    ->first();

                if ($escalaSemanal) {
                    \Log::info('Escala semanal encontrada para o vigilante', [
                        'data' => $dataAtual->format('Y-m-d'),
                        'vigilante_id' => $vigilanteId,
                        'posto' => $escalaSemanal->postoTrabalho->nome
                    ]);
                    
                    $escalasVigilante[$dataAtual->format('Y-m-d')] = [
                        'tipo' => 'escala_semanal',
                        'posto' => $escalaSemanal->postoTrabalho->nome,
                        'cartao_programa' => $escalaSemanal->cartaoPrograma ? $escalaSemanal->cartaoPrograma->nome : 'Não definido'
                    ];
                }

                // Verificar se o vigilante foi chamado como substituto neste dia
                $ajusteDiario = EscalaDiaria::with(['usuarioOriginal', 'postoTrabalho', 'cartaoPrograma'])
                    ->where('data', $dataAtual->format('Y-m-d'))
                    ->where('usuario_substituto_id', $vigilanteId)
                    ->where('status', 'ativo')
                    ->first();

                if ($ajusteDiario) {
                    $escalasVigilante[$dataAtual->format('Y-m-d')] = [
                        'tipo' => 'substituicao',
                        'posto' => $ajusteDiario->postoTrabalho->nome,
                        'cartao_programa' => $ajusteDiario->cartaoPrograma ? $ajusteDiario->cartaoPrograma->nome : 'Não definido',
                        'substituindo' => $ajusteDiario->usuarioOriginal->nome
                    ];
                }

                $dataAtual->addDay();
            }

            \Log::info('Escalas do vigilante encontradas', [
                'vigilante_id' => $vigilanteId,
                'ano' => $ano,
                'mes' => $mes,
                'escalas' => $escalasVigilante
            ]);

            return response()->json([
                'success' => true,
                'escalas' => $escalasVigilante
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar escalas do vigilante', [
                'error' => $e->getMessage(),
                'vigilante_id' => $vigilanteId,
                'ano' => $ano,
                'mes' => $mes
            ]);

            return response()->json([
                'success' => false,
                'escalas' => []
            ]);
        }
    }
}
