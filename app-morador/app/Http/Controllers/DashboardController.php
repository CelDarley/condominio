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
            
        $vigilantesOnline = \App\Models\Usuario::where('online', true)
            ->where('tipo', 'vigilante')
            ->get();
            
        return view('home', compact('alertasAtivos', 'vigilantesOnline'));
    }
    
    public function dashboard()
    {
        $morador = \App\Models\Morador::find(session('morador_id'));
        
        $alertasAtivos = \App\Models\Alerta::where('status', 'ativo')
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $vigilantesOnline = \App\Models\Usuario::where('online', true)
            ->where('tipo', 'vigilante')
            ->get();
            
        $minhasSolicitacoes = \App\Models\SolicitacaoPanico::where('morador_id', $morador->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('dashboard', compact('morador', 'alertasAtivos', 'vigilantesOnline', 'minhasSolicitacoes'));
    }
    
    public function getVigilantesPosicao()
    {
        $vigilantes = \App\Models\Usuario::where('online', true)
            ->where('tipo', 'vigilante')
            ->whereNotNull('coordenadas_atual')
            ->get(['id', 'nome', 'coordenadas_atual', 'ultima_atualizacao_localizacao']);
            
        return response()->json($vigilantes);
    }
}
