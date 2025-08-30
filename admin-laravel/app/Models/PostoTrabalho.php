<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostoTrabalho extends Model
{
    protected $table = 'posto_trabalho';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nome',
        'descricao',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    // Relacionamentos
    public function escalas()
    {
        return $this->hasMany(Escala::class, 'posto_trabalho_id'); // Corrigido de 'posto_id' para 'posto_trabalho_id'
    }

    public function pontosBase()
    {
        return $this->hasMany(PontoBase::class, 'posto_trabalho_id');
    }

    public function cartoesPrograma()
    {
        return $this->hasMany(CartaoPrograma::class, 'posto_trabalho_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Métodos auxiliares
    public function getTotalPontosBase(): int
    {
        return $this->pontosBase()->count();
    }

    public function getTotalCartoesPrograma(): int
    {
        return $this->cartoesPrograma()->count();
    }

    public function getPontosBaseAtivos()
    {
        return $this->pontosBase()->ativos()->get();
    }

    public function getCartoesAtivos()
    {
        return $this->cartoesPrograma()->ativos()->get();
    }

    public function temPontosBase(): bool
    {
        return $this->pontosBase()->exists();
    }

    public function temCartoesPrograma(): bool
    {
        return $this->cartoesPrograma()->exists();
    }
}
