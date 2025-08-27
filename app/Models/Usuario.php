<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'senha_hash',
        'tipo',
        'telefone',
        'ativo',
        'data_criacao',
        'data_atualizacao'
    ];

    protected $hidden = [
        'senha_hash',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime',
    ];

    public function escalas()
    {
        return $this->hasMany(Escala::class, 'usuario_id');
    }

    public function postosTrabalho()
    {
        return $this->belongsToMany(PostoTrabalho::class, 'escala', 'usuario_id', 'posto_trabalho_id');
    }

    public function isAdmin()
    {
        return $this->tipo === 'admin';
    }

    public function isVigilante()
    {
        return $this->tipo === 'vigilante';
    }

    public function isMorador()
    {
        return $this->tipo === 'morador';
    }
}
