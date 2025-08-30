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
        'usuario_id',
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

    // Relacionamento com usuário (autenticação)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relacionamento com veículos
    public function veiculos()
    {
        return $this->hasMany(Veiculo::class);
    }

    // Método helper para obter dados completos
    public function getDadosCompletosAttribute()
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'endereco_completo' => "{$this->endereco}, Apt {$this->apartamento}" . 
                                  ($this->bloco ? ", Bloco {$this->bloco}" : ''),
            'apartamento' => $this->apartamento,
            'bloco' => $this->bloco,
            'cpf' => $this->cpf,
            'quantidade_veiculos' => $this->veiculos()->count(),
            'ativo' => $this->ativo
        ];
    }
}
