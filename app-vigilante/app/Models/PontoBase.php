<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PontoBase extends Model
{
    protected $table = 'ponto_base';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'posto_trabalho_id',
        'nome',
        'endereco',
        'descricao',
        'latitude',
        'longitude',
        'qr_code',
        'ativo',
        'data_criacao'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'data_criacao' => 'datetime',
    ];

    // Relacionamentos
    public function postoTrabalho()
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_trabalho_id');
    }

    public function registrosPresenca()
    {
        return $this->hasMany(RegistroPresenca::class, 'ponto_id');
    }

    public function cartaoProgramaPontos()
    {
        return $this->hasMany(CartaoProgramaPonto::class, 'ponto_base_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorPosto($query, $postoId)
    {
        return $query->where('posto_id', $postoId);
    }

    // Métodos auxiliares
    public function temCoordenadas()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function temQrCode()
    {
        return !is_null($this->qr_code) && !empty($this->qr_code);
    }

    public function getEnderecoCompleto()
    {
        return $this->endereco ?? $this->nome;
    }

    public function getStatusPresencaHoje($usuarioId)
    {
        $hoje = now()->startOfDay();
        
        $registro = $this->registrosPresenca()
            ->where('usuario_id', $usuarioId)
            ->where('timestamp_chegada', '>=', $hoje)
            ->first();

        if (!$registro) {
            return 'pendente';
        }

        if ($registro->timestamp_saida) {
            return 'concluido';
        }

        return 'presente';
    }

    public function getUltimoRegistro($usuarioId)
    {
        return $this->registrosPresenca()
            ->where('usuario_id', $usuarioId)
            ->orderBy('timestamp_chegada', 'desc')
            ->first();
    }
} 