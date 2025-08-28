<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'tipo',
        'cor',
        'marca',
        'modelo',
        'morador_id'
    ];

    /**
     * Relacionamento com morador
     */
    public function morador(): BelongsTo
    {
        return $this->belongsTo(Morador::class);
    }
}
