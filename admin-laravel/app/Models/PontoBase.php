<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PontoBase extends Model
{
    use HasFactory;

    protected $table = 'ponto_base';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'posto_trabalho_id',
        'nome',
        'endereco',
        'descricao',
        'latitude',
        'longitude',
        'qr_code',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6'
    ];

    // Relacionamentos
    public function posto(): BelongsTo
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_trabalho_id');
    }

    public function cartaoProgramas(): BelongsToMany
    {
        return $this->belongsToMany(CartaoPrograma::class, 'cartao_programa_pontos', 'ponto_base_id', 'cartao_programa_id')
                    ->withPivot(['ordem', 'tempo_permanencia', 'tempo_deslocamento', 'instrucoes_especificas', 'obrigatorio']);
    }

    public function cartaoProgramaPontos(): HasMany
    {
        return $this->hasMany(CartaoProgramaPonto::class);
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorPosto($query, $postoId)
    {
        return $query->where('posto_trabalho_id', $postoId);
    }

    // Métodos auxiliares
    public function gerarQRCode(): string
    {
        if (empty($this->qr_code)) {
            $this->qr_code = 'QR_' . $this->id . '_' . uniqid();
            $this->save();
        }

        return $this->qr_code;
    }

    public function getLocalizacaoCompleta(): string
    {
        $endereco = $this->endereco;

        if ($this->latitude && $this->longitude) {
            $endereco .= " (GPS: {$this->latitude}, {$this->longitude})";
        }

        return $endereco;
    }

    public function getCartoesProgramaAtivos()
    {
        return $this->cartaoProgramas()->ativos()->get();
    }

    public function isUsadoEmCartaoPrograma(): bool
    {
        return $this->cartaoProgramaPontos()->exists();
    }
}
