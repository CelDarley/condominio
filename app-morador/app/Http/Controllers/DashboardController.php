<?php

namespace App\Http\Controllers;

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
        // Pegar o morador autenticado através do guard 'morador'
        $morador = auth('morador')->user();
        
        // Se não houver morador autenticado, redirecionar para login
        if (!$morador) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta página.');
        }
        
        $alertasAtivos = \App\Models\Alerta::where('status', 'ativo')
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Para o app-morador, não precisamos mostrar vigilantes online
        $vigilantesOnline = collect(); // Array vazio para compatibilidade
            
        $minhasSolicitacoes = \App\Models\SolicitacaoPanico::where('morador_id', $morador->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('dashboard', compact('morador', 'alertasAtivos', 'vigilantesOnline', 'minhasSolicitacoes'));
    }
    
    public function getVigilantesPosicao()
    {
        // Para o app-morador, retornar array vazio já que não temos acesso aos dados dos vigilantes
        return response()->json([]);
    }
}
