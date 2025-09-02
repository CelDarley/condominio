<?php

namespace App\Http\Controllers;

use App\Models\Ocorrencia;
use App\Models\PostoTrabalho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OcorrenciaController extends Controller
{
    // Listar ocorrências do usuário logado
    public function index()
    {
        $user = Auth::user();
        
        $ocorrencias = Ocorrencia::where('usuario_id', $user->id)
            ->with(['postoTrabalho'])
            ->orderBy('data_ocorrencia', 'desc')
            ->paginate(10);

        return view('ocorrencias.index', compact('ocorrencias'));
    }

    // Mostrar formulário de criação
    public function create()
    {
        $postos = PostoTrabalho::where('ativo', true)->get();
        
        return view('ocorrencias.create', compact('postos'));
    }

    // Salvar nova ocorrência
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo' => 'required|in:incidente,manutencao,seguranca,outros',
            'prioridade' => 'required|in:baixa,media,alta,urgente',
            'posto_trabalho_id' => 'nullable|exists:posto_trabalho,id',
            'anexos.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx', // 10MB max
        ]);

        $user = Auth::user();
        $anexos = [];

        // Upload de arquivos
        if ($request->hasFile('anexos')) {
            foreach ($request->file('anexos') as $arquivo) {
                $nome = time() . '_' . $arquivo->getClientOriginalName();
                $caminho = $arquivo->storeAs('ocorrencias', $nome, 'public');
                $anexos[] = [
                    'nome' => $arquivo->getClientOriginalName(),
                    'caminho' => $caminho,
                    'tipo' => $arquivo->getClientMimeType(),
                    'tamanho' => $arquivo->getSize()
                ];
            }
        }

        Ocorrencia::create([
            'usuario_id' => $user->id,
            'posto_trabalho_id' => $request->posto_trabalho_id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'tipo' => $request->tipo,
            'prioridade' => $request->prioridade,
            'status' => 'aberta',
            'anexos' => $anexos,
            'data_ocorrencia' => now(),
        ]);

        return redirect()->route('ocorrencias.index')
            ->with('success', 'Ocorrência registrada com sucesso!');
    }

    // Mostrar detalhes de uma ocorrência
    public function show(Ocorrencia $ocorrencia)
    {
        // Verificar se a ocorrência pertence ao usuário logado
        if ($ocorrencia->usuario_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        return view('ocorrencias.show', compact('ocorrencia'));
    }

    // Mostrar formulário de edição
    public function edit(Ocorrencia $ocorrencia)
    {
        // Verificar se a ocorrência pertence ao usuário logado
        if ($ocorrencia->usuario_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $postos = PostoTrabalho::where('ativo', true)->get();
        
        return view('ocorrencias.edit', compact('ocorrencia', 'postos'));
    }

    // Atualizar ocorrência
    public function update(Request $request, Ocorrencia $ocorrencia)
    {
        // Verificar se a ocorrência pertence ao usuário logado
        if ($ocorrencia->usuario_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo' => 'required|in:incidente,manutencao,seguranca,outros',
            'prioridade' => 'required|in:baixa,media,alta,urgente',
            'status' => 'required|in:aberta,em_andamento,resolvida,fechada',
            'posto_trabalho_id' => 'nullable|exists:posto_trabalho,id',
            'observacoes' => 'nullable|string',
            'anexos.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $anexos = $ocorrencia->anexos ?? [];

        // Upload de novos arquivos
        if ($request->hasFile('anexos')) {
            foreach ($request->file('anexos') as $arquivo) {
                $nome = time() . '_' . $arquivo->getClientOriginalName();
                $caminho = $arquivo->storeAs('ocorrencias', $nome, 'public');
                $anexos[] = [
                    'nome' => $arquivo->getClientOriginalName(),
                    'caminho' => $caminho,
                    'tipo' => $arquivo->getClientMimeType(),
                    'tamanho' => $arquivo->getSize()
                ];
            }
        }

        $ocorrencia->update([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'tipo' => $request->tipo,
            'prioridade' => $request->prioridade,
            'status' => $request->status,
            'posto_trabalho_id' => $request->posto_trabalho_id,
            'observacoes' => $request->observacoes,
            'anexos' => $anexos,
        ]);

        return redirect()->route('ocorrencias.show', $ocorrencia)
            ->with('success', 'Ocorrência atualizada com sucesso!');
    }

    // Remover anexo específico
    public function removeAnexo(Request $request, Ocorrencia $ocorrencia)
    {
        // Verificar se a ocorrência pertence ao usuário logado
        if ($ocorrencia->usuario_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $indice = $request->input('indice');
        $anexos = $ocorrencia->anexos ?? [];

        if (isset($anexos[$indice])) {
            // Remover arquivo do storage
            Storage::disk('public')->delete($anexos[$indice]['caminho']);
            
            // Remover do array
            unset($anexos[$indice]);
            $anexos = array_values($anexos); // Reindexar array
            
            $ocorrencia->update(['anexos' => $anexos]);
        }

        return response()->json(['success' => true]);
    }

    // Download de anexo
    public function downloadAnexo(Ocorrencia $ocorrencia, $indice)
    {
        // Verificar se a ocorrência pertence ao usuário logado
        if ($ocorrencia->usuario_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $anexos = $ocorrencia->anexos ?? [];
        
        if (!isset($anexos[$indice])) {
            abort(404, 'Anexo não encontrado.');
        }

        $anexo = $anexos[$indice];
        $caminhoCompleto = storage_path('app/public/' . $anexo['caminho']);

        if (!file_exists($caminhoCompleto)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return response()->download($caminhoCompleto, $anexo['nome']);
    }
}
