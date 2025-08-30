<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'conteudo',
        'tipo',
        'ativo',
        'likes',
        'comentarios_count',
        'metadata'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function comentarios()
    {
        return $this->hasMany(PostComment::class)->where('ativo', true)->orderBy('created_at', 'desc');
    }

    public function medias()
    {
        return $this->hasMany(PostMedia::class)->orderBy('ordem');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeRecentes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeComMidia($query)
    {
        return $query->whereIn('tipo', ['imagem', 'video', 'audio']);
    }

    // Métodos auxiliares
    public function incrementarLikes()
    {
        $this->increment('likes');
    }

    public function incrementarComentarios()
    {
        $this->increment('comentarios_count');
    }

    public function decrementarComentarios()
    {
        $this->decrement('comentarios_count');
    }

    public function getTempoDecorridoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getTemMidiaAttribute()
    {
        return $this->medias()->count() > 0;
    }

    public function getPrimeiraMidiaAttribute()
    {
        return $this->medias()->first();
    }
}
