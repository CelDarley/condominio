<?php

namespace App\Http\Controllers;

use App\Models\RegistroPresenca;
use App\Models\PontoBase;
use App\Models\Escala;
use App\Models\CartaoProgramaPonto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresencaController extends Controller
{
    // Registrar chegada em um ponto
    public function registrarChegada(Request $request, $pontoId)
    {
        try {
            $user = Auth::user();
            $hoje = today();
            $agora = now();

            // Verificar se o ponto existe
            $ponto = PontoBase::findOrFail($pontoId);

            // Buscar escala ativa do usuário para hoje
            $escala = Escala::where('usuario_id', $user->id)
                ->whereJsonContains('dias_semana', $hoje->dayOfWeek == 0 ? 6 : $hoje->dayOfWeek - 1)
                ->where('ativo', true)
                ->first();

            if (!$escala) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Você não possui escala ativa para hoje.'
                ], 400);
            }

            // Verificar se já está presente no ponto
            if (RegistroPresenca::estaPresenteNoPonto($user->id, $pontoId, $hoje)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Você já registrou chegada neste ponto. Registre a saída primeiro.'
                ], 400);
            }

            // Buscar cartão programa ponto (se existir)
            $cartaoProgramaPonto = null;
            if ($escala->cartao_programa_id) {
                $cartaoProgramaPonto = CartaoProgramaPonto::where('cartao_programa_id', $escala->cartao_programa_id)
                    ->where('ponto_base_id', $pontoId)
                    ->first();
            }

            // Capturar localização se fornecida
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            // Criar registro de chegada
            $registro = RegistroPresenca::create([
                'usuario_id' => $user->id,
                'escala_id' => $escala->id,
                'ponto_base_id' => $pontoId,
                'cartao_programa_ponto_id' => $cartaoProgramaPonto ? $cartaoProgramaPonto->id : null,
                'data' => $hoje,
                'tipo' => 'chegada',
                'data_hora_registro' => $agora,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'observacoes' => $request->input('observacoes'),
                'status' => 'normal'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Chegada registrada com sucesso!',
                'registro' => [
                    'id' => $registro->id,
                    'ponto_nome' => $ponto->nome,
                    'data_hora' => $registro->getDataHoraFormatada(),
                    'tipo' => 'chegada'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao registrar chegada: ' . $e->getMessage()
            ], 500);
        }
    }

    // Registrar saída de um ponto
    public function registrarSaida(Request $request, $pontoId)
    {
        try {
            $user = Auth::user();
            $hoje = today();
            $agora = now();

            // Verificar se o ponto existe
            $ponto = PontoBase::findOrFail($pontoId);

            // Verificar se está presente no ponto
            if (!RegistroPresenca::estaPresenteNoPonto($user->id, $pontoId, $hoje)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Você não está presente neste ponto. Registre a chegada primeiro.'
                ], 400);
            }

            // Buscar escala ativa do usuário para hoje
            $escala = Escala::where('usuario_id', $user->id)
                ->whereJsonContains('dias_semana', $hoje->dayOfWeek == 0 ? 6 : $hoje->dayOfWeek - 1)
                ->where('ativo', true)
                ->first();

            // Buscar cartão programa ponto (se existir)
            $cartaoProgramaPonto = null;
            if ($escala && $escala->cartao_programa_id) {
                $cartaoProgramaPonto = CartaoProgramaPonto::where('cartao_programa_id', $escala->cartao_programa_id)
                    ->where('ponto_base_id', $pontoId)
                    ->first();
            }

            // Capturar localização se fornecida
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            // Criar registro de saída
            $registro = RegistroPresenca::create([
                'usuario_id' => $user->id,
                'escala_id' => $escala ? $escala->id : null,
                'ponto_base_id' => $pontoId,
                'cartao_programa_ponto_id' => $cartaoProgramaPonto ? $cartaoProgramaPonto->id : null,
                'data' => $hoje,
                'tipo' => 'saida',
                'data_hora_registro' => $agora,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'observacoes' => $request->input('observacoes'),
                'status' => 'normal'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Saída registrada com sucesso!',
                'registro' => [
                    'id' => $registro->id,
                    'ponto_nome' => $ponto->nome,
                    'data_hora' => $registro->getDataHoraFormatada(),
                    'tipo' => 'saida'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao registrar saída: ' . $e->getMessage()
            ], 500);
        }
    }

    // Ver histórico de registros
    public function historico(Request $request)
    {
        $user = Auth::user();
        
        // Filtros
        $dataInicio = $request->input('data_inicio', today()->toDateString());
        $dataFim = $request->input('data_fim', today()->toDateString());

        $registros = RegistroPresenca::where('usuario_id', $user->id)
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->with(['pontoBase', 'escala'])
            ->orderBy('data_hora_registro', 'desc')
            ->get();

        return view('presenca.historico', compact('registros', 'dataInicio', 'dataFim'));
    }

    // Status atual dos pontos para hoje
    public function statusHoje()
    {
        try {
            $user = Auth::user();
            $hoje = today();
            $diaSemana = $hoje->dayOfWeek == 0 ? 6 : $hoje->dayOfWeek - 1;

            // Buscar escala ativa do usuário para hoje
            $escala = Escala::where('usuario_id', $user->id)
                ->whereJsonContains('dias_semana', $diaSemana)
                ->where('ativo', true)
                ->first();

            if (!$escala) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nenhuma escala ativa encontrada para hoje.'
                ], 404);
            }

            $pontos = [];
            
            // Buscar pontos do cartão programa se existir
            if ($escala->cartao_programa_id) {
                $cartaoPontos = CartaoProgramaPonto::where('cartao_programa_id', $escala->cartao_programa_id)
                    ->with('pontoBase')
                    ->orderBy('ordem')
                    ->get();
                
                foreach ($cartaoPontos as $cartaoPonto) {
                    $ultimoRegistro = RegistroPresenca::ultimaPresencaPonto(
                        $user->id, 
                        $cartaoPonto->ponto_base_id, 
                        $hoje
                    );

                    $pontos[] = [
                        'id' => $cartaoPonto->pontoBase->id,
                        'nome' => $cartaoPonto->pontoBase->nome,
                        'ordem' => $cartaoPonto->ordem,
                        'tempo_permanencia' => $cartaoPonto->tempo_permanencia,
                        'presente' => $ultimoRegistro && $ultimoRegistro->tipo === 'chegada',
                        'ultimo_registro' => $ultimoRegistro ? [
                            'tipo' => $ultimoRegistro->tipo,
                            'data_hora' => $ultimoRegistro->getDataHoraFormatada()
                        ] : null
                    ];
                }
            }

            return response()->json([
                'escala' => [
                    'id' => $escala->id,
                    'nome' => $escala->nome,
                    'posto' => $escala->postoTrabalho ? $escala->postoTrabalho->nome : 'N/A'
                ],
                'pontos' => $pontos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }
} 