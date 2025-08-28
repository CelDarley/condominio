<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'morador_id',
        'marca',
        'modelo',
        'placa',
        'cor'
    ];

    public function morador()
    {
        return $this->belongsTo(Morador::class);
    }
}
