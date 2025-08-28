<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartaoProgramaPonto extends Model
{
    use HasFactory;

    protected $table = 'cartao_programa_pontos';

    protected $fillable = [
        'cartao_programa_id',
        'ponto_base_id',
        'ordem',
        'tempo_permanencia',
        'tempo_deslocamento',
        'instrucoes_especificas',
        'obrigatorio'
    ];

    protected $casts = [
        'obrigatorio' => 'boolean'
    ];

    // Relacionamentos
    public function cartaoPrograma(): BelongsTo
    {
        return $this->belongsTo(CartaoPrograma::class);
    }

    public function pontoBase(): BelongsTo
    {
        return $this->belongsTo(PontoBase::class);
    }

    // Scopes
    public function scopeObrigatorios($query)
    {
        return $query->where('obrigatorio', true);
    }

    public function scopePorOrdem($query)
    {
        return $query->orderBy('ordem');
    }

    // Métodos auxiliares
    public function getTempoTotalPonto(): int
    {
        return $this->tempo_permanencia + $this->tempo_deslocamento;
    }

    public function getTempoTotalFormatado(): string
    {
        $total = $this->getTempoTotalPonto();
        return "{$total}min ({$this->tempo_permanencia}min + {$this->tempo_deslocamento}min)";
    }

    public function isUltimoPonto(): bool
    {
        return $this->ordem === $this->cartaoPrograma->cartaoProgramaPontos()->max('ordem');
    }

    public function getPontoAnterior(): ?self
    {
        return $this->cartaoPrograma
                    ->cartaoProgramaPontos()
                    ->where('ordem', '<', $this->ordem)
                    ->orderBy('ordem', 'desc')
                    ->first();
    }

    public function getProximoPonto(): ?self
    {
        return $this->cartaoPrograma
                    ->cartaoProgramaPontos()
                    ->where('ordem', '>', $this->ordem)
                    ->orderBy('ordem')
                    ->first();
    }
}
