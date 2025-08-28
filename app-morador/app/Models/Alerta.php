<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'tipo',
        'prioridade',
        'status',
        'usuario_id',
        'localizacao',
        'coordenadas',
        'resolvido_em'
    ];

    protected $casts = [
        'coordenadas' => 'array',
        'resolvido_em' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function comentarios()
    {
        return $this->hasMany(ComentarioMorador::class);
    }
}
