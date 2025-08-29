<?php

namespace App\Http\Controllers;

use App\Models\PostoTrabalho;
use App\Models\PontoBase;
use App\Models\Escala;
use App\Models\EscalaDiaria;
use App\Models\CartaoPrograma;
use App\Models\CartaoProgramaPonto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostoController extends Controller
{
    // Middleware aplicado diretamente nas rotas

    public function show($postoId)
    {
        $posto = PostoTrabalho::findOrFail($postoId);
        $user = Auth::user();
        
        // Usar a nova lógica de escala efetiva para hoje
        $escala = EscalaDiaria::getEscalaVigilante(now()->format('Y-m-d'), $user->id);
        
        // Verificar se a escala é para este posto
        if (!$escala || $escala->posto_trabalho_id != $postoId) {
            return redirect()->route('dashboard')->with('error', 'Você não tem escala para este posto hoje.');
        }

        $pontosBase = collect();
        $cartaoPrograma = null;

        // Se há escala com cartão programa, usar os pontos do cartão
        if ($escala && $escala->cartao_programa_id) {
            $cartaoPrograma = $escala->cartaoPrograma;
            
            if ($cartaoPrograma) {
                $pontosBase = CartaoProgramaPonto::where('cartao_programa_id', $cartaoPrograma->id)
                    ->with('pontoBase')
                    ->orderBy('ordem')
                    ->get()
                    ->pluck('pontoBase');
            }
        } else {
            // Se não há cartão programa, usar pontos base do posto
            $pontosBase = PontoBase::where('posto_trabalho_id', $postoId)
                ->where('ativo', true)
                ->orderBy('ordem')
                ->get();
        }

        return view('posto.show', compact('posto', 'pontosBase', 'cartaoPrograma', 'escala'));
    }

    public function statusPontos($postoId)
    {
        $user = Auth::user();
        $hoje = now()->startOfDay();

        // Buscar pontos base do posto
        $pontosBase = PontoBase::where('posto_id', $postoId)
            ->where('ativo', true)
            ->get();

        $statusPontos = [];
        
        foreach ($pontosBase as $ponto) {
            $status = $ponto->getStatusPresencaHoje($user->id);
            $ultimoRegistro = $ponto->getUltimoRegistro($user->id);
            
            $statusPontos[] = [
                'ponto_id' => $ponto->id,
                'status' => $status,
                'timestamp_chegada' => $ultimoRegistro && $ultimoRegistro->timestamp_chegada >= $hoje 
                    ? $ultimoRegistro->getDataChegadaFormatada() : null,
                'timestamp_saida' => $ultimoRegistro && $ultimoRegistro->timestamp_saida 
                    ? $ultimoRegistro->getDataSaidaFormatada() : null
            ];
        }

        return response()->json($statusPontos);
    }

    private function calcularStatusPontos($pontosBase, $usuarioId)
    {
        $statusPontos = [];
        
        foreach ($pontosBase as $ponto) {
            $status = $ponto->getStatusPresencaHoje($usuarioId);
            $ultimoRegistro = $ponto->getUltimoRegistro($usuarioId);
            
            $statusPontos[$ponto->id] = [
                'status' => $status,
                'ultimo_registro' => $ultimoRegistro,
                'timestamp_chegada' => $ultimoRegistro ? $ultimoRegistro->getDataChegadaFormatada() : null,
                'timestamp_saida' => $ultimoRegistro && $ultimoRegistro->timestamp_saida 
                    ? $ultimoRegistro->getDataSaidaFormatada() : null
            ];
        }

        return $statusPontos;
    }
} 