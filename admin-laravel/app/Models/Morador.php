<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Morador extends Authenticatable
{
    use HasFactory;

    protected $table = 'moradores';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'endereco',
        'apartamento',
        'bloco',
        'cpf',
        'password',
        'ativo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class);
    }
}
