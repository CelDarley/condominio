<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Morador extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'telefone',
        'endereco',
        'email'
    ];

    /**
     * Relacionamento com veículos
     */
    public function veiculos(): HasMany
    {
        return $this->hasMany(Veiculo::class);
    }
}
