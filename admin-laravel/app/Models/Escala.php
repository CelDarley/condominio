<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    protected $table = 'escala';
    protected $primaryKey = 'id';
    public $timestamps = true; // Alterado para true já que agora temos timestamps

    protected $fillable = [
        'usuario_id',
        'posto_trabalho_id', // Corrigido de 'posto_id' para 'posto_trabalho_id'
        'cartao_programa_id', // Novo campo
        'dia_semana',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'dia_semana' => 'integer'
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function postoTrabalho() // Renomeado de 'posto' para ser mais claro
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_trabalho_id');
    }

    public function cartaoPrograma() // Novo relacionamento
    {
        return $this->belongsTo(CartaoPrograma::class, 'cartao_programa_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Métodos auxiliares
    public function getDiaSemanaNome()
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
}
