<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'tipo',
        'ativo',
        'coordenadas_atual',
        'ultima_atualizacao_localizacao',
        'online'
    ];

    protected $hidden = [
        'senha',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'online' => 'boolean',
        'coordenadas_atual' => 'array',
        'ultima_atualizacao_localizacao' => 'datetime'
    ];

    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'usuario_id');
    }

    public function solicitacoesAtendidas()
    {
        return $this->hasMany(SolicitacaoPanico::class, 'atendido_por');
    }
}
