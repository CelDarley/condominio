@extends('layouts.app')

@section('title', $posto->nome . ' - SegCond Vigilante')
@section('page-title', $posto->nome)

@push('header-actions')
<button class="btn btn-outline-light btn-sm" onclick="refreshStatus()">
    <i class="fas fa-sync-alt"></i>
</button>
<a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
    <i class="fas fa-arrow-left"></i>
</a>
@endpush

@section('content')
<!-- Informações do Posto -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-building me-2"></i>
            Informações do Posto
        </h6>
    </div>
    <div class="card-body">
        <h5 class="mb-2">{{ $posto->nome }}</h5>
        @if($posto->descricao)
            <p class="text-muted mb-3">{{ $posto->descricao }}</p>
        @endif
        
        @if($cartaoPrograma)
            <div class="cartao-programa-info bg-light p-3 rounded">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-id-card text-info me-2"></i>
                    <strong>{{ $cartaoPrograma->nome }}</strong>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-clock text-warning me-2"></i>
                    <span>{{ $cartaoPrograma->getHorarioInicioFormatado() }} - {{ $cartaoPrograma->getHorarioFimFormatado() }}</span>
                </div>
                @if($cartaoPrograma->getDuracaoFormatada())
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-hourglass-half text-secondary me-2"></i>
                        <span>Duração: {{ $cartaoPrograma->getDuracaoFormatada() }}</span>
                    </div>
                @endif
                @if($cartaoPrograma->descricao)
                    <p class="small text-muted mb-0">{{ $cartaoPrograma->descricao }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Pontos de Controle -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-map-marker-alt me-2"></i>
            Pontos de Controle
        </h6>
        <span class="badge bg-primary">{{ $pontosBase->count() }} pontos</span>
    </div>
    <div class="card-body">
        @if($pontosBase->isNotEmpty())
            @foreach($pontosBase as $ponto)
                @php
                    $status = $statusPontos[$ponto->id] ?? ['status' => 'pendente'];
                    $statusClass = match($status['status']) {
                        'concluido' => 'success',
                        'presente' => 'info', 
                        default => 'warning'
                    };
                    $statusIcon = match($status['status']) {
                        'concluido' => 'fa-check-circle',
                        'presente' => 'fa-clock',
                        default => 'fa-circle'
                    };
                    $statusText = match($status['status']) {
                        'concluido' => 'Concluído',
                        'presente' => 'Presente',
                        default => 'Pendente'
                    };
                @endphp
                
                <div class="ponto-card mb-3 p-3 border rounded" data-ponto-id="{{ $ponto->id }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                @if(isset($ponto->ordem))
                                    <span class="badge bg-secondary me-2">{{ $ponto->ordem }}</span>
                                @endif
                                {{ $ponto->nome }}
                            </h6>
                            <p class="text-muted small mb-1">{{ $ponto->getEnderecoCompleto() }}</p>
                            @if($ponto->descricao)
                                <p class="text-muted small mb-2">{{ $ponto->descricao }}</p>
                            @endif
                        </div>
                        <span class="badge bg-{{ $statusClass }} status-badge">
                            <i class="fas {{ $statusIcon }} me-1"></i>
                            {{ $statusText }}
                        </span>
                    </div>

                    <!-- Informações do Cartão Programa -->
                    @if(isset($ponto->tempo_permanencia) || isset($ponto->instrucoes_especificas))
                        <div class="programa-info bg-light p-2 rounded mb-2">
                            @if(isset($ponto->tempo_permanencia))
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <small>Permanência: {{ $ponto->tempo_permanencia }}min</small>
                                    @if(isset($ponto->tempo_deslocamento))
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-walking text-info me-1"></i>
                                        <small>Deslocamento: {{ $ponto->tempo_deslocamento }}min</small>
                                    @endif
                                </div>
                            @endif
                            @if(isset($ponto->instrucoes_especificas) && $ponto->instrucoes_especificas)
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                    <small class="text-muted">{{ $ponto->instrucoes_especificas }}</small>
                                </div>
                            @endif
                            @if(isset($ponto->obrigatorio) && $ponto->obrigatorio)
                                <div class="mt-1">
                                    <span class="badge bg-danger">Obrigatório</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Status de Presença -->
                    <div class="status-info mb-3" style="display: none;">
                        <div class="d-flex justify-content-between text-muted small">
                            <span class="chegada-info"></span>
                            <span class="saida-info"></span>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-registrar" onclick="registrarPresenca({{ $ponto->id }})">
                            <i class="fas fa-check me-2"></i>
                            <span class="btn-text">Registrar Presença</span>
                        </button>
                        
                        @if($ponto->temCoordenadas())
                            <button class="btn btn-outline-primary btn-sm" onclick="abrirMapa({{ $ponto->latitude }}, {{ $ponto->longitude }})">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Ver Localização
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-4">
                <i class="fas fa-map-marker-alt text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">Nenhum ponto de controle cadastrado</p>
                <small class="text-muted">Entre em contato com o administrador</small>
            </div>
        @endif
    </div>
</div>

<!-- Resumo do Progresso -->
@if($pontosBase->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-chart-pie me-2"></i>
            Progresso de Hoje
        </h6>
    </div>
    <div class="card-body">
        <div class="progress mb-3" style="height: 20px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="progress-bar">
                <span id="progress-text">0%</span>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-4">
                <div class="border-end">
                    <div class="fw-bold text-warning" id="pendentes-count">0</div>
                    <small class="text-muted">Pendentes</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border-end">
                    <div class="fw-bold text-info" id="presentes-count">0</div>
                    <small class="text-muted">Presente</small>
                </div>
            </div>
            <div class="col-4">
                <div class="fw-bold text-success" id="concluidos-count">0</div>
                <small class="text-muted">Concluídos</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Loading -->
<div class="loading">
    <div class="spinner-border" role="status">
        <span class="visually-hidden">Atualizando...</span>
    </div>
    <p class="mt-2">Atualizando status...</p>
</div>
@endsection

@push('scripts')
<script>
let isRegistering = false;

// Carregar status inicial
document.addEventListener('DOMContentLoaded', function() {
    refreshStatus();
    
    // Auto-refresh a cada 2 minutos
    setInterval(refreshStatus, 120000);
});

function refreshStatus() {
    showLoading();
    
    fetch(`/posto/{{ $posto->id }}/status-pontos`)
        .then(response => response.json())
        .then(data => {
            updatePontosStatus(data);
            updateProgress();
        })
        .catch(error => {
            console.error('Erro ao atualizar status:', error);
            showToast('Erro ao atualizar status', 'warning');
        })
        .finally(() => {
            hideLoading();
        });
}

function updatePontosStatus(statusData) {
    statusData.forEach(status => {
        const pontoCard = document.querySelector(`[data-ponto-id="${status.ponto_id}"]`);
        if (!pontoCard) return;
        
        const statusBadge = pontoCard.querySelector('.status-badge');
        const statusInfo = pontoCard.querySelector('.status-info');
        const btnRegistrar = pontoCard.querySelector('.btn-registrar');
        const btnText = pontoCard.querySelector('.btn-text');
        
        // Update status badge
        statusBadge.className = 'badge status-badge';
        let statusClass, statusIcon, statusText, btnLabel;
        
        switch(status.status) {
            case 'concluido':
                statusClass = 'bg-success';
                statusIcon = 'fa-check-circle';
                statusText = 'Concluído';
                btnLabel = 'Registrar Presença';
                btnRegistrar.className = 'btn btn-success btn-registrar';
                btnRegistrar.disabled = true;
                break;
            case 'presente':
                statusClass = 'bg-info';
                statusIcon = 'fa-clock';
                statusText = 'Presente';
                btnLabel = 'Registrar Saída';
                btnRegistrar.className = 'btn btn-warning btn-registrar';
                btnRegistrar.disabled = false;
                break;
            default:
                statusClass = 'bg-warning';
                statusIcon = 'fa-circle';
                statusText = 'Pendente';
                btnLabel = 'Registrar Chegada';
                btnRegistrar.className = 'btn btn-success btn-registrar';
                btnRegistrar.disabled = false;
        }
        
        statusBadge.classList.add(statusClass);
        statusBadge.innerHTML = `<i class="fas ${statusIcon} me-1"></i>${statusText}`;
        btnText.textContent = btnLabel;
        
        // Update timestamps
        if (status.timestamp_chegada || status.timestamp_saida) {
            statusInfo.style.display = 'block';
            const chegadaInfo = statusInfo.querySelector('.chegada-info');
            const saidaInfo = statusInfo.querySelector('.saida-info');
            
            chegadaInfo.innerHTML = status.timestamp_chegada 
                ? `<i class="fas fa-sign-in-alt me-1"></i>Chegada: ${status.timestamp_chegada}`
                : '';
            saidaInfo.innerHTML = status.timestamp_saida 
                ? `<i class="fas fa-sign-out-alt me-1"></i>Saída: ${status.timestamp_saida}`
                : '';
        } else {
            statusInfo.style.display = 'none';
        }
    });
}

function updateProgress() {
    const pontoCards = document.querySelectorAll('.ponto-card');
    let pendentes = 0, presentes = 0, concluidos = 0;
    
    pontoCards.forEach(card => {
        const badge = card.querySelector('.status-badge');
        if (badge.textContent.includes('Pendente')) pendentes++;
        else if (badge.textContent.includes('Presente')) presentes++;
        else if (badge.textContent.includes('Concluído')) concluidos++;
    });
    
    const total = pontoCards.length;
    const progressPercent = total > 0 ? Math.round((concluidos / total) * 100) : 0;
    
    document.getElementById('pendentes-count').textContent = pendentes;
    document.getElementById('presentes-count').textContent = presentes;
    document.getElementById('concluidos-count').textContent = concluidos;
    document.getElementById('progress-bar').style.width = progressPercent + '%';
    document.getElementById('progress-text').textContent = progressPercent + '%';
}

function registrarPresenca(pontoId) {
    if (isRegistering) return;
    
    isRegistering = true;
    const btn = document.querySelector(`[data-ponto-id="${pontoId}"] .btn-registrar`);
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registrando...';
    btn.disabled = true;
    
    fetch(`/presenca/registrar/${pontoId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'chegada' || data.status === 'saida') {
            showToast(data.message, 'success');
            refreshStatus();
        } else {
            showToast(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao registrar presença', 'danger');
    })
    .finally(() => {
        isRegistering = false;
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function abrirMapa(latitude, longitude) {
    const url = `https://www.google.com/maps?q=${latitude},${longitude}`;
    window.open(url, '_blank');
}
</script>
@endpush 