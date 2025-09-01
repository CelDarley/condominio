<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escala;
use App\Models\Usuario;
use App\Models\PostoTrabalho;
use Carbon\Carbon;

class EscalaController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $escalas = Escala::with(['usuario', 'postoTrabalho', 'cartaoPrograma'])
            ->ativos()
            ->orderBy('data_inicio')
            ->get();

        return view('admin.escalas.index', compact('escalas'));
    }

    public function create()
    {
        $usuarios = Usuario::where('tipo', 'vigilante')
            ->where('ativo', true)
            ->get();

        $postos = PostoTrabalho::ativos()->get();

        $diasSemana = [
            0 => 'Segunda-feira',
            1 => 'Terça-feira',
            2 => 'Quarta-feira',
            3 => 'Quinta-feira',
            4 => 'Sexta-feira',
            5 => 'Sábado',
            6 => 'Domingo'
        ];

        return view('admin.escalas.create', compact('usuarios', 'postos', 'diasSemana'));
    }

    public function store(Request $request)
    {
        // Log para debug
        \Log::info('EscalaController::store chamado', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId()
        ]);

        // Validação básica
        $request->validate([
            'usuario_id' => 'required|exists:usuario,id',
            'posto_trabalho_id' => 'required|exists:posto_trabalho,id',
            'dias' => 'required|array',
        ]);

        // Validação customizada: pelo menos um dia deve estar selecionado
        $diasSelecionados = collect($request->dias ?? [])
            ->filter(function ($dia) {
                return isset($dia['ativo']) && $dia['ativo'];
            });

        if ($diasSelecionados->isEmpty()) {
            return back()->withErrors([
                'dias' => 'Selecione pelo menos um dia da semana.'
            ])->withInput();
        }

        // Validar cartões programa apenas para dias selecionados
        foreach ($request->dias as $diaSemana => $dados) {
            if (isset($dados['ativo']) && $dados['ativo'] && isset($dados['cartao_programa_id'])) {
                $request->validate([
                    "dias.{$diaSemana}.cartao_programa_id" => 'exists:cartao_programas,id'
                ]);
            }
        }

        \Log::info('Validação passou com sucesso');

        $usuario_id = $request->usuario_id;
        $posto_trabalho_id = $request->posto_trabalho_id;
        $dias = $request->dias;

        \Log::info('Dados processados', [
            'usuario_id' => $usuario_id,
            'posto_trabalho_id' => $posto_trabalho_id,
            'dias_count' => count($dias)
        ]);

        $escalasCreated = 0;
        $errors = [];

        \DB::beginTransaction();
        try {
            foreach ($dias as $dia_semana => $dadosDia) {
                if (!isset($dadosDia['ativo']) || !$dadosDia['ativo']) {
                    continue; // Pular dias não selecionados
                }

                // Verificar se o cartão programa pertence ao posto selecionado
                if (!empty($dadosDia['cartao_programa_id'])) {
                    $cartaoPrograma = \App\Models\CartaoPrograma::find($dadosDia['cartao_programa_id']);
                    if ($cartaoPrograma && $cartaoPrograma->posto_trabalho_id != $posto_trabalho_id) {
                        $errors[] = "O cartão programa selecionado para {$this->getNomeDiaSemana($dia_semana)} não pertence ao posto escolhido.";
                        continue;
                    }
                }

                // Verificar se já existe escala para este usuário neste dia
                $escalaExistente = Escala::where('usuario_id', $usuario_id)
                    ->whereJsonContains('dias_semana', $dia_semana)
                    ->where('ativo', true)
                    ->first();

                if ($escalaExistente) {
                    $errors[] = "Já existe uma escala ativa para este usuário em {$this->getNomeDiaSemana($dia_semana)}.";
                    continue;
                }

                // Criar escala
                Escala::create([
                    'nome' => "Escala {$this->getNomeDiaSemana($dia_semana)}",
                    'usuario_id' => $usuario_id,
                    'posto_trabalho_id' => $posto_trabalho_id,
                    'cartao_programa_id' => $dadosDia['cartao_programa_id'] ?: null,
                    'data_inicio' => now()->format('Y-m-d'),
                    'horario_inicio' => '08:00',
                    'horario_fim' => '18:00',
                    'dias_semana' => [$dia_semana],
                    'ativo' => true
                ]);

                $escalasCreated++;
            }

            if ($escalasCreated === 0) {
                \DB::rollback();
                if (!empty($errors)) {
                    return back()->withErrors(['dias' => implode(' ', $errors)])->withInput();
                } else {
                    return back()->withErrors(['dias' => 'Nenhuma escala foi criada. Selecione pelo menos um dia válido.'])->withInput();
                }
            }

            \DB::commit();

            $message = "Criada(s) {$escalasCreated} escala(s) com sucesso!";
            if (!empty($errors)) {
                $message .= " Avisos: " . implode(' ', $errors);
            }

            \Log::info('Redirecionando com sucesso', [
                'message' => $message,
                'escalas_created' => $escalasCreated
            ]);

            return redirect()->route('admin.escalas.index')->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Erro ao criar escalas', [
                'error' => $e->getMessage(),
                'usuario_id' => $usuario_id,
                'posto_trabalho_id' => $posto_trabalho_id,
                'dias' => $dias
            ]);

            return back()->withErrors(['error' => 'Erro ao criar escalas: ' . $e->getMessage()])->withInput();
        }
    }

    private function getNomeDiaSemana($numero)
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

        return $dias[$numero] ?? "Dia {$numero}";
    }

    public function show(Escala $escala)
    {
        $escala->load(['usuario', 'postoTrabalho', 'cartaoPrograma']); // Corrigido relacionamentos
        return view('admin.escalas.show', compact('escala'));
    }

    public function edit(Escala $escala)
    {
        $usuarios = Usuario::where('tipo', 'vigilante')
            ->where('ativo', true)
            ->get();

        $postos = PostoTrabalho::ativos()->get();

        $diasSemana = [
            0 => 'Segunda-feira',
            1 => 'Terça-feira',
            2 => 'Quarta-feira',
            3 => 'Quinta-feira',
            4 => 'Sexta-feira',
            5 => 'Sábado',
            6 => 'Domingo'
        ];

        return view('admin.escalas.edit', compact('escala', 'usuarios', 'postos', 'diasSemana'));
    }

    public function update(Request $request, Escala $escala)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuario,id',
            'posto_trabalho_id' => 'required|exists:posto_trabalho,id',
            'cartao_programa_id' => 'nullable|exists:cartao_programa,id',
            'dias_semana' => 'required|array',
            'dias_semana.*' => 'integer|between:0,6',
            'ativo' => 'boolean'
        ]);

        // Verificar conflitos (exceto a própria escala)
        $escalaExistente = Escala::where('usuario_id', $request->usuario_id)
            ->where('ativo', true)
            ->where('id', '!=', $escala->id)
            ->where(function($query) use ($request) {
                foreach ($request->dias_semana as $dia) {
                    $query->orWhereJsonContains('dias_semana', $dia);
                }
            })
            ->first();

        if ($escalaExistente) {
            return back()->withErrors(['dias_semana' => 'Já existe uma escala ativa para este usuário em um dos dias selecionados.']);
        }

        $escala->update([
            'usuario_id' => $request->usuario_id,
            'posto_trabalho_id' => $request->posto_trabalho_id,
            'cartao_programa_id' => $request->cartao_programa_id ?: null,
            'dias_semana' => $request->dias_semana,
            'ativo' => $request->has('ativo')
        ]);

        return redirect()->route('admin.escalas.index')
            ->with('success', 'Escala atualizada com sucesso!');
    }

    public function destroy(Escala $escala)
    {
        // Soft delete - apenas desativa
        $escala->update(['ativo' => false]);

        return redirect()->route('admin.escalas.index')
            ->with('success', 'Escala desativada com sucesso!');
    }

    // API para consultar escalas
    public function getEscalasByUsuario($usuarioId, $diaSemana)
    {
        $escalas = Escala::with(['postoTrabalho', 'cartaoPrograma'])
            ->where('usuario_id', $usuarioId)
            ->whereJsonContains('dias_semana', $diaSemana)
            ->where('ativo', true)
            ->get();

        return response()->json($escalas);
    }

    // Relatório de escalas por período
    public function relatorio(Request $request)
    {
        $query = Escala::with(['usuario', 'postoTrabalho', 'cartaoPrograma'])
            ->ativos();

        // Filtro por vigilante
        if ($request->filled('vigilante_id')) {
            $query->where('usuario_id', $request->vigilante_id);
        }

        $escalas = $query->get()
            ->groupBy(function($escala) {
                // Agrupar por todos os dias da semana da escala
                return $escala->dias_semana;
            });

        // Calcular total de horas trabalhadas
        $totalHoras = 0;
        if ($request->filled('vigilante_id')) {
            $escalasFiltradas = Escala::with(['usuario', 'postoTrabalho'])
                ->where('usuario_id', $request->vigilante_id)
                ->ativos()
                ->get();

            foreach ($escalasFiltradas as $escala) {
                // Os horários já são objetos Carbon
                $horarioInicio = $escala->horario_inicio;
                $horarioFim = $escala->horario_fim;
                
                // Se o horário de fim é menor que o de início, significa que passa da meia-noite
                if ($horarioFim < $horarioInicio) {
                    $horarioFim = $horarioFim->copy()->addDay();
                }
                
                $diferenca = abs($horarioFim->diffInMinutes($horarioInicio));
                
                // Multiplicar pelos dias da semana
                $totalHoras += ($diferenca * count($escala->dias_semana));
            }
        }

        $diasSemana = [
            0 => 'Segunda-feira',
            1 => 'Terça-feira',
            2 => 'Quarta-feira',
            3 => 'Quinta-feira',
            4 => 'Sexta-feira',
            5 => 'Sábado',
            6 => 'Domingo'
        ];

        $usuarios = Usuario::where('tipo', 'vigilante')
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('admin.escalas.relatorio', compact('escalas', 'diasSemana', 'usuarios', 'totalHoras'));
    }
}
