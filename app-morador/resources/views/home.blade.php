@extends('layouts.app')

@section('title', 'Início')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Seção de Boas-vindas -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <h1 class="card-title text-primary">
                    <i class="fas fa-shield-alt me-3"></i>
                    Bem-vindo ao {{ config('app.name') }}
                </h1>
                <p class="card-text lead">
                    Sistema de segurança e comunicação para moradores do condomínio
                </p>
                @if(!session('morador_id'))
                    <div class="mt-3">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Cadastrar
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Alertas Ativos -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alertas Ativos
                </h5>
            </div>
            <div class="card-body">
                @if($alertasAtivos->count() > 0)
                    @foreach($alertasAtivos as $alerta)
                        <div class="alert alert-{{ $alerta->prioridade === 'critica' ? 'danger' : ($alerta->prioridade === 'alta' ? 'warning' : 'info') }} alert-priority-{{ $alerta->prioridade }} mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="alert-heading mb-1">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        {{ $alerta->titulo }}
                                    </h6>
                                    <p class="mb-2">{{ $alerta->descricao }}</p>
                                    <div class="d-flex gap-3">
                                        <span class="badge bg-{{ $alerta->tipo === 'seguranca' ? 'danger' : ($alerta->tipo === 'manutencao' ? 'warning' : 'info') }}">
                                            {{ ucfirst($alerta->tipo) }}
                                        </span>
                                        <span class="badge bg-{{ $alerta->prioridade === 'critica' ? 'danger' : ($alerta->prioridade === 'alta' ? 'warning' : ($alerta->prioridade === 'media' ? 'info' : 'success')) }}">
                                            {{ ucfirst($alerta->prioridade) }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $alerta->created_at ? $alerta->created_at->diffForHumans() : 'Data não disponível' }}
                                        </small>
                                    </div>
                                </div>
                                @if($alerta->localizacao)
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $alerta->localizacao }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    @if(session('morador_id'))
                        <div class="text-center mt-3">
                            <a href="{{ route('alertas.index') }}" class="btn btn-outline-warning">
                                <i class="fas fa-list me-2"></i>Ver Todos os Alertas
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        <p class="mt-3 text-muted">Nenhum alerta ativo no momento.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status dos Vigilantes -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield me-2"></i>
                    Vigilantes Online
                </h5>
            </div>
            <div class="card-body">
                @if($vigilantesOnline->count() > 0)
                    @foreach($vigilantesOnline as $vigilante)
                        <div class="vigilante-online mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-circle text-success me-2"></i>
                                    <strong>{{ $vigilante->nome }}</strong>
                                </div>
                                <small>
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $vigilante->ultima_atualizacao_localizacao ? $vigilante->ultima_atualizacao_localizacao->diffForHumans() : 'N/A' }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-user-slash text-muted" style="font-size: 2rem;"></i>
                        <p class="mt-2 text-muted">Nenhum vigilante online</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Botão de Pânico -->
        @if(session('morador_id'))
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Emergência
                    </h5>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted mb-3">
                        Em caso de emergência, clique no botão abaixo para solicitar ajuda imediata.
                    </p>
                    <button type="button" class="btn btn-panico btn-lg" data-bs-toggle="modal" data-bs-target="#panicoModal">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        BOTÃO DE PÂNICO
                    </button>
                </div>
            </div>
        @endif

        <!-- Informações do Condomínio -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informações
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-phone text-primary me-3"></i>
                    <div>
                        <strong>Portaria</strong><br>
                        <small class="text-muted">(11) 99999-9999</small>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-ambulance text-danger me-3"></i>
                    <div>
                        <strong>Emergência</strong><br>
                        <small class="text-muted">192</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fas fa-fire-extinguisher text-warning me-3"></i>
                    <div>
                        <strong>Bombeiros</strong><br>
                        <small class="text-muted">193</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal do Botão de Pânico -->
@if(session('morador_id'))
<div class="modal fade" id="panicoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Solicitar Ajuda de Emergência
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="panicoForm" action="{{ route('panico.ativar') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Emergência</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Selecione o tipo...</option>
                            <option value="seguranca">Segurança</option>
                            <option value="medica">Médica</option>
                            <option value="incendio">Incêndio</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição (opcional)</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Descreva brevemente a situação..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="localizacao" class="form-label">Localização (opcional)</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao" placeholder="Ex: Bloco A, Apartamento 101">
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Atenção:</strong> Esta solicitação será enviada imediatamente para todos os vigilantes online.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Ativar Pânico
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Capturar localização do usuário para o botão de pânico
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Adicionar campos hidden ao formulário
            $('#panicoForm').append(`
                <input type="hidden" name="latitude" value="${latitude}">
                <input type="hidden" name="longitude" value="${longitude}">
            `);
        });
    }
    
    // Envio do formulário de pânico via AJAX
    $('#panicoForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#panicoModal').modal('hide');
                    
                    // Mostrar mensagem de sucesso
                    const alert = $(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);
                    
                    $('.container').prepend(alert);
                    
                    // Scroll para o topo
                    $('html, body').animate({ scrollTop: 0 }, 500);
                }
            },
            error: function() {
                alert('Erro ao enviar solicitação de pânico. Tente novamente.');
            }
        });
    });
});
</script>
@endsection
