<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostoTrabalho;
use App\Models\PontoBase;

class PostoTrabalhoController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $postos = PostoTrabalho::ativos()->get();
        return view('admin.postos.index', compact('postos'));
    }

    public function create()
    {
        return view('admin.postos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string'
        ]);

        PostoTrabalho::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'ativo' => true
        ]);

        return redirect()->route('admin.postos.index')
            ->with('success', 'Posto de trabalho criado com sucesso!');
    }

    public function show(PostoTrabalho $posto)
    {
        $posto->load([
            'pontosBase' => function($query) {
                $query->ativos()->orderBy('ordem');
            },
            'pontosBase.cartaoProgramaPontos',
            'cartoesPrograma' => function($query) {
                $query->ativos()->with('cartaoProgramaPontos')->orderBy('nome');
            }
        ]);
        
        return view('admin.postos.show', compact('posto'));
    }

    public function edit(PostoTrabalho $posto)
    {
        return view('admin.postos.edit', compact('posto'));
    }

    public function update(Request $request, PostoTrabalho $posto)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean'
        ]);

        $posto->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'ativo' => $request->has('ativo')
        ]);

        return redirect()->route('admin.postos.index')
            ->with('success', 'Posto de trabalho atualizado com sucesso!');
    }

    public function destroy(PostoTrabalho $posto)
    {
        // Soft delete - apenas desativa
        $posto->update(['ativo' => false]);

        return redirect()->route('admin.postos.index')
            ->with('success', 'Posto de trabalho desativado com sucesso!');
    }

    // Gerenciamento de Pontos Base
    public function pontosBase(PostoTrabalho $posto)
    {
        $pontos = $posto->pontosBase()->ativos()->orderBy('ordem')->get();
        return view('admin.postos.pontos-base', compact('posto', 'pontos'));
    }

    public function createPontoBase(PostoTrabalho $posto)
    {
        return view('admin.postos.create-ponto-base', compact('posto'));
    }

    public function storePontoBase(Request $request, PostoTrabalho $posto)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'endereco' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'instrucoes' => 'nullable|string',
            'ordem' => 'nullable|integer|min:1',
            'horario_inicio' => 'nullable|string',
            'horario_fim' => 'nullable|string',
            'tempo_permanencia' => 'nullable|integer|min:1|max:120',
            'tempo_deslocamento' => 'nullable|integer|min:1|max:60'
        ]);

        // Se não informou ordem, pega a próxima
        $ordem = $request->ordem ?? ($posto->pontosBase()->max('ordem') + 1);

        PontoBase::create([
            'posto_id' => $posto->id,
            'nome' => $request->nome,
            'endereco' => $request->endereco,
            'descricao' => $request->descricao,
            'instrucoes' => $request->instrucoes,
            'ordem' => $ordem,
            'horario_inicio' => $request->horario_inicio ?? '08:00',
            'horario_fim' => $request->horario_fim ?? '18:00',
            'tempo_permanencia' => $request->tempo_permanencia ?? 10,
            'tempo_deslocamento' => $request->tempo_deslocamento ?? 5,
            'ativo' => $request->has('ativo')
        ]);

        return redirect()->route('admin.postos.pontos-base', $posto)
            ->with('success', 'Ponto base criado com sucesso!');
    }
}
