<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartaoPrograma extends Model
{
    protected $table = 'cartao_programas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'posto_trabalho_id',
        'horario_inicio',
        'horario_fim',
        'tempo_total_estimado',
        'ativo',
        'configuracoes',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'horario_inicio' => 'datetime',
        'horario_fim' => 'datetime',
        'tempo_total_estimado' => 'integer',
        'configuracoes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function postoTrabalho()
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_trabalho_id');
    }

    public function escalas()
    {
        return $this->hasMany(Escala::class, 'cartao_programa_id');
    }

    public function pontos()
    {
        return $this->hasMany(CartaoProgramaPonto::class, 'cartao_programa_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorPosto($query, $postoId)
    {
        return $query->where('posto_trabalho_id', $postoId);
    }

    // Métodos auxiliares
    public function getHorarioInicioFormatado()
    {
        return $this->horario_inicio ? $this->horario_inicio->format('H:i') : null;
    }

    public function getHorarioFimFormatado()
    {
        return $this->horario_fim ? $this->horario_fim->format('H:i') : null;
    }

    public function getDuracaoFormatada()
    {
        if ($this->tempo_total_estimado) {
            $horas = intval($this->tempo_total_estimado / 60);
            $minutos = $this->tempo_total_estimado % 60;
            
            if ($horas > 0) {
                return sprintf('%dh %02dm', $horas, $minutos);
            } else {
                return sprintf('%dm', $minutos);
            }
        }
        
        return null;
    }

    public function getTotalPontos()
    {
        return $this->pontos()->count();
    }

    public function getPontosOrdenados()
    {
        return $this->pontos()->orderBy('ordem')->get();
    }

    public function temPontos()
    {
        return $this->pontos()->exists();
    }
} 