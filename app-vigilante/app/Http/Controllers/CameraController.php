<?php

namespace App\Http\Controllers;

use App\Models\CameraCompartilhada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CameraController extends Controller
{
    // Listar câmeras agrupadas por morador
    public function index()
    {
        $camerasAgrupadas = CameraCompartilhada::agruparPorMorador();
        $totalCameras = CameraCompartilhada::getTotalCamerasCompartilhadas();
        $totalMoradores = CameraCompartilhada::getTotalMoradoresCompartilhando();
        $camerasPorTipo = CameraCompartilhada::getCamerasPorTipo();

        return view('cameras.index', compact(
            'camerasAgrupadas',
            'totalCameras',
            'totalMoradores',
            'camerasPorTipo'
        ));
    }

    // Listar câmeras de um morador específico
    public function camerasDoMorador(Request $request)
    {
        $apartamento = $request->input('apartamento');
        $nomeMorador = $request->input('nome_morador');

        if (!$apartamento) {
            return response()->json(['error' => 'Apartamento é obrigatório'], 400);
        }

        $cameras = CameraCompartilhada::camerasDoMorador($apartamento, $nomeMorador);

        return response()->json([
            'cameras' => $cameras->map(function ($camera) {
                return [
                    'id' => $camera->id,
                    'titulo' => $camera->titulo_camera,
                    'descricao' => $camera->descricao,
                    'tipo' => $camera->getTipoFormatado(),
                    'tipo_icon' => $camera->getTipoIcon(),
                    'tipo_class' => $camera->getTipoClass(),
                    'url_imagem' => $camera->url_imagem,
                    'url_thumbnail' => $camera->getUrlThumbnail(),
                    'data_compartilhamento' => $camera->getDataCompartilhamentoFormatada(),
                    'observacoes' => $camera->observacoes
                ];
            }),
            'morador_info' => [
                'nome' => $nomeMorador ?: 'Não informado',
                'apartamento' => $apartamento
            ]
        ]);
    }

    // Visualizar imagem de uma câmera específica
    public function visualizar($id)
    {
        $camera = CameraCompartilhada::ativas()
            ->compartilhadasVigilancia()
            ->findOrFail($id);

        return response()->json([
            'camera' => [
                'id' => $camera->id,
                'titulo' => $camera->titulo_camera,
                'descricao' => $camera->descricao,
                'morador' => $camera->nome_morador,
                'apartamento' => $camera->apartamento,
                'tipo' => $camera->getTipoFormatado(),
                'tipo_icon' => $camera->getTipoIcon(),
                'tipo_class' => $camera->getTipoClass(),
                'url_imagem' => $camera->url_imagem,
                'data_compartilhamento' => $camera->getDataCompartilhamentoFormatada(),
                'observacoes' => $camera->observacoes
            ]
        ]);
    }

    // Buscar câmeras (para filtros)
    public function buscar(Request $request)
    {
        $query = CameraCompartilhada::ativas()->compartilhadasVigilancia();

        if ($request->has('apartamento') && $request->apartamento) {
            $query->where('apartamento', 'like', '%' . $request->apartamento . '%');
        }

        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('nome_morador') && $request->nome_morador) {
            $query->where('nome_morador', 'like', '%' . $request->nome_morador . '%');
        }

        $cameras = $query->orderBy('apartamento')
            ->orderBy('titulo_camera')
            ->get()
            ->groupBy(function ($camera) {
                return $camera->apartamento . ' - ' . $camera->nome_morador;
            });

        return response()->json([
            'cameras_agrupadas' => $cameras->map(function ($cameras, $moradorKey) {
                return [
                    'morador_info' => $moradorKey,
                    'cameras' => $cameras->map(function ($camera) {
                        return [
                            'id' => $camera->id,
                            'titulo' => $camera->titulo_camera,
                            'tipo' => $camera->getTipoFormatado(),
                            'tipo_icon' => $camera->getTipoIcon(),
                            'url_thumbnail' => $camera->getUrlThumbnail()
                        ];
                    })
                ];
            })->values()
        ]);
    }

    // Estatísticas das câmeras
    public function estatisticas()
    {
        $totalCameras = CameraCompartilhada::getTotalCamerasCompartilhadas();
        $totalMoradores = CameraCompartilhada::getTotalMoradoresCompartilhando();
        $camerasPorTipo = CameraCompartilhada::getCamerasPorTipo();

        // Câmeras mais recentes
        $camerasRecentes = CameraCompartilhada::ativas()
            ->compartilhadasVigilancia()
            ->orderBy('data_compartilhamento', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'total_cameras' => $totalCameras,
            'total_moradores' => $totalMoradores,
            'cameras_por_tipo' => $camerasPorTipo,
            'cameras_recentes' => $camerasRecentes->map(function ($camera) {
                return [
                    'titulo' => $camera->titulo_camera,
                    'morador' => $camera->nome_morador,
                    'apartamento' => $camera->apartamento,
                    'tipo' => $camera->getTipoFormatado(),
                    'data' => $camera->getDataCompartilhamentoFormatada()
                ];
            })
        ]);
    }
}
