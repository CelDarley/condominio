<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    protected $table = 'escala';
    protected $primaryKey = 'id';
    public $timestamps = false;

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
        'ativo',
        'observacoes'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date', 
        'horario_inicio' => 'datetime:H:i',
        'horario_fim' => 'datetime:H:i',
        'dias_semana' => 'array',
        'ativo' => 'boolean',
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



    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorDia($query, $diaSemana)
    {
        return $query->whereJsonContains('dias_semana', $diaSemana);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // Métodos auxiliares
    public function getDiasSemanaTexto()
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

        $nomesDias = [];
        foreach ($this->dias_semana ?? [] as $dia) {
            $nomesDias[] = $dias[$dia] ?? 'Indefinido';
        }

        return implode(', ', $nomesDias);
    }

    public function getDiasSemanaAbrev()
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

        $nomesDias = [];
        foreach ($this->dias_semana ?? [] as $dia) {
            $nomesDias[] = $dias[$dia] ?? '?';
        }

        return implode(', ', $nomesDias);
    }

    public function trabalhaNoDia($diaSemana)
    {
        return in_array($diaSemana, $this->dias_semana ?? []);
    }
} 