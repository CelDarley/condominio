<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanicoController extends Controller
{
    public function ativar(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:seguranca,medica,incendio,outro',
            'descricao' => 'nullable|string|max:500',
            'localizacao' => 'nullable|string|max:255'
        ]);
        
        // Capturar coordenadas se disponíveis
        $coordenadas = null;
        if ($request->has('latitude') && $request->has('longitude')) {
            $coordenadas = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ];
        }
        
        $solicitacao = \App\Models\SolicitacaoPanico::create([
            'morador_id' => session('morador_id'),
            'tipo' => $request->tipo,
            'descricao' => $request->descricao,
            'localizacao' => $request->localizacao,
            'coordenadas' => $coordenadas,
            'status' => 'ativo'
        ]);
        
        // Aqui você pode adicionar notificações para os vigilantes
        // Por exemplo, enviar SMS, email ou push notification
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Solicitação de pânico ativada com sucesso!',
                'solicitacao' => $solicitacao
            ]);
        }
        
        return back()->with('success', 'Solicitação de pânico ativada! Os vigilantes foram notificados.');
    }
    
    public function status()
    {
        $morador = \App\Models\Morador::find(session('morador_id'));
        $solicitacoes = $morador->solicitacoesPanico()
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($solicitacoes);
    }
}
