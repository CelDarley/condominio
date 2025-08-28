<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escala;
use App\Models\Usuario;
use App\Models\PostoTrabalho;

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
            ->orderBy('dia_semana')
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
        $request->validate([
            'usuario_id' => 'required|exists:usuario,id',
            'posto_trabalho_id' => 'required|exists:posto_trabalho,id', // Corrigido nome do campo
            'cartao_programa_id' => 'nullable|exists:cartao_programas,id', // Novo campo
            'dia_semana' => 'required|integer|between:0,6'
        ]);

        // Verificar se o cartão programa pertence ao posto selecionado
        if ($request->cartao_programa_id) {
            $cartaoPrograma = \App\Models\CartaoPrograma::find($request->cartao_programa_id);
            if ($cartaoPrograma && $cartaoPrograma->posto_trabalho_id != $request->posto_trabalho_id) {
                return back()->withErrors(['cartao_programa_id' => 'O cartão programa selecionado não pertence ao posto de trabalho escolhido.']);
            }
        }

        // Verificar se já existe escala para este usuário neste dia
        $escalaExistente = Escala::where('usuario_id', $request->usuario_id)
            ->where('dia_semana', $request->dia_semana)
            ->where('ativo', true)
            ->first();

        if ($escalaExistente) {
            return back()->withErrors(['dia_semana' => 'Já existe uma escala ativa para este usuário neste dia da semana.']);
        }

        Escala::create([
            'usuario_id' => $request->usuario_id,
            'posto_trabalho_id' => $request->posto_trabalho_id, // Corrigido nome do campo
            'cartao_programa_id' => $request->cartao_programa_id, // Novo campo
            'dia_semana' => $request->dia_semana,
            'ativo' => true
        ]);

        return redirect()->route('admin.escalas.index')
            ->with('success', 'Escala criada com sucesso!');
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
            'posto_id' => 'required|exists:posto_trabalho,id',
            'dia_semana' => 'required|integer|between:0,6',
            'ativo' => 'boolean'
        ]);

        // Verificar conflitos (exceto a própria escala)
        $escalaExistente = Escala::where('usuario_id', $request->usuario_id)
            ->where('dia_semana', $request->dia_semana)
            ->where('ativo', true)
            ->where('id', '!=', $escala->id)
            ->first();

        if ($escalaExistente) {
            return back()->withErrors(['dia_semana' => 'Já existe uma escala ativa para este usuário neste dia da semana.']);
        }

        $escala->update([
            'usuario_id' => $request->usuario_id,
            'posto_id' => $request->posto_id,
            'dia_semana' => $request->dia_semana,
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
            ->where('dia_semana', $diaSemana)
            ->where('ativo', true)
            ->get();

        return response()->json($escalas);
    }

    // Relatório de escalas por período
    public function relatorio()
    {
        $escalas = Escala::with(['usuario', 'postoTrabalho', 'cartaoPrograma'])
            ->ativos()
            ->get()
            ->groupBy('dia_semana');

        $diasSemana = [
            0 => 'Segunda-feira',
            1 => 'Terça-feira',
            2 => 'Quarta-feira',
            3 => 'Quinta-feira', 
            4 => 'Sexta-feira',
            5 => 'Sábado',
            6 => 'Domingo'
        ];

        return view('admin.escalas.relatorio', compact('escalas', 'diasSemana'));
    }
}
