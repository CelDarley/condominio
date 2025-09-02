@extends('layouts.app')

@section('title', 'Dashboard - SegCond Vigilante')
@section('page-title', 'Dashboard')

@push('header-actions')
<button class="btn btn-outline-light btn-sm" onclick="refreshDashboard()">
    <i class="fas fa-sync-alt"></i>
</button>
@endpush

@section('content')
<!-- Carrossel de Datas -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-calendar-alt me-2"></i>
            Navegue pelas datas
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="date-carousel-container">
            <button class="carousel-nav-btn" id="prev-btn" onclick="navigateDate(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="date-carousel" id="date-carousel">
                @foreach($diasCarrossel as $dia)
                    <button class="date-btn {{ $dia['e_selecionado'] ? 'active' : '' }} {{ $dia['e_hoje'] ? 'today' : '' }} {{ $dia['tem_ajuste'] ? 'with-adjustment' : '' }}" 
                            data-date="{{ $dia['data'] }}"
                            onclick="loadPostosPorData('{{ $dia['data'] }}')">
                        <div class="fw-bold">{{ $dia['nome'] }}</div>
                        <small>{{ $dia['dia'] }}</small>
                        @if($dia['e_hoje'])
                            <div class="today-indicator">Hoje</div>
                        @endif
                        @if($dia['tem_ajuste'])
                            <div class="adjustment-indicator">Ajuste</div>
                        @endif
                    </button>
                @endforeach
            </div>
            
            <button class="carousel-nav-btn" id="next-btn" onclick="navigateDate(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Escala do Dia -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-briefcase me-2"></i>
            <span id="posts-title">Escala para {{ $dataBase->format('d/m/Y') }}</span>
        </h6>
        <small class="text-muted" id="current-date">{{ $dataBase->format('l, d \d\e F \d\e Y') }}</small>
    </div>
    <div class="card-body" id="posts-container">
        @if($postos->isNotEmpty())
            @foreach($postos as $posto)
                <div class="posto-card mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                {{ $posto->nome }}
                            </h6>
                            @if($posto->descricao)
                                <p class="text-muted small mb-2">{{ $posto->descricao }}</p>
                            @endif
                        </div>
                        <span class="badge bg-success">Ativo</span>
                    </div>

                    @if($cartaoPrograma)
                        <div class="cartao-programa-info bg-light p-3 rounded mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-id-card text-info me-2"></i>
                                <strong>{{ $cartaoPrograma->nome }}</strong>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span>{{ $cartaoPrograma->getHorarioInicioFormatado() }} - {{ $cartaoPrograma->getHorarioFimFormatado() }}</span>
                            </div>
                            @if($cartaoPrograma->descricao)
                                <p class="small text-muted mb-0">{{ $cartaoPrograma->descricao }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="d-grid">
                        <a href="{{ route('posto.show', $posto->id) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-play me-2"></i>
                            Iniciar Trabalho
                        </a>
                        <small class="text-muted text-center mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Clique para ver pontos de controle e registrar presença
                        </small>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-4">
                <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">Nenhuma escala para este dia</p>
                <small class="text-muted">Selecione outro dia ou entre em contato com o administrador</small>
            </div>
        @endif
    </div>
</div>

<!-- Ações Rápidas -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-bolt me-2"></i>
            Ações Rápidas
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-6">
                <button class="btn btn-warning w-100" onclick="showAvisoModal()">
                    <i class="fas fa-bullhorn"></i>
                    <br><small>Enviar Aviso</small>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-danger w-100" onclick="confirmarPanico()">
                    <i class="fas fa-exclamation-triangle"></i>
                    <br><small>Botão Pânico</small>
                </button>
            </div>
            <div class="col-6">
                <a href="{{ route('presenca.historico') }}" class="btn btn-info w-100">
                    <i class="fas fa-history"></i>
                    <br><small>Histórico</small>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('avisos.index') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-list"></i>
                    <br><small>Meus Avisos</small>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Loading -->
<div class="loading">
    <div class="spinner-border" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p class="mt-2">Carregando informações...</p>
</div>

<!-- Modal Aviso Rápido -->
<div class="modal fade" id="avisoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bullhorn me-2"></i>
                    Enviar Aviso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="avisoForm">
                    @csrf
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required maxlength="200">
                    </div>
                    <div class="mb-3">
                        <label for="mensagem" class="form-label">Mensagem</label>
                        <textarea class="form-control" id="mensagem" name="mensagem" rows="3" required maxlength="1000"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="enviarAviso()">
                    <i class="fas fa-paper-plane me-2"></i>
                    Enviar Aviso
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pânico -->
<div class="modal fade" id="panicoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alerta de Pânico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-warning me-2"></i>
                    <strong>Atenção!</strong> Esta ação enviará um alerta de emergência para todos os administradores.
                </div>
                <div class="mb-3">
                    <label for="localizacao" class="form-label">Localização (opcional)</label>
                    <input type="text" class="form-control" id="localizacao" placeholder="Ex: Portaria principal, Bloco A...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="enviarPanico()">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Pânico
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.date-btn.with-adjustment {
    border: 2px solid #fd7e14 !important;
    background-color: #fff3cd !important;
}

.adjustment-indicator {
    position: absolute;
    top: 2px;
    right: 2px;
    background-color: #fd7e14;
    color: white;
    font-size: 0.6rem;
    padding: 1px 4px;
    border-radius: 6px;
    line-height: 1;
}

.today-indicator {
    position: absolute;
    bottom: 2px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #198754;
    color: white;
    font-size: 0.6rem;
    padding: 1px 4px;
    border-radius: 6px;
    line-height: 1;
}

.date-btn {
    position: relative;
}

.escala-ajustada-indicator {
    background-color: #fff3cd;
    border-left: 4px solid #fd7e14;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    border-radius: 0.25rem;
}

.escala-ajustada-indicator .badge {
    background-color: #fd7e14 !important;
}
</style>
@endpush

@push('scripts')
<script>
let currentDate = '{{ $dataBase->format("Y-m-d") }}';

function loadPostosPorData(data) {
    showLoading();
    
    // Update active date button
    document.querySelectorAll('.date-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-date="${data}"]`).classList.add('active');
    
    fetch(`/api/postos-por-data/${data}`)
        .then(response => response.json())
        .then(responseData => {
            updatePostsContainer(responseData, data);
            currentDate = data;
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao carregar informações', 'danger');
        })
        .finally(() => {
            hideLoading();
        });
}

function navigateDate(direction) {
    const date = new Date(currentDate);
    date.setDate(date.getDate() + direction);
    const newDate = date.toISOString().split('T')[0];
    
    // Redirecionar para a nova data
    window.location.href = `/dashboard?data=${newDate}`;
}

function updatePostsContainer(data, selectedDate) {
    const container = document.getElementById('posts-container');
    const title = document.getElementById('posts-title');
    
    const formattedDate = new Date(selectedDate).toLocaleDateString('pt-BR');
    title.textContent = `Escala para ${formattedDate}`;
    
    if (data.posto) {
        const posto = data.posto;
        const cartaoPrograma = data.cartao_programa;
        const temAjuste = data.tem_ajuste || false;
        const infoAjuste = data.info_ajuste;
        
        let cartaoHtml = '';
        if (cartaoPrograma) {
            cartaoHtml = `
                <div class="cartao-programa-info bg-light p-3 rounded mb-3 ${temAjuste ? 'escala-ajustada-indicator' : ''}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-id-card text-info me-2"></i>
                            <strong>${cartaoPrograma.nome}</strong>
                        </div>
                        ${temAjuste ? '<span class="badge">Ajustado</span>' : ''}
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-clock text-warning me-2"></i>
                        <span>${cartaoPrograma.horario_inicio} - ${cartaoPrograma.horario_fim}</span>
                    </div>
                    ${cartaoPrograma.descricao ? `<p class="small text-muted mb-0">${cartaoPrograma.descricao}</p>` : ''}
                    ${temAjuste && infoAjuste ? `
                        <div class="alert alert-warning mt-2 mb-0 py-2">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Escala ajustada:</strong> ${infoAjuste.motivo || 'Substituição temporária'}
                                ${infoAjuste.usuario_original ? `<br>Originalmente: ${infoAjuste.usuario_original}` : ''}
                            </small>
                        </div>
                    ` : ''}
                </div>
            `;
        }
        
        container.innerHTML = `
            <div class="posto-card mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            ${posto.nome}
                        </h6>
                        ${posto.descricao ? `<p class="text-muted small mb-2">${posto.descricao}</p>` : ''}
                    </div>
                    <span class="badge bg-success">Ativo</span>
                </div>
                ${cartaoHtml}
                <div class="d-grid">
                    <a href="/posto/${posto.id}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>
                        Ver Posto de Trabalho
                    </a>
                </div>
            </div>
        `;
    } else {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">Nenhuma escala para este dia</p>
                <small class="text-muted">Selecione outro dia ou entre em contato com o administrador</small>
            </div>
        `;
    }
}