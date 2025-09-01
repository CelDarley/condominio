<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    protected $table = 'escala';
    protected $primaryKey = 'id';
    public $timestamps = true; // Alterado para true já que agora temos timestamps

    protected $fillable = [
        'nome',
        'descricao',
        'posto_trabalho_id',
        'usuario_id',
        'data_inicio',
        'data_fim',
        'horario_inicio',
        'horario_fim',
        'dias_semana',
        'cartao_programa_id',
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'horario_inicio' => 'datetime:H:i',
        'horario_fim' => 'datetime:H:i',
        'dias_semana' => 'array'
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
    public function getDiasSemanaNomes()
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

        if (is_array($this->dias_semana)) {
            return collect($this->dias_semana)->map(function($dia) use ($dias) {
                return $dias[$dia] ?? 'Indefinido';
            })->implode(', ');
        }

        return 'Indefinido';
    }
}
