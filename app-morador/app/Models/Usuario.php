<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'data_atualizacao',
        'coordenadas_atual',
        'ultima_atualizacao_localizacao',
        'online'
    ];

    protected $hidden = [
        'senha_hash',
        'remember_token',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'online' => 'boolean',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime',
        'ultima_atualizacao_localizacao' => 'datetime',
        'coordenadas_atual' => 'array',
    ];

    // Override métodos de autenticação para usar senha_hash
    public function getAuthPassword()
    {
        return $this->senha_hash;
    }

    public function getAuthPasswordName()
    {
        return 'senha_hash';
    }

    // Métodos auxiliares para verificar tipo
    public function isMorador()
    {
        return $this->tipo === 'morador';
    }

    public function isVigilante()
    {
        return $this->tipo === 'vigilante';
    }

    public function isAdmin()
    {
        return $this->tipo === 'admin';
    }

    // Relacionamentos
    public function alertas()
    {
        return $this->hasMany(Alerta::class, 'usuario_id');
    }

    public function comentarios()
    {
        return $this->hasMany(ComentarioMorador::class, 'usuario_id');
    }

    public function solicitacoesPanico()
    {
        return $this->hasMany(SolicitacaoPanico::class, 'usuario_id');
    }

    // Scopes
    public function scopeMoradores($query)
    {
        return $query->where('tipo', 'morador');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('online', true);
    }
} 