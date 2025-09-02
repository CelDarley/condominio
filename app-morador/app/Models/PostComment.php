<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'usuario_id',
        'comentario',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Métodos auxiliares
    public function getTempoDecorridoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::created(function ($comment) {
            $comment->post->incrementarComentarios();
        });

        static::deleted(function ($comment) {
            $comment->post->decrementarComentarios();
        });
    }
}
