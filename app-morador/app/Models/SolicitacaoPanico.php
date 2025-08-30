<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitacaoPanico extends Model
{
    protected $table = 'solicitacao_panicos';
    
    protected $fillable = [
        'morador_id',
        'titulo',
        'descricao',
        'status',
        'prioridade',
        'latitude',
        'longitude',
        'endereco',
        'resolvido_em',
        'observacoes'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'resolvido_em' => 'datetime'
    ];

    public function morador()
    {
        return $this->belongsTo(Usuario::class, 'morador_id');
    }
}
