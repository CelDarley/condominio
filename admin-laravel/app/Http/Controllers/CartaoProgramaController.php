<?php

namespace App\Http\Controllers;

use App\Models\CartaoPrograma;
use App\Models\PostoTrabalho;
use App\Models\PontoBase;
use Illuminate\Http\Request;

class CartaoProgramaController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cartoes = CartaoPrograma::with(['postoTrabalho', 'cartaoProgramaPontos.pontoBase'])
                                ->orderBy('nome')
                                ->paginate(10);

        return view('admin.cartoes-programa.index', compact('cartoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $postos = PostoTrabalho::ativos()->orderBy('nome')->get();
        
        return view('admin.cartoes-programa.create', compact('postos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'posto_trabalho_id' => 'required|exists:posto_trabalho,id',
            'horario_inicio' => 'required|string',
            'horario_fim' => 'required|string',
        ]);

        $cartaoPrograma = CartaoPrograma::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'posto_trabalho_id' => $request->posto_trabalho_id,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim' => $request->horario_fim,
            'ativo' => $request->has('ativo')
        ]);

        return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
            ->with('success', 'Cartão Programa criado com sucesso! Agora adicione pontos base à sequência.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CartaoPrograma $cartaoPrograma)
    {
        $cartaoPrograma->load([
            'postoTrabalho',
            'cartaoProgramaPontos.pontoBase'
        ]);

        // Buscar pontos do posto que ainda não foram adicionados ao cartão
        $pontosJaAdicionados = $cartaoPrograma->cartaoProgramaPontos->pluck('ponto_base_id')->toArray();
        
        $pontosDisponiveis = PontoBase::porPosto($cartaoPrograma->posto_trabalho_id)
                                    ->ativos()
                                    ->whereNotIn('id', $pontosJaAdicionados)
                                    ->get();

        // Calcular tempos totais
        $tempoTotalPermanencia = $cartaoPrograma->cartaoProgramaPontos->sum('tempo_permanencia');
        $tempoTotalDeslocamento = $cartaoPrograma->cartaoProgramaPontos->sum('tempo_deslocamento');
        $tempoTotalItinerario = $tempoTotalPermanencia + $tempoTotalDeslocamento;

        return view('admin.cartoes-programa.show', compact(
            'cartaoPrograma', 
            'pontosDisponiveis', 
            'tempoTotalPermanencia', 
            'tempoTotalDeslocamento', 
            'tempoTotalItinerario'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CartaoPrograma $cartaoPrograma)
    {
        $postos = PostoTrabalho::ativos()->orderBy('nome')->get();
        
        return view('admin.cartoes-programa.edit', compact('cartaoPrograma', 'postos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartaoPrograma $cartaoPrograma)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'posto_trabalho_id' => 'required|exists:posto_trabalho,id',
            'horario_inicio' => 'required|string',
            'horario_fim' => 'required|string',
        ]);

        $cartaoPrograma->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'posto_trabalho_id' => $request->posto_trabalho_id,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim' => $request->horario_fim,
            'ativo' => $request->has('ativo')
        ]);

        return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
            ->with('success', 'Cartão Programa atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartaoPrograma $cartaoPrograma)
    {
        // Verificar se há escalas usando este cartão
        if ($cartaoPrograma->escalas()->count() > 0) {
            return redirect()->route('admin.cartoes-programa.index')
                ->with('error', 'Não é possível excluir este cartão programa. Existem escalas vinculadas a ele.');
        }

        $cartaoPrograma->delete();

        return redirect()->route('admin.cartoes-programa.index')
            ->with('success', 'Cartão Programa excluído com sucesso!');
    }

    /**
     * Adicionar ponto base ao cartão programa
     */
    public function adicionarPonto(Request $request, CartaoPrograma $cartaoPrograma)
    {
        // Debug log
        \Log::info('CartaoProgramaController::adicionarPonto chamado', [
            'cartao_id' => $cartaoPrograma->id,
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'ponto_base_id' => 'required|exists:ponto_base,id',
                'tempo_permanencia' => 'required|integer|min:1|max:120',
                'tempo_deslocamento' => 'required|integer|min:1|max:60',
                'instrucoes_especificas' => 'nullable|string',
                'obrigatorio' => 'nullable|in:on,1,true'
            ]);
            \Log::info('Validação passou com sucesso');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação', [
                'errors' => $e->errors(),
                'messages' => $e->getMessage()
            ]);
            return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
                ->withErrors($e->errors())
                ->withInput();
        }

        // Verificar se o ponto já está no cartão
        $jaExiste = $cartaoPrograma->cartaoProgramaPontos()->where('ponto_base_id', $request->ponto_base_id)->exists();
        \Log::info('Verificação de ponto existente', ['ja_existe' => $jaExiste, 'ponto_base_id' => $request->ponto_base_id]);
        
        if ($jaExiste) {
            \Log::info('Ponto já existe, redirecionando com erro');
            return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
                ->with('error', 'Este ponto base já está incluído no cartão programa.');
        }

        // Obter próxima ordem
        $proximaOrdem = $cartaoPrograma->cartaoProgramaPontos()->max('ordem') + 1;
        \Log::info('Próxima ordem calculada', ['proxima_ordem' => $proximaOrdem]);

        try {
            $novoPonto = $cartaoPrograma->cartaoProgramaPontos()->create([
                'ponto_base_id' => $request->ponto_base_id,
                'ordem' => $proximaOrdem,
                'tempo_permanencia' => $request->tempo_permanencia,
                'tempo_deslocamento' => $request->tempo_deslocamento,
                'instrucoes_especificas' => $request->instrucoes_especificas,
                'obrigatorio' => $request->has('obrigatorio') && in_array($request->obrigatorio, ['on', '1', 'true', true])
            ]);
            \Log::info('Ponto criado com sucesso', ['novo_ponto_id' => $novoPonto->id]);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar ponto', ['erro' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
                ->with('error', 'Erro ao adicionar ponto: ' . $e->getMessage());
        }

        // Recalcular tempo total
        $cartaoPrograma->calcularTempoTotal();
        \Log::info('Tempo total recalculado');

        return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
            ->with('success', 'Ponto base adicionado ao cartão programa com sucesso!');
    }

    /**
     * Remover ponto base do cartão programa
     */
    public function removerPonto(CartaoPrograma $cartaoPrograma, $pontoId)
    {
        $programaPonto = $cartaoPrograma->cartaoProgramaPontos()
                                       ->where('id', $pontoId)
                                       ->first();

        if (!$programaPonto) {
            return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
                ->with('error', 'Ponto base não encontrado no cartão programa.');
        }

        $ordemRemovida = $programaPonto->ordem;
        $programaPonto->delete();

        // Reordenar pontos restantes
        $cartaoPrograma->cartaoProgramaPontos()
                      ->where('ordem', '>', $ordemRemovida)
                      ->decrement('ordem');

        // Recalcular tempo total
        $cartaoPrograma->calcularTempoTotal();

        return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
            ->with('success', 'Ponto base removido do cartão programa com sucesso!');
    }

    /**
     * Reordenar pontos do cartão programa
     */
    public function reordenarPontos(Request $request, CartaoPrograma $cartaoPrograma)
    {
        $request->validate([
            'pontos' => 'required|array',
            'pontos.*.id' => 'required|exists:cartao_programa_pontos,id',
            'pontos.*.ordem' => 'required|integer|min:1'
        ]);

        foreach ($request->pontos as $ponto) {
            $cartaoPrograma->cartaoProgramaPontos()
                          ->where('id', $ponto['id'])
                          ->update(['ordem' => $ponto['ordem']]);
        }

        return response()->json(['success' => true, 'message' => 'Ordem dos pontos atualizada com sucesso!']);
    }

    /**
     * Duplicar cartão programa
     */
    public function duplicar(Request $request, CartaoPrograma $cartaoPrograma)
    {
        $request->validate([
            'novo_nome' => 'required|string|max:100'
        ]);

        $novoCartao = $cartaoPrograma->duplicar($request->novo_nome);

        return redirect()->route('admin.cartoes-programa.show', $novoCartao)
            ->with('success', 'Cartão Programa duplicado com sucesso!');
    }

    /**
     * Buscar cartões programa por posto (para AJAX)
     */
    public function porPosto($postoId)
    {
        $cartoes = CartaoPrograma::where('posto_trabalho_id', $postoId)
                                ->where('ativo', true)
                                ->orderBy('nome')
                                ->get(['id', 'nome', 'horario_inicio', 'horario_fim']);
        
        return response()->json($cartoes);
    }

    /**
     * Buscar dados de um ponto específico do cartão programa (para edição)
     */
    public function buscarPonto(CartaoPrograma $cartaoPrograma, $pontoId)
    {
        $ponto = $cartaoPrograma->cartaoProgramaPontos()
                                ->with('pontoBase')
                                ->where('id', $pontoId)
                                ->first();
        
        if (!$ponto) {
            return response()->json(['error' => 'Ponto não encontrado'], 404);
        }
        
        return response()->json($ponto);
    }
    
    /**
     * Editar um ponto do cartão programa
     */
    public function editarPonto(Request $request, CartaoPrograma $cartaoPrograma, $pontoId)
    {
        \Log::info('Iniciando edição de ponto', ['cartao_id' => $cartaoPrograma->id, 'ponto_id' => $pontoId]);
        
        try {
            $request->validate([
                'tempo_permanencia' => 'required|integer|min:1|max:120',
                'tempo_deslocamento' => 'required|integer|min:1|max:60',
                'instrucoes_especificas' => 'nullable|string',
                'obrigatorio' => 'nullable|in:on,1,true'
            ]);
            \Log::info('Validação da edição passou com sucesso');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação na edição do ponto', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        }
        
        // Buscar o ponto específico
        $ponto = $cartaoPrograma->cartaoProgramaPontos()->where('id', $pontoId)->first();
        
        if (!$ponto) {
            \Log::error('Ponto não encontrado para edição', ['ponto_id' => $pontoId]);
            return back()->withErrors(['error' => 'Ponto não encontrado no cartão programa.']);
        }
        
        try {
            // Atualizar os dados do ponto
            $ponto->update([
                'tempo_permanencia' => $request->tempo_permanencia,
                'tempo_deslocamento' => $request->tempo_deslocamento,
                'instrucoes_especificas' => $request->instrucoes_especificas,
                'obrigatorio' => $request->has('obrigatorio') && in_array($request->obrigatorio, ['on', '1', 'true', true])
            ]);
            
            \Log::info('Ponto editado com sucesso', ['ponto_id' => $ponto->id]);
            
            return redirect()->route('admin.cartoes-programa.show', $cartaoPrograma)
                           ->with('success', 'Ponto atualizado com sucesso!');
                           
        } catch (\Exception $e) {
            \Log::error('Erro ao editar ponto', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Erro ao editar o ponto. Tente novamente.']);
        }
    }
}
