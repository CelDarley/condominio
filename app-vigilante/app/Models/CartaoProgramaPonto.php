<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartaoProgramaPonto extends Model
{
    protected $table = 'cartao_programa_pontos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cartao_programa_id',
        'ponto_base_id',
        'ordem',
        'tempo_permanencia',
        'tempo_deslocamento',
        'instrucoes_especificas',
        'obrigatorio',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'ordem' => 'integer',
        'tempo_permanencia' => 'integer',
        'tempo_deslocamento' => 'integer',
        'obrigatorio' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function cartaoPrograma()
    {
        return $this->belongsTo(CartaoPrograma::class, 'cartao_programa_id');
    }

    public function pontoBase()
    {
        return $this->belongsTo(PontoBase::class, 'ponto_base_id');
    }

    // Scopes
    public function scopeObrigatorios($query)
    {
        return $query->where('obrigatorio', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem');
    }

    // Métodos auxiliares
    public function getTempoPermanenciaFormatado()
    {
        if ($this->tempo_permanencia >= 60) {
            $horas = intval($this->tempo_permanencia / 60);
            $minutos = $this->tempo_permanencia % 60;
            return sprintf('%dh %02dm', $horas, $minutos);
        }
        
        return sprintf('%dm', $this->tempo_permanencia);
    }

    public function getTempoDeslocamentoFormatado()
    {
        if ($this->tempo_deslocamento >= 60) {
            $horas = intval($this->tempo_deslocamento / 60);
            $minutos = $this->tempo_deslocamento % 60;
            return sprintf('%dh %02dm', $horas, $minutos);
        }
        
        return sprintf('%dm', $this->tempo_deslocamento);
    }

    public function ehObrigatorio()
    {
        return $this->obrigatorio;
    }

    public function temInstrucoes()
    {
        return !empty($this->instrucoes_especificas);
    }
} 