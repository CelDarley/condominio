<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ocorrencia extends Model
{
    protected $table = 'ocorrencias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'posto_trabalho_id',
        'titulo',
        'descricao',
        'tipo',
        'prioridade',
        'status',
        'anexos',
        'data_ocorrencia',
        'observacoes'
    ];

    protected $casts = [
        'anexos' => 'array',
        'data_ocorrencia' => 'datetime',
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
    public function scopeAbertas($query)
    {
        return $query->where('status', 'aberta');
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopePorPrioridade($query, $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    // Métodos auxiliares
    public function getTipoFormatado()
    {
        $tipos = [
            'incidente' => 'Incidente',
            'manutencao' => 'Manutenção',
            'seguranca' => 'Segurança',
            'outros' => 'Outros'
        ];

        return $tipos[$this->tipo] ?? 'Indefinido';
    }

    public function getPrioridadeFormatada()
    {
        $prioridades = [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta',
            'urgente' => 'Urgente'
        ];

        return $prioridades[$this->prioridade] ?? 'Indefinida';
    }

    public function getStatusFormatado()
    {
        $status = [
            'aberta' => 'Aberta',
            'em_andamento' => 'Em Andamento',
            'resolvida' => 'Resolvida',
            'fechada' => 'Fechada'
        ];

        return $status[$this->status] ?? 'Indefinido';
    }

    public function getPrioridadeClass()
    {
        $classes = [
            'baixa' => 'text-success',
            'media' => 'text-warning',
            'alta' => 'text-danger',
            'urgente' => 'text-danger fw-bold'
        ];

        return $classes[$this->prioridade] ?? 'text-muted';
    }

    public function getStatusClass()
    {
        $classes = [
            'aberta' => 'bg-warning',
            'em_andamento' => 'bg-info',
            'resolvida' => 'bg-success',
            'fechada' => 'bg-secondary'
        ];

        return $classes[$this->status] ?? 'bg-light';
    }

    public function getDataFormatada()
    {
        return $this->data_ocorrencia->format('d/m/Y H:i');
    }

    public function temAnexos()
    {
        return !empty($this->anexos);
    }

    public function getQuantidadeAnexos()
    {
        return $this->anexos ? count($this->anexos) : 0;
    }
}
