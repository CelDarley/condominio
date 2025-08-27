<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostoTrabalho extends Model
{
    use HasFactory;

    protected $table = 'posto_trabalho';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'ativo',
        'data_criacao'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_criacao' => 'datetime',
    ];

    public function pontosBase()
    {
        return $this->hasMany(PontoBase::class, 'posto_id');
    }

    public function escalas()
    {
        return $this->hasMany(Escala::class, 'posto_trabalho_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'escala', 'posto_trabalho_id', 'usuario_id');
    }
}
