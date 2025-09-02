@extends('layouts.app')

@section('title', 'Câmeras Compartilhadas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-video me-2"></i>
            Câmeras Compartilhadas
        </h4>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-video fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ $totalCameras }}</h3>
                    <small>Câmeras Ativas</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ $totalMoradores }}</h3>
                    <small>Moradores Compartilhando</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chart-pie fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ count($camerasPorTipo) }}</h3>
                    <small>Tipos Diferentes</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="card-title">
                <i class="fas fa-filter me-2"></i>Filtros
            </h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="filtroApartamento" placeholder="Apartamento">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="filtroMorador" placeholder="Nome do Morador">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtroTipo">
                        <option value="">Todos os Tipos</option>
                        <option value="entrada">Entrada</option>
                        <option value="varanda">Varanda</option>
                        <option value="garagem">Garagem</option>
                        <option value="area_comum">Área Comum</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="aplicarFiltros()">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($camerasAgrupadas->count() > 0)
        <!-- Lista de Moradores e suas Câmeras -->
        <div class="row" id="cameras-container">
            @foreach($camerasAgrupadas as $moradorInfo => $cameras)
                @php
                    $parts = explode(' - ', $moradorInfo);
                    $apartamento = $parts[0] ?? '';
                    $nomeMorador = $parts[1] ?? '';
                @endphp
                <div class="col-lg-6 col-xl-4 mb-4 morador-card">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <i class="fas fa-home me-2"></i>
                                        Apto {{ $apartamento }}
                                    </h6>
                                    <small class="text-muted">{{ $nomeMorador }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $cameras->count() }} câmera(s)</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach($cameras as $camera)
                                <div class="col-6">
                                    <div class="camera-thumbnail position-relative">
                                        <img src="{{ $camera->getUrlThumbnail() }}" 
                                             alt="{{ $camera->titulo_camera }}"
                                             class="img-fluid rounded camera-img"
                                             data-camera-id="{{ $camera->id }}"
                                             style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                                             onclick="visualizarCamera({{ $camera->id }})">
                                        <div class="position-absolute top-0 end-0 m-1">
                                            <span class="badge bg-dark bg-opacity-75">
                                                <i class="{{ $camera->getTipoIcon() }} me-1"></i>
                                                {{ $camera->getTipoFormatado() }}
                                            </span>
                                        </div>
                                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-1">
                                            <small class="d-block text-truncate">{{ $camera->titulo_camera }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-outline-primary btn-sm w-100" 
                                    onclick="verTodasCameras('{{ $apartamento }}', '{{ $nomeMorador }}')">
                                <i class="fas fa-expand me-2"></i>Ver Todas as Câmeras
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-video fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma câmera compartilhada</h5>
                <p class="text-muted mb-4">Ainda não há câmeras compartilhadas pelos moradores.</p>
            </div>
        </div>
    @endif
</div>

<!-- Modal para visualização em tela cheia -->
<div class="modal fade" id="cameraModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <div class="text-white">
                    <h5 class="modal-title" id="cameraModalTitle">Visualizando Câmera</h5>
                    <small id="cameraModalInfo" class="text-light opacity-75"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 d-flex align-items-center justify-content-center">
                <img id="cameraModalImage" src="" class="img-fluid" style="max-height: calc(100vh - 120px);">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <div class="text-center text-white" id="cameraModalDetails">
                    <!-- Detalhes da câmera serão inseridos aqui -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para listar todas as câmeras de um morador -->
<div class="modal fade" id="todasCamerasModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="todasCamerasModalTitle">Câmeras do Morador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3" id="todasCamerasContainer">
                    <!-- Câmeras serão carregadas aqui -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Visualizar câmera em tela cheia
async function visualizarCamera(cameraId) {
    try {
        const response = await fetch(`/cameras/visualizar/${cameraId}`);
        const data = await response.json();
        
        if (response.ok) {
            const camera = data.camera;
            
            document.getElementById('cameraModalTitle').textContent = camera.titulo;
            document.getElementById('cameraModalInfo').textContent = 
                `${camera.apartamento} - ${camera.morador} | ${camera.tipo}`;
            document.getElementById('cameraModalImage').src = camera.url_imagem;
            
            const details = `
                <div class="row text-start">
                    <div class="col-md-6">
                        <small><strong>Compartilhado em:</strong> ${camera.data_compartilhamento}</small>
                    </div>
                    <div class="col-md-6">
                        <small><strong>Tipo:</strong> <i class="${camera.tipo_icon} me-1"></i>${camera.tipo}</small>
                    </div>
                    ${camera.descricao ? `<div class="col-12 mt-2"><small><strong>Descrição:</strong> ${camera.descricao}</small></div>` : ''}
                    ${camera.observacoes ? `<div class="col-12 mt-2"><small><strong>Observações:</strong> ${camera.observacoes}</small></div>` : ''}
                </div>
            `;
            document.getElementById('cameraModalDetails').innerHTML = details;
            
            new bootstrap.Modal(document.getElementById('cameraModal')).show();
        } else {
            alert('Erro ao carregar câmera');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao carregar câmera');
    }
}

// Ver todas as câmeras de um morador
async function verTodasCameras(apartamento, nomeMorador) {
    try {
        const response = await fetch(`/cameras/morador?apartamento=${apartamento}&nome_morador=${nomeMorador}`);
        const data = await response.json();
        
        if (response.ok) {
            document.getElementById('todasCamerasModalTitle').textContent = 
                `Câmeras do ${data.morador_info.apartamento} - ${data.morador_info.nome}`;
            
            const container = document.getElementById('todasCamerasContainer');
            container.innerHTML = '';
            
            data.cameras.forEach(camera => {
                const cameraCard = `
                    <div class="col-md-6">
                        <div class="card">
                            <img src="${camera.url_thumbnail}" 
                                 class="card-img-top" 
                                 style="height: 150px; object-fit: cover; cursor: pointer;"
                                 onclick="visualizarCamera(${camera.id})">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1">${camera.titulo}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted ${camera.tipo_class}">
                                        <i class="${camera.tipo_icon} me-1"></i>${camera.tipo}
                                    </small>
                                    <button class="btn btn-outline-primary btn-sm" 
                                            onclick="visualizarCamera(${camera.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                ${camera.descricao ? `<small class="text-muted d-block mt-1">${camera.descricao}</small>` : ''}
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += cameraCard;
            });
            
            new bootstrap.Modal(document.getElementById('todasCamerasModal')).show();
        } else {
            alert('Erro ao carregar câmeras do morador');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao carregar câmeras do morador');
    }
}

// Aplicar filtros
async function aplicarFiltros() {
    const apartamento = document.getElementById('filtroApartamento').value;
    const nomeMorador = document.getElementById('filtroMorador').value;
    const tipo = document.getElementById('filtroTipo').value;
    
    const params = new URLSearchParams();
    if (apartamento) params.append('apartamento', apartamento);
    if (nomeMorador) params.append('nome_morador', nomeMorador);
    if (tipo) params.append('tipo', tipo);
    
    try {
        const response = await fetch(`/cameras/buscar?${params.toString()}`);
        const data = await response.json();
        
        if (response.ok) {
            const container = document.getElementById('cameras-container');
            container.innerHTML = '';
            
            if (data.cameras_agrupadas.length > 0) {
                data.cameras_agrupadas.forEach(grupo => {
                    const parts = grupo.morador_info.split(' - ');
                    const apartamento = parts[0];
                    const nomeMorador = parts[1];
                    
                    const moradorCard = createMoradorCard(apartamento, nomeMorador, grupo.cameras);
                    container.innerHTML += moradorCard;
                });
            } else {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma câmera encontrada</h5>
                                <p class="text-muted">Tente ajustar os filtros de busca.</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao filtrar câmeras');
    }
}

function createMoradorCard(apartamento, nomeMorador, cameras) {
    const camerasHtml = cameras.map(camera => `
        <div class="col-6">
            <div class="camera-thumbnail position-relative">
                <img src="${camera.url_thumbnail}" 
                     alt="${camera.titulo}"
                     class="img-fluid rounded camera-img"
                     style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                     onclick="visualizarCamera(${camera.id})">
                <div class="position-absolute top-0 end-0 m-1">
                    <span class="badge bg-dark bg-opacity-75">
                        <i class="${camera.tipo_icon} me-1"></i>
                        ${camera.tipo}
                    </span>
                </div>
                <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-1">
                    <small class="d-block text-truncate">${camera.titulo}</small>
                </div>
            </div>
        </div>
    `).join('');
    
    return `
        <div class="col-lg-6 col-xl-4 mb-4 morador-card">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">
                                <i class="fas fa-home me-2"></i>
                                Apto ${apartamento}
                            </h6>
                            <small class="text-muted">${nomeMorador}</small>
                        </div>
                        <span class="badge bg-primary">${cameras.length} câmera(s)</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        ${camerasHtml}
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button class="btn btn-outline-primary btn-sm w-100" 
                            onclick="verTodasCameras('${apartamento}', '${nomeMorador}')">
                        <i class="fas fa-expand me-2"></i>Ver Todas as Câmeras
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Limpar filtros
function limparFiltros() {
    document.getElementById('filtroApartamento').value = '';
    document.getElementById('filtroMorador').value = '';
    document.getElementById('filtroTipo').value = '';
    location.reload();
}

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            bootstrap.Modal.getInstance(modal)?.hide();
        });
    }
});
</script>

<style>
.camera-img:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.camera-thumbnail {
    overflow: hidden;
    border-radius: 0.375rem;
}

#cameraModal .modal-content {
    background: rgba(0, 0, 0, 0.95) !important;
}

.modal-fullscreen .modal-body {
    background: black;
}
</style>
@endsection 