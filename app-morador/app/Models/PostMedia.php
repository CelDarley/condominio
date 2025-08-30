<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'tipo',
        'arquivo_path',
        'arquivo_nome',
        'mime_type',
        'tamanho',
        'metadata',
        'ordem'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relacionamentos
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Métodos auxiliares
    public function getUrlAttribute()
    {
        return Storage::url($this->arquivo_path);
    }

    public function getTamanhoHumanoAttribute()
    {
        $bytes = $this->tamanho;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImagem()
    {
        return $this->tipo === 'imagem';
    }

    public function isVideo()
    {
        return $this->tipo === 'video';
    }

    public function isAudio()
    {
        return $this->tipo === 'audio';
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($media) {
            // Remover arquivo físico quando mídia for deletada
            if (Storage::exists($media->arquivo_path)) {
                Storage::delete($media->arquivo_path);
            }
        });
    }
}
