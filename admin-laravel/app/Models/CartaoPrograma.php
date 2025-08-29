<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartaoPrograma extends Model
{
    use HasFactory;

    protected $table = 'cartao_programas';

    protected $fillable = [
        'nome',
        'descricao',
        'posto_trabalho_id',
        'horario_inicio',
        'horario_fim',
        'tempo_total_estimado',
        'ativo',
        'configuracoes'
    ];

    protected $casts = [
        'horario_inicio' => 'datetime:H:i',
        'horario_fim' => 'datetime:H:i',
        'ativo' => 'boolean',
        'configuracoes' => 'array'
    ];

    // Relacionamentos
    public function postoTrabalho(): BelongsTo
    {
        return $this->belongsTo(PostoTrabalho::class, 'posto_trabalho_id');
    }

    public function pontosBase(): BelongsToMany
    {
        return $this->belongsToMany(PontoBase::class, 'cartao_programa_pontos', 'cartao_programa_id', 'ponto_base_id')
                    ->withPivot(['ordem', 'tempo_permanencia', 'tempo_deslocamento', 'instrucoes_especificas', 'obrigatorio'])
                    ->orderByPivot('ordem');
    }

    public function cartaoProgramaPontos(): HasMany
    {
        return $this->hasMany(CartaoProgramaPonto::class)->orderBy('ordem');
    }

    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class);
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
    public function calcularTempoTotal(): int
    {
        $tempoTotal = $this->cartaoProgramaPontos->sum(function ($programaPonto) {
            return $programaPonto->tempo_permanencia + $programaPonto->tempo_deslocamento;
        });

        // Atualizar na base de dados
        $this->update(['tempo_total_estimado' => $tempoTotal]);

        return $tempoTotal;
    }

    public function getTempoTotalFormatado(): string
    {
        $minutos = $this->tempo_total_estimado;
        $horas = floor($minutos / 60);
        $mins = $minutos % 60;

        return $horas > 0 ? "{$horas}h {$mins}min" : "{$mins}min";
    }

    public function getTotalPontos(): int
    {
        return $this->cartaoProgramaPontos->count();
    }

    public function getPontosAtivos(): int
    {
        return $this->cartaoProgramaPontos->where('obrigatorio', true)->count();
    }

    public function getSequenciaCompleta(): array
    {
        return $this->cartaoProgramaPontos
                    ->load('pontoBase')
                    ->map(function ($programaPonto) {
                        return [
                            'ordem' => $programaPonto->ordem,
                            'ponto' => $programaPonto->pontoBase,
                            'tempo_permanencia' => $programaPonto->tempo_permanencia,
                            'tempo_deslocamento' => $programaPonto->tempo_deslocamento,
                            'instrucoes_especificas' => $programaPonto->instrucoes_especificas,
                            'obrigatorio' => $programaPonto->obrigatorio
                        ];
                    })->toArray();
    }

    // Método para duplicar cartão programa
    public function duplicar(string $novoNome): self
    {
        $novoCartao = $this->replicate(['nome']);
        $novoCartao->nome = $novoNome;
        $novoCartao->save();

        // Copiar pontos base
        foreach ($this->cartaoProgramaPontos as $programaPonto) {
            $novoCartao->cartaoProgramaPontos()->create([
                'ponto_base_id' => $programaPonto->ponto_base_id,
                'ordem' => $programaPonto->ordem,
                'tempo_permanencia' => $programaPonto->tempo_permanencia,
                'tempo_deslocamento' => $programaPonto->tempo_deslocamento,
                'instrucoes_especificas' => $programaPonto->instrucoes_especificas,
                'obrigatorio' => $programaPonto->obrigatorio
            ]);
        }

        $novoCartao->calcularTempoTotal();

        return $novoCartao;
    }

    /**
     * Retorna o horário de início formatado
     */
    public function getHorarioInicioFormatado(): string
    {
        return $this->horario_inicio ? $this->horario_inicio->format('H:i') : '--:--';
    }

    /**
     * Retorna o horário de fim formatado
     */
    public function getHorarioFimFormatado(): string
    {
        return $this->horario_fim ? $this->horario_fim->format('H:i') : '--:--';
    }

    /**
     * Calcula a duração do turno em minutos considerando turnos noturnos
     */
    public function getDuracaoTurno(): int
    {
        if (!$this->horario_inicio || !$this->horario_fim) {
            return 0;
        }

        $inicio = $this->horario_inicio;
        $fim = $this->horario_fim;

        // Se o horário de fim é menor que o início, é um turno noturno
        if ($fim <= $inicio) {
            // Adiciona 24 horas (1440 minutos) ao horário de fim
            $inicioMinutos = ($inicio->hour * 60) + $inicio->minute;
            $fimMinutos = (($fim->hour + 24) * 60) + $fim->minute;
            return $fimMinutos - $inicioMinutos;
        }

        // Turno normal (mesmo dia)
        $inicioMinutos = ($inicio->hour * 60) + $inicio->minute;
        $fimMinutos = ($fim->hour * 60) + $fim->minute;
        return $fimMinutos - $inicioMinutos;
    }

    /**
     * Retorna uma descrição formatada do turno
     */
    public function getDescricaoTurno(): string
    {
        $inicio = $this->getHorarioInicioFormatado();
        $fim = $this->getHorarioFimFormatado();
        $duracao = $this->getDuracaoTurno();
        
        $horas = floor($duracao / 60);
        $minutos = $duracao % 60;
        
        $duracaoFormatada = $horas > 0 ? "{$horas}h" : "";
        if ($minutos > 0) {
            $duracaoFormatada .= $horas > 0 ? " {$minutos}min" : "{$minutos}min";
        }
        
        // Detectar se é turno noturno
        $turnoNoturno = $this->horario_fim <= $this->horario_inicio;
        $descricao = "{$inicio} às {$fim}";
        
        if ($turnoNoturno) {
            $descricao .= " (próximo dia)";
        }
        
        if ($duracaoFormatada) {
            $descricao .= " - {$duracaoFormatada}";
        }
        
        return $descricao;
    }
}
