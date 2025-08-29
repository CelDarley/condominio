<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EscalaDiaria extends Model
{
    protected $table = 'escala_diaria';
    
    protected $fillable = [
        'data',
        'escala_original_id',
        'usuario_original_id',
        'usuario_substituto_id',
        'posto_trabalho_id',
        'cartao_programa_id',
        'motivo',
        'status',
        'criado_por'
    ];

    protected $casts = [
        'data' => 'date',
        'status' => 'string'
    ];

    // Relacionamentos
    public function escalaOriginal()
    {
        return $this->belongsTo(Escala::class, 'escala_original_id');
    }

    public function usuarioOriginal()
    {
        return $this->belongsTo(Usuario::class, 'usuario_original_id');
    }

    public function usuarioSubstituto()
    {
        return $this->belongsTo(Usuario::class, 'usuario_substituto_id');
    }

    public function postoTrabalho()
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_trabalho_id');
    }

    public function cartaoPrograma()
    {
        return $this->belongsTo(CartaoPrograma::class, 'cartao_programa_id');
    }

    public function criadoPor()
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopePorData($query, $data)
    {
        return $query->where('data', $data);
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data', [$dataInicio, $dataFim]);
    }

    // Métodos auxiliares
    public function isAtivo()
    {
        return $this->status === 'ativo';
    }

    public function getDataFormatada()
    {
        return $this->data->format('d/m/Y');
    }

    public function getDiaSemana()
    {
        $dias = [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado'
        ];
        
        return $dias[$this->data->dayOfWeek];
    }

    // Método estático para obter escala efetiva de um dia - versão para app-vigilante
    public static function getEscalaEfetivaVigilante($data, $usuarioId = null, $postoId = null)
    {
        $diaSemana = Carbon::parse($data)->dayOfWeek;
        // Ajustar para padrão brasileiro (0 = segunda, 6 = domingo)
        $diaSemanaDb = ($diaSemana == 0) ? 6 : $diaSemana - 1;

        // Buscar escalas semanais para este dia
        $query = Escala::with(['usuario', 'postoTrabalho', 'cartaoPrograma'])
            ->where('dia_semana', $diaSemanaDb)
            ->where('ativo', true);
            
        if ($usuarioId) {
            $query->where('usuario_id', $usuarioId);
        }
        
        if ($postoId) {
            $query->where('posto_trabalho_id', $postoId);
        }
        
        $escalasSemanais = $query->get();

        // Buscar ajustes diários para esta data
        $ajustesQuery = self::with(['usuarioSubstituto', 'postoTrabalho', 'cartaoPrograma', 'escalaOriginal'])
            ->where('data', $data)
            ->where('status', 'ativo');
            
        if ($postoId) {
            $ajustesQuery->where('posto_trabalho_id', $postoId);
        }
        
        $ajustesDiarios = $ajustesQuery->get()->keyBy('escala_original_id');

        // Aplicar ajustes diários às escalas semanais
        $escalasEfetivas = $escalasSemanais->map(function ($escala) use ($ajustesDiarios) {
            if (isset($ajustesDiarios[$escala->id])) {
                $ajuste = $ajustesDiarios[$escala->id];
                
                // Só aplicar ajuste se o usuário original for o mesmo da escala
                if ($ajuste->usuario_original_id == $escala->usuario_id) {
                    // Criar cópia da escala com os dados do substituto
                    $escalaAjustada = clone $escala;
                    $escalaAjustada->usuario_id = $ajuste->usuario_substituto_id;
                    $escalaAjustada->usuario = $ajuste->usuarioSubstituto;
                    
                    // Se tem cartão programa específico, usar ele, senão manter original
                    if ($ajuste->cartao_programa_id) {
                        $escalaAjustada->cartao_programa_id = $ajuste->cartao_programa_id;
                        $escalaAjustada->cartaoPrograma = $ajuste->cartaoPrograma;
                    }
                    
                    $escalaAjustada->ajuste_diario = $ajuste;
                    $escalaAjustada->tem_ajuste = true;
                    
                    return $escalaAjustada;
                }
            }
            
            $escala->tem_ajuste = false;
            return $escala;
        });

        return $escalasEfetivas;
    }

    // Método específico para buscar escala de um vigilante em uma data
    public static function getEscalaVigilante($data, $usuarioId)
    {
        $escalas = self::getEscalaEfetivaVigilante($data, $usuarioId);
        return $escalas->first();
    }
} 