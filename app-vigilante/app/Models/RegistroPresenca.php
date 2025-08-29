<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroPresenca extends Model
{
    protected $table = 'registro_presenca';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'ponto_id',
        'timestamp_chegada',
        'timestamp_saida',
        'observacoes',
        'data_criacao'
    ];

    protected $casts = [
        'timestamp_chegada' => 'datetime',
        'timestamp_saida' => 'datetime',
        'data_criacao' => 'datetime',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function pontoBase()
    {
        return $this->belongsTo(PontoBase::class, 'ponto_id');
    }

    // Scopes
    public function scopeHoje($query)
    {
        return $query->whereDate('timestamp_chegada', now()->toDateString());
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorPonto($query, $pontoId)
    {
        return $query->where('ponto_id', $pontoId);
    }

    public function scopeAtivos($query)
    {
        return $query->whereNull('timestamp_saida');
    }

    public function scopeConcluidos($query)
    {
        return $query->whereNotNull('timestamp_saida');
    }

    // Métodos auxiliares
    public function estaAtivo()
    {
        return is_null($this->timestamp_saida);
    }

    public function estaConcluido()
    {
        return !is_null($this->timestamp_saida);
    }

    public function getTempoPermanencia()
    {
        if ($this->timestamp_saida) {
            return $this->timestamp_chegada->diffInMinutes($this->timestamp_saida);
        }
        
        return $this->timestamp_chegada->diffInMinutes(now());
    }

    public function getTempoPermanenciaFormatado()
    {
        $minutos = $this->getTempoPermanencia();
        
        if ($minutos >= 60) {
            $horas = intval($minutos / 60);
            $minutosRestantes = $minutos % 60;
            return sprintf('%dh %02dm', $horas, $minutosRestantes);
        }
        
        return sprintf('%dm', $minutos);
    }

    public function getStatus()
    {
        if ($this->timestamp_saida) {
            return 'concluido';
        }
        
        return 'presente';
    }

    public function getDataChegadaFormatada()
    {
        return $this->timestamp_chegada ? $this->timestamp_chegada->format('d/m/Y H:i:s') : null;
    }

    public function getDataSaidaFormatada()
    {
        return $this->timestamp_saida ? $this->timestamp_saida->format('d/m/Y H:i:s') : null;
    }
} 