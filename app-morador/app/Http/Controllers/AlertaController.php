<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        $alertas = \App\Models\Alerta::where('status', 'ativo')
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->with(['usuario', 'comentarios.morador'])
            ->paginate(10);
            
        return view('alertas.index', compact('alertas'));
    }
    
    public function show(\App\Models\Alerta $alerta)
    {
        $alerta->load(['usuario', 'comentarios.morador']);
        return view('alertas.show', compact('alerta'));
    }
    
    public function getAlertasAtivos()
    {
        $alertas = \App\Models\Alerta::where('status', 'ativo')
            ->orderBy('prioridade', 'desc')
            ->orderBy('created_at', 'desc')
            ->with(['usuario'])
            ->get();
            
        return response()->json($alertas);
    }
}
