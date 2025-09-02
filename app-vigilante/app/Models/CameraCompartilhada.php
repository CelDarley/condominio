<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CameraCompartilhada extends Model
{
    protected $table = 'cameras_compartilhadas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome_morador',
        'apartamento',
        'titulo_camera',
        'descricao',
        'url_imagem',
        'url_thumbnail',
        'tipo',
        'ativa',
        'compartilhada_vigilancia',
        'data_compartilhamento',
        'observacoes'
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'compartilhada_vigilancia' => 'boolean',
        'data_compartilhamento' => 'datetime',
    ];

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }

    public function scopeCompartilhadasVigilancia($query)
    {
        return $query->where('compartilhada_vigilancia', true);
    }

    public function scopePorApartamento($query, $apartamento)
    {
        return $query->where('apartamento', $apartamento);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Métodos auxiliares
    public function getTipoFormatado()
    {
        $tipos = [
            'entrada' => 'Entrada',
            'varanda' => 'Varanda',
            'garagem' => 'Garagem',
            'area_comum' => 'Área Comum',
            'outros' => 'Outros'
        ];

        return $tipos[$this->tipo] ?? 'Indefinido';
    }

    public function getTipoIcon()
    {
        $icons = [
            'entrada' => 'fas fa-door-open',
            'varanda' => 'fas fa-home',
            'garagem' => 'fas fa-car',
            'area_comum' => 'fas fa-users',
            'outros' => 'fas fa-video'
        ];

        return $icons[$this->tipo] ?? 'fas fa-video';
    }

    public function getTipoClass()
    {
        $classes = [
            'entrada' => 'text-primary',
            'varanda' => 'text-success',
            'garagem' => 'text-warning',
            'area_comum' => 'text-info',
            'outros' => 'text-secondary'
        ];

        return $classes[$this->tipo] ?? 'text-secondary';
    }

    public function getDataCompartilhamentoFormatada()
    {
        return $this->data_compartilhamento->format('d/m/Y H:i');
    }

    public function getUrlThumbnail()
    {
        return $this->url_thumbnail ?: $this->url_imagem;
    }

    public function temDescricao()
    {
        return !empty($this->descricao);
    }

    public function temObservacoes()
    {
        return !empty($this->observacoes);
    }

    // Agrupar câmeras por morador
    public static function agruparPorMorador()
    {
        return self::ativas()
            ->compartilhadasVigilancia()
            ->orderBy('apartamento')
            ->orderBy('titulo_camera')
            ->get()
            ->groupBy(function ($camera) {
                return $camera->apartamento . ' - ' . $camera->nome_morador;
            });
    }

    // Buscar câmeras por morador
    public static function camerasDoMorador($apartamento, $nomeMorador = null)
    {
        $query = self::ativas()
            ->compartilhadasVigilancia()
            ->where('apartamento', $apartamento);

        if ($nomeMorador) {
            $query->where('nome_morador', $nomeMorador);
        }

        return $query->orderBy('titulo_camera')->get();
    }

    // Estatísticas
    public static function getTotalCamerasCompartilhadas()
    {
        return self::ativas()->compartilhadasVigilancia()->count();
    }

    public static function getTotalMoradoresCompartilhando()
    {
        return self::ativas()
            ->compartilhadasVigilancia()
            ->distinct()
            ->count(['apartamento', 'nome_morador']);
    }

    public static function getCamerasPorTipo()
    {
        return self::ativas()
            ->compartilhadasVigilancia()
            ->selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo')
            ->toArray();
    }
}
