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
        $postos = PostoTrabalho::with(['pontosBase', 'cartoesPrograma'])
            ->where('ativo', true)
            ->get();
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
                $query->ativos()->orderBy('nome');
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
        try {
            // Log para debug
            \Log::info('Tentando desativar posto de trabalho', [
                'posto_id' => $posto->id,
                'posto_nome' => $posto->nome,
                'user_id' => auth()->id(),
                'pontos_base_count' => $posto->pontosBase->count(),
                'cartoes_programa_count' => $posto->cartoesPrograma->count()
            ]);

            // Verificar se o posto tem pontos base ativos
            $pontosAtivos = $posto->pontosBase()->where('ativo', true)->count();
            if ($pontosAtivos > 0) {
                \Log::warning('Tentativa de desativar posto com pontos base ativos', [
                    'posto_id' => $posto->id,
                    'pontos_ativos' => $pontosAtivos
                ]);
                
                return redirect()->route('admin.postos.index')
                    ->with('error', "Não é possível desativar este posto pois ele possui {$pontosAtivos} ponto(s) base ativo(s). Desative os pontos base primeiro.");
            }

            // Verificar se o posto tem cartões programa ativos
            $cartoesAtivos = $posto->cartoesPrograma()->where('ativo', true)->count();
            if ($cartoesAtivos > 0) {
                \Log::warning('Tentativa de desativar posto com cartões programa ativos', [
                    'posto_id' => $posto->id,
                    'cartoes_ativos' => $cartoesAtivos
                ]);
                
                return redirect()->route('admin.postos.index')
                    ->with('error', "Não é possível desativar este posto pois ele possui {$cartoesAtivos} cartão(ões) programa ativo(s). Desative os cartões primeiro.");
            }

            // Verificar se o posto tem escalas ativas
            $escalasAtivas = $posto->escalas()->where('ativo', true)->count();
            if ($escalasAtivas > 0) {
                \Log::warning('Tentativa de desativar posto com escalas ativas', [
                    'posto_id' => $posto->id,
                    'escalas_ativas' => $escalasAtivas
                ]);
                
                return redirect()->route('admin.postos.index')
                    ->with('error', "Não é possível desativar este posto pois ele possui {$escalasAtivas} escala(s) ativa(s). Desative as escalas primeiro.");
            }

            // Soft delete - apenas desativa
            $resultado = $posto->update(['ativo' => false]);

            if (!$resultado) {
                throw new \Exception('Falha ao atualizar registro no banco de dados');
            }

            \Log::info('Posto de trabalho desativado com sucesso', [
                'posto_id' => $posto->id,
                'posto_nome' => $posto->nome
            ]);

            return redirect()->route('admin.postos.index')
                ->with('success', 'Posto de trabalho desativado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao desativar posto de trabalho', [
                'error' => $e->getMessage(),
                'posto_id' => $posto->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.postos.index')
                ->with('error', 'Erro ao desativar posto de trabalho: ' . $e->getMessage());
        }
    }

    // Gerenciamento de Pontos Base
    public function pontosBase(PostoTrabalho $posto)
    {
        $pontos = $posto->pontosBase()->ativos()->orderBy('nome')->get();
        return view('admin.postos.pontos-base', compact('posto', 'pontos'));
    }

    public function createPontoBase(PostoTrabalho $posto)
    {
        return view('admin.postos.create-ponto-base', compact('posto'));
    }

    public function storePontoBase(Request $request, PostoTrabalho $posto)
    {
        // Log para debug
        \Log::info('Tentando criar ponto base', [
            'request_data' => $request->all(),
            'posto_id' => $posto->id,
            'user_id' => auth()->id(),
            'session_id' => session()->getId()
        ]);

        try {
            $request->validate([
                'nome' => 'required|string|max:100',
                'endereco' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'latitude' => 'nullable|string',
                'longitude' => 'nullable|string'
            ]);

            $pontoData = [
                'posto_id' => $posto->id,
                'nome' => $request->nome,
                'endereco' => $request->endereco,
                'descricao' => $request->descricao ?: null,
                'latitude' => $request->latitude ?: null,
                'longitude' => $request->longitude ?: null,
                'ativo' => $request->has('ativo') || $request->input('ativo') === 'on' || $request->boolean('ativo')
            ];

            \Log::info('Dados do ponto base para criação', $pontoData);

            $ponto = PontoBase::create($pontoData);

            \Log::info('Ponto base criado com sucesso', ['ponto_id' => $ponto->id]);

            // Log adicional para debug
            \Log::info('Redirecionando após criação', [
                'route' => 'admin.postos.pontos-base',
                'posto_id' => $posto->id
            ]);

            return redirect()->route('admin.postos.pontos-base', $posto)
                ->with('success', 'Ponto base criado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar ponto base', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors(['error' => 'Erro ao criar ponto base: ' . $e->getMessage()])->withInput();
        }
    }

    // Edição de Ponto Base
    public function editPontoBase(PostoTrabalho $posto, PontoBase $ponto)
    {
        // Verificar se o ponto pertence ao posto
        if ($ponto->posto_id !== $posto->id) {
            abort(404, 'Ponto base não encontrado neste posto.');
        }

        return view('admin.postos.edit-ponto-base', compact('posto', 'ponto'));
    }

    // Atualização de Ponto Base
    public function updatePontoBase(Request $request, PostoTrabalho $posto, PontoBase $ponto)
    {
        // Verificar se o ponto pertence ao posto
        if ($ponto->posto_id !== $posto->id) {
            abort(404, 'Ponto base não encontrado neste posto.');
        }

        try {
            $request->validate([
                'nome' => 'required|string|max:100',
                'endereco' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'latitude' => 'nullable|string',
                'longitude' => 'nullable|string'
            ]);

            $pontoData = [
                'nome' => $request->nome,
                'endereco' => $request->endereco,
                'descricao' => $request->descricao ?: null,
                'latitude' => $request->latitude ?: null,
                'longitude' => $request->longitude ?: null,
                'ativo' => $request->has('ativo') || $request->input('ativo') === 'on' || $request->boolean('ativo')
            ];

            $ponto->update($pontoData);

            return redirect()->route('admin.postos.pontos-base', $posto)
                ->with('success', 'Ponto base atualizado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar ponto base', [
                'error' => $e->getMessage(),
                'ponto_id' => $ponto->id,
                'posto_id' => $posto->id
            ]);

            return back()->withErrors(['error' => 'Erro ao atualizar ponto base: ' . $e->getMessage()])->withInput();
        }
    }

    // Exclusão de Ponto Base
    public function destroyPontoBase(PostoTrabalho $posto, PontoBase $ponto)
    {
        // Verificar se o ponto pertence ao posto
        if ($ponto->posto_id !== $posto->id) {
            abort(404, 'Ponto base não encontrado neste posto.');
        }

        try {
            // Verificar se o ponto está sendo usado em algum cartão programa
            if ($ponto->isUsadoEmCartaoPrograma()) {
                return redirect()->route('admin.postos.pontos-base', $posto)
                    ->with('error', 'Não é possível excluir este ponto base pois ele está sendo usado em um ou mais cartões programa.');
            }

            // Soft delete - apenas desativa
            $ponto->update(['ativo' => false]);

            return redirect()->route('admin.postos.pontos-base', $posto)
                ->with('success', 'Ponto base desativado com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao excluir ponto base', [
                'error' => $e->getMessage(),
                'ponto_id' => $ponto->id,
                'posto_id' => $posto->id
            ]);

            return redirect()->route('admin.postos.pontos-base', $posto)
                ->with('error', 'Erro ao excluir ponto base: ' . $e->getMessage());
        }
    }
}
