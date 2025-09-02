<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisoController extends Controller
{
    // Middleware aplicado diretamente nas rotas

    public function index()
    {
        $user = Auth::user();
        
        $avisos = Aviso::where('usuario_id', $user->id)
            ->ativos()
            ->recentes()
            ->paginate(15);

        return view('aviso.index', compact('avisos'));
    }

    public function create()
    {
        return view('aviso.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'mensagem' => 'required|string|max:1000'
        ], [
            'titulo.required' => 'O título é obrigatório.',
            'titulo.max' => 'O título não pode ter mais de 200 caracteres.',
            'mensagem.required' => 'A mensagem é obrigatória.',
            'mensagem.max' => 'A mensagem não pode ter mais de 1000 caracteres.'
        ]);

        try {
            $aviso = Aviso::create([
                'usuario_id' => Auth::id(),
                'titulo' => $request->input('titulo'),
                'mensagem' => $request->input('mensagem'),
                'timestamp' => now(),
                'ativo' => true,
                'data_criacao' => now()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Aviso enviado com sucesso!',
                    'aviso_id' => $aviso->id
                ]);
            }

            return redirect()->route('avisos.index')
                ->with('success', 'Aviso enviado com sucesso!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erro ao enviar aviso: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'Erro ao enviar aviso: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $aviso = Aviso::where('id', $id)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        return view('aviso.show', compact('aviso'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        
        $aviso = Aviso::where('id', $id)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        return view('aviso.edit', compact('aviso'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'mensagem' => 'required|string|max:1000'
        ], [
            'titulo.required' => 'O título é obrigatório.',
            'titulo.max' => 'O título não pode ter mais de 200 caracteres.',
            'mensagem.required' => 'A mensagem é obrigatória.',
            'mensagem.max' => 'A mensagem não pode ter mais de 1000 caracteres.'
        ]);

        $user = Auth::user();
        
        $aviso = Aviso::where('id', $id)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        try {
            $aviso->update([
                'titulo' => $request->input('titulo'),
                'mensagem' => $request->input('mensagem')
            ]);

            return redirect()->route('avisos.show', $aviso->id)
                ->with('success', 'Aviso atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar aviso: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        
        $aviso = Aviso::where('id', $id)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        try {
            $aviso->update(['ativo' => false]);

            return response()->json([
                'status' => 'success',
                'message' => 'Aviso removido com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao remover aviso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function enviarRapido(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'mensagem' => 'required|string|max:1000'
        ]);

        try {
            $aviso = Aviso::create([
                'usuario_id' => Auth::id(),
                'titulo' => $request->input('titulo'),
                'mensagem' => $request->input('mensagem'),
                'timestamp' => now(),
                'ativo' => true,
                'data_criacao' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Aviso enviado com sucesso!',
                'aviso' => [
                    'id' => $aviso->id,
                    'titulo' => $aviso->titulo,
                    'mensagem' => $aviso->mensagem,
                    'timestamp' => $aviso->getTimestampFormatado(),
                    'prioridade' => $aviso->getPrioridade()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao enviar aviso: ' . $e->getMessage()
            ], 500);
        }
    }

    public function panico(Request $request)
    {
        try {
            $user = Auth::user();
            $localizacao = $request->input('localizacao', 'Localização não informada');
            
            $aviso = Aviso::create([
                'usuario_id' => $user->id,
                'titulo' => '🚨 ALERTA DE PÂNICO',
                'mensagem' => "SITUAÇÃO DE EMERGÊNCIA!\n\nVigilante: {$user->nome}\nLocalização: {$localizacao}\nHorário: " . now()->format('d/m/Y H:i:s') . "\n\nAção imediata necessária!",
                'timestamp' => now(),
                'ativo' => true,
                'data_criacao' => now()
            ]);

            // Aqui você pode adicionar lógica para notificações push, emails, etc.
            
            return response()->json([
                'status' => 'success',
                'message' => 'Alerta de pânico enviado! Ajuda está a caminho.',
                'aviso_id' => $aviso->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao enviar alerta de pânico: ' . $e->getMessage()
            ], 500);
        }
    }
} 