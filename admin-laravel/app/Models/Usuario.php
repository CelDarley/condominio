<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nome',
        'email',
        'senha_hash',
        'tipo',
        'telefone',
        'ativo',
    ];

    protected $hidden = [
        'senha_hash',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Sobrescrever o método de autenticação para usar senha_hash
    public function getAuthPassword()
    {
        return $this->senha_hash;
    }

    // Método para verificar se é admin
    public function isAdmin()
    {
        return $this->tipo === 'admin';
    }

    // Método para verificar se é vigilante
    public function isVigilante()
    {
        return $this->tipo === 'vigilante';
    }

    // Método para verificar se é morador
    public function isMorador()
    {
        return $this->tipo === 'morador';
    }

    // Relacionamentos
    public function escalas()
    {
        return $this->hasMany(Escala::class, 'usuario_id');
    }

    public function postosTrabalho()
    {
        return $this->belongsToMany(PostoTrabalho::class, 'escala', 'usuario_id', 'posto_trabalho_id');
    }

    // Relacionamento com dados específicos de morador
    public function dadosMorador()
    {
        return $this->hasOne(Morador::class, 'usuario_id')->where('ativo', true);
    }

    // Método helper para acessar veículos (apenas para moradores)
    public function veiculos()
    {
        return $this->dadosMorador() ? $this->dadosMorador->veiculos() : collect();
    }

    // Método helper para obter endereço completo (apenas para moradores)
    public function getEnderecoCompletoAttribute()
    {
        if ($this->isMorador() && $this->dadosMorador) {
            $dados = $this->dadosMorador;
            return "{$dados->endereco}, Apt {$dados->apartamento}" .
                   ($dados->bloco ? ", Bloco {$dados->bloco}" : '');
        }
        return null;
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
