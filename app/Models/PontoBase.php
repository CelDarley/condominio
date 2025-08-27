<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PontoBase extends Model
{
    use HasFactory;

    protected $table = 'ponto_base';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'posto_id',
        'nome',
        'endereco',
        'descricao',
        'horario_inicio',
        'horario_fim',
        'tempo_permanencia',
        'instrucoes',
        'ordem',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'horario_inicio' => 'datetime',
        'horario_fim' => 'datetime',
        'tempo_permanencia' => 'integer',
        'ordem' => 'integer',
    ];

    public function posto()
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_id');
    }

    public function itinerariosOrigem()
    {
        return $this->hasMany(Itinerario::class, 'ponto_origem_id');
    }

    public function itinerariosDestino()
    {
        return $this->hasMany(Itinerario::class, 'ponto_destino_id');
    }
}
