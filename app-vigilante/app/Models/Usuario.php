<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'senha_hash',
        'tipo',
        'ativo',
        'telefone',
        'data_criacao',
        'data_atualizacao'
    ];

    protected $hidden = [
        'senha_hash',
        'remember_token',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime',
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

    // Verificar senha compatível com bcrypt do Laravel
    public function checkPassword($password)
    {
        if (!$this->senha_hash || strlen(trim($this->senha_hash)) === 0) {
            return false;
        }

        // Verificar hash Laravel/bcrypt
        try {
            return Hash::check($password, $this->senha_hash);
        } catch (\Exception $e) {
            return false;
        }
    }

    // Relacionamentos
    public function escalas()
    {
        return $this->hasMany(Escala::class, 'usuario_id');
    }

    public function registrosPresenca()
    {
        return $this->hasMany(RegistroPresenca::class, 'usuario_id');
    }

    public function avisos()
    {
        return $this->hasMany(Aviso::class, 'usuario_id');
    }

    // Scopes
    public function scopeVigilantes($query)
    {
        return $query->where('tipo', 'vigilante');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Métodos auxiliares
    public function isVigilante()
    {
        return $this->tipo === 'vigilante';
    }

    public function isAdmin()
    {
        return $this->tipo === 'admin';
    }

    public function isMorador()
    {
        return $this->tipo === 'morador';
    }
} 