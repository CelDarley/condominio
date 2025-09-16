<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $alertasAtivos = \App\Models\Alerta::where('status', 'ativo')
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Para o app-morador, não precisamos mostrar vigilantes online
        // pois essa informação é do sistema administrativo
        $vigilantesOnline = collect(); // Array vazio para compatibilidade

        return view('home', compact('alertasAtivos', 'vigilantesOnline'));
    }

    public function dashboard()
    {
        // Pegar o usuário autenticado através do guard 'morador'
        $usuario = auth('morador')->user();

        // Se não houver usuário autenticado, redirecionar para login
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta página.');
        }

        // Carregar dados do morador via relacionamento
        $morador = $usuario->morador ?? $usuario; // Fallback para o próprio usuário se não houver morador

        $alertasAtivos = \App\Models\Alerta::where('status', 'ativo')
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Para o app-morador, não precisamos mostrar vigilantes online
        $vigilantesOnline = collect(); // Array vazio para compatibilidade

        // Tentar buscar solicitações de pânico, mas não quebrar se a tabela não existir
        try {
            $minhasSolicitacoes = \App\Models\SolicitacaoPanico::where('morador_id', $usuario->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            // Se a tabela não existir, usar array vazio
            $minhasSolicitacoes = collect();
        }

        return view('dashboard', compact('morador', 'alertasAtivos', 'vigilantesOnline', 'minhasSolicitacoes'));
    }

    public function getVigilantesPosicao()
    {
        // Para o app-morador, retornar array vazio já que não temos acesso aos dados dos vigilantes
        $vigilantes = Usuario::where('tipo', 'vigilante')->get();

        return response()->json($vigilantes);
    }
}
