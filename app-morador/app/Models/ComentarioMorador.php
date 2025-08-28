<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioMorador extends Model
{
    protected $fillable = [
        'conteudo',
        'morador_id',
        'alerta_id',
        'tipo',
        'publico'
    ];

    protected $casts = [
        'publico' => 'boolean',
    ];

    public function morador()
    {
        return $this->belongsTo(Morador::class);
    }

    public function alerta()
    {
        return $this->belongsTo(Alerta::class);
    }
}
