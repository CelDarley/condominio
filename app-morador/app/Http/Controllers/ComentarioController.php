<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'conteudo' => 'required|string|max:1000',
            'tipo' => 'required|in:geral,alerta,sugestao,reclamacao',
            'alerta_id' => 'nullable|exists:alertas,id',
            'publico' => 'boolean'
        ]);
        
        $comentario = \App\Models\ComentarioMorador::create([
            'conteudo' => $request->conteudo,
            'morador_id' => session('morador_id'),
            'alerta_id' => $request->alerta_id,
            'tipo' => $request->tipo,
            'publico' => $request->publico ?? true
        ]);
        
        if ($request->ajax()) {
            return response()->json($comentario->load('morador'));
        }
        
        return back()->with('success', 'Comentário postado com sucesso!');
    }
    
    public function destroy(\App\Models\ComentarioMorador $comentario)
    {
        if ($comentario->morador_id !== session('morador_id')) {
            abort(403, 'Você não pode excluir este comentário.');
        }
        
        $comentario->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Comentário excluído com sucesso!');
    }
}
