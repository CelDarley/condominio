<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    protected $table = 'aviso';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'titulo',
        'mensagem',
        'timestamp',
        'ativo',
        'data_criacao'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'timestamp' => 'datetime',
        'data_criacao' => 'datetime',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeRecentes($query)
    {
        return $query->orderBy('timestamp', 'desc');
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // Métodos auxiliares
    public function getTimestampFormatado()
    {
        return $this->timestamp ? $this->timestamp->format('d/m/Y H:i:s') : null;
    }

    public function getTimestampHumano()
    {
        return $this->timestamp ? $this->timestamp->diffForHumans() : null;
    }

    public function estaAtivo()
    {
        return $this->ativo;
    }

    public function getPrioridade()
    {
        // Lógica simples de prioridade baseada em palavras-chave
        $mensagemLower = strtolower($this->mensagem . ' ' . $this->titulo);
        
        if (strpos($mensagemLower, 'urgente') !== false || 
            strpos($mensagemLower, 'emergência') !== false ||
            strpos($mensagemLower, 'pânico') !== false) {
            return 'alta';
        }
        
        if (strpos($mensagemLower, 'importante') !== false ||
            strpos($mensagemLower, 'atenção') !== false) {
            return 'media';
        }
        
        return 'baixa';
    }

    public function getClassePrioridade()
    {
        switch ($this->getPrioridade()) {
            case 'alta':
                return 'danger';
            case 'media':
                return 'warning';
            default:
                return 'info';
        }
    }
} 