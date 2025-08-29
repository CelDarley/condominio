<?php

namespace App\Http\Controllers;

use App\Models\RegistroPresenca;
use App\Models\PontoBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresencaController extends Controller
{
    // Middleware aplicado diretamente nas rotas

    public function registrar(Request $request, $pontoId)
    {
        try {
            $user = Auth::user();
            $agora = now();
            $hoje = $agora->startOfDay();

            // Verificar se o ponto existe
            $ponto = PontoBase::findOrFail($pontoId);

            // Verificar se já existe um registro de chegada para hoje
            $registro = RegistroPresenca::where('usuario_id', $user->id)
                ->where('ponto_id', $pontoId)
                ->where('timestamp_chegada', '>=', $hoje)
                ->first();

            if (!$registro) {
                // Novo registro de chegada
                $novoRegistro = RegistroPresenca::create([
                    'usuario_id' => $user->id,
                    'ponto_id' => $pontoId,
                    'timestamp_chegada' => $agora,
                    'data_criacao' => $agora
                ]);

                return response()->json([
                    'status' => 'chegada',
                    'message' => 'Presença registrada com sucesso!',
                    'timestamp_chegada' => $novoRegistro->getDataChegadaFormatada(),
                    'ponto_nome' => $ponto->nome
                ]);
            } else {
                if ($registro->timestamp_saida) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Você já registrou chegada e saída neste ponto hoje.'
                    ], 400);
                }

                // Registrar saída
                $registro->update([
                    'timestamp_saida' => $agora
                ]);

                return response()->json([
                    'status' => 'saida',
                    'message' => 'Saída registrada com sucesso!',
                    'timestamp_chegada' => $registro->getDataChegadaFormatada(),
                    'timestamp_saida' => $registro->getDataSaidaFormatada(),
                    'tempo_permanencia' => $registro->getTempoPermanenciaFormatado(),
                    'ponto_nome' => $ponto->nome
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao registrar presença: ' . $e->getMessage()
            ], 500);
        }
    }

    public function historico(Request $request)
    {
        $user = Auth::user();
        
        // Filtros
        $dataInicio = $request->input('data_inicio', now()->startOfMonth()->toDateString());
        $dataFim = $request->input('data_fim', now()->toDateString());
        $pontoId = $request->input('ponto_id');

        $query = RegistroPresenca::where('usuario_id', $user->id)
            ->whereBetween('timestamp_chegada', [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59'])
            ->with(['pontoBase', 'pontoBase.postoTrabalho'])
            ->orderBy('timestamp_chegada', 'desc');

        if ($pontoId) {
            $query->where('ponto_id', $pontoId);
        }

        $registros = $query->paginate(20);

        // Estatísticas
        $totalRegistros = $query->count();
        $registrosConcluidos = $query->whereNotNull('timestamp_saida')->count();
        $registrosAtivos = $totalRegistros - $registrosConcluidos;

        return view('presenca.historico', compact(
            'registros',
            'totalRegistros',
            'registrosConcluidos',
            'registrosAtivos',
            'dataInicio',
            'dataFim',
            'pontoId'
        ));
    }

    public function relatorio(Request $request)
    {
        $user = Auth::user();
        
        // Relatório dos últimos 30 dias
        $dataInicio = now()->subDays(30)->startOfDay();
        $dataFim = now()->endOfDay();

        $registros = RegistroPresenca::where('usuario_id', $user->id)
            ->whereBetween('timestamp_chegada', [$dataInicio, $dataFim])
            ->with(['pontoBase', 'pontoBase.postoTrabalho'])
            ->orderBy('timestamp_chegada', 'desc')
            ->get();

        // Agrupar por posto
        $registrosPorPosto = $registros->groupBy(function ($registro) {
            return $registro->pontoBase->postoTrabalho->nome ?? 'Posto não identificado';
        });

        // Estatísticas gerais
        $estatisticas = [
            'total_registros' => $registros->count(),
            'registros_concluidos' => $registros->where('timestamp_saida', '!=', null)->count(),
            'tempo_total_minutos' => $registros->sum(function ($registro) {
                return $registro->getTempoPermanencia();
            }),
            'postos_visitados' => $registrosPorPosto->count(),
            'pontos_visitados' => $registros->unique('ponto_id')->count()
        ];

        $estatisticas['tempo_total_formatado'] = $this->formatarTempo($estatisticas['tempo_total_minutos']);
        $estatisticas['tempo_medio_formatado'] = $estatisticas['total_registros'] > 0 
            ? $this->formatarTempo($estatisticas['tempo_total_minutos'] / $estatisticas['total_registros'])
            : '0m';

        return view('presenca.relatorio', compact(
            'registrosPorPosto',
            'estatisticas',
            'dataInicio',
            'dataFim'
        ));
    }

    private function formatarTempo($minutos)
    {
        if ($minutos >= 60) {
            $horas = intval($minutos / 60);
            $minutosRestantes = $minutos % 60;
            return sprintf('%dh %02dm', $horas, $minutosRestantes);
        }
        
        return sprintf('%dm', $minutos);
    }
} 