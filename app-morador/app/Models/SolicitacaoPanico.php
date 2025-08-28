<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitacaoPanico extends Model
{
    protected $fillable = [
        'morador_id',
        'descricao',
        'tipo',
        'status',
        'localizacao',
        'coordenadas',
        'atendido_em',
        'atendido_por',
        'observacoes_atendimento'
    ];

    protected $casts = [
        'coordenadas' => 'array',
        'atendido_em' => 'datetime'
    ];

    public function morador()
    {
        return $this->belongsTo(Morador::class);
    }

    public function atendidoPor()
    {
        return $this->belongsTo(Usuario::class, 'atendido_por');
    }
}
