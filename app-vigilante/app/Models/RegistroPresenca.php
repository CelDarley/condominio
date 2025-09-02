<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroPresenca extends Model
{
    protected $table = 'registro_presenca';
    protected $primaryKey = 'id';
    public $timestamps = true; // Usando created_at e updated_at

    protected $fillable = [
        'usuario_id',
        'escala_id',
        'ponto_base_id',
        'cartao_programa_ponto_id',
        'data',
        'tipo',
        'data_hora_registro',
        'latitude',
        'longitude',
        'observacoes',
        'status'
    ];

    protected $casts = [
        'data' => 'date',
        'data_hora_registro' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'tipo' => 'string',
        'status' => 'string'
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function escala()
    {
        return $this->belongsTo(Escala::class, 'escala_id');
    }

    public function pontoBase()
    {
        return $this->belongsTo(PontoBase::class, 'ponto_base_id');
    }

    public function cartaoProgramaPonto()
    {
        return $this->belongsTo(CartaoProgramaPonto::class, 'cartao_programa_ponto_id');
    }

    // Scopes
    public function scopeHoje($query)
    {
        return $query->whereDate('data', today());
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorPonto($query, $pontoId)
    {
        return $query->where('ponto_base_id', $pontoId);
    }

    public function scopePorEscala($query, $escalaId)
    {
        return $query->where('escala_id', $escalaId);
    }

    public function scopeTipoChegada($query)
    {
        return $query->where('tipo', 'chegada');
    }

    public function scopeTipoSaida($query)
    {
        return $query->where('tipo', 'saida');
    }

    // Métodos auxiliares
    public function isChegada()
    {
        return $this->tipo === 'chegada';
    }

    public function isSaida()
    {
        return $this->tipo === 'saida';
    }

    public function getDataHoraFormatada()
    {
        return $this->data_hora_registro ? $this->data_hora_registro->format('d/m/Y H:i:s') : null;
    }

    public function getStatusBadge()
    {
        $badges = [
            'normal' => 'bg-success',
            'atraso' => 'bg-warning',
            'antecipado' => 'bg-info'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    // Método estático para verificar última presença em um ponto
    public static function ultimaPresencaPonto($usuarioId, $pontoBaseId, $data)
    {
        return static::where('usuario_id', $usuarioId)
            ->where('ponto_base_id', $pontoBaseId)
            ->where('data', $data)
            ->orderBy('data_hora_registro', 'desc')
            ->first();
    }

    // Método para verificar se o vigilante está presente no ponto
    public static function estaPresenteNoPonto($usuarioId, $pontoBaseId, $data)
    {
        $ultimo = static::ultimaPresencaPonto($usuarioId, $pontoBaseId, $data);
        return $ultimo && $ultimo->tipo === 'chegada';
    }
} 