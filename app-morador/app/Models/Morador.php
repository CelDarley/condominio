<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Morador extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'apartamento',
        'bloco',
        'cpf',
        'ativo',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function comentarios()
    {
        return $this->hasMany(ComentarioMorador::class);
    }

    public function solicitacoesPanico()
    {
        return $this->hasMany(SolicitacaoPanico::class);
    }
}
