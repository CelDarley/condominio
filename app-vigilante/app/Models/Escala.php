<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    protected $table = 'escala';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'posto_trabalho_id',
        'cartao_programa_id',
        'dia_semana',
        'ativo',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function postoTrabalho()
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_trabalho_id');
    }

    public function cartaoPrograma()
    {
        return $this->belongsTo(CartaoPrograma::class, 'cartao_programa_id');
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorDia($query, $diaSemana)
    {
        return $query->where('dia_semana', $diaSemana);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // Métodos auxiliares
    public function getNomeDiaSemana()
    {
        $dias = [
            0 => 'Segunda-feira',
            1 => 'Terça-feira',
            2 => 'Quarta-feira',
            3 => 'Quinta-feira',
            4 => 'Sexta-feira',
            5 => 'Sábado',
            6 => 'Domingo'
        ];

        return $dias[$this->dia_semana] ?? 'Indefinido';
    }

    public function getNomeDiaSemanaAbrev()
    {
        $dias = [
            0 => 'Seg',
            1 => 'Ter',
            2 => 'Qua',
            3 => 'Qui',
            4 => 'Sex',
            5 => 'Sáb',
            6 => 'Dom'
        ];

        return $dias[$this->dia_semana] ?? '?';
    }

    public function temCartaoPrograma()
    {
        return !is_null($this->cartao_programa_id);
    }
} 