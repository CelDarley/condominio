<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerario extends Model
{
    use HasFactory;

    protected $table = 'itinerario';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'posto_id',
        'ponto_origem_id',
        'ponto_destino_id',
        'tempo_estimado',
        'instrucoes',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'tempo_estimado' => 'integer',
    ];

    public function posto()
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_id');
    }

    public function pontoOrigem()
    {
        return $this->belongsTo(PontoBase::class, 'ponto_origem_id');
    }

    public function pontoDestino()
    {
        return $this->belongsTo(PontoBase::class, 'ponto_destino_id');
    }
}
