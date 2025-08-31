@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Feed da Comunidade -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>
                    Feed da Comunidade
                </h5>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x text-success mb-3"></i>
                <p class="text-muted mb-3">
                    Conecte-se com outros moradores! Compartilhe fotos, vídeos, áudios e participe das conversas da comunidade.
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('feed.index') }}" class="btn btn-success">
                        <i class="fas fa-comments me-2"></i>
                        Acessar Feed
                    </a>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Compartilhe momentos e se conecte com vizinhos
                    </small>
                </div>
            </div>
        </div>

        <!-- Mapa dos Vigilantes -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Localização dos Vigilantes em Tempo Real
                </h5>
            </div>
            <div class="card-body">
                <div id="map"></div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        A posição dos vigilantes é atualizada automaticamente a cada 30 segundos.
                    </small>
                </div>
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
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-1">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        {{ $alerta->titulo }}
                                    </h6>
                                    <p class="mb-2">{{ $alerta->descricao }}</p>
                                    <div class="d-flex gap-3 mb-2">
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
                                    
                                    <!-- Comentários do Alerta -->
                                    @if($alerta->comentarios->count() > 0)
                                        <div class="mt-3">
                                            <h6 class="text-muted mb-2">
                                                <i class="fas fa-comments me-1"></i>
                                                Comentários ({{ $alerta->comentarios->count() }})
                                            </h6>
                                            @foreach($alerta->comentarios->take(3) as $comentario)
                                                <div class="border-start border-2 ps-3 mb-2">
                                                    <small class="text-muted">
                                                        <strong>{{ $comentario->morador->nome }}</strong> 
                                                        disse há {{ $comentario->created_at ? $comentario->created_at->diffForHumans() : 'data não disponível' }}:
                                                    </small>
                                                    <p class="mb-1 small">{{ $comentario->conteudo }}</p>
                                                </div>
                                            @endforeach
                                            @if($alerta->comentarios->count() > 3)
                                                <small class="text-muted">
                                                    <i class="fas fa-ellipsis-h me-1"></i>
                                                    E mais {{ $alerta->comentarios->count() - 3 }} comentários...
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <!-- Formulário para novo comentário -->
                                    <div class="mt-3">
                                        <form class="comentario-form" data-alerta-id="{{ $alerta->id }}">
                                            <div class="input-group">
                                                <input type="text" class="form-control" 
                                                       placeholder="Adicionar comentário..." 
                                                       name="conteudo" required>
                                                <button type="submit" class="btn btn-outline-primary">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                @if($alerta->localizacao)
                                    <div class="ms-3">
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $alerta->localizacao }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('alertas.index') }}" class="btn btn-outline-warning">
                            <i class="fas fa-list me-2"></i>Ver Todos os Alertas
                        </a>
                    </div>
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
        <!-- Botão de Pânico -->
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


    </div>
</div>

<!-- Modal do Botão de Pânico -->
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
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" 
                                  placeholder="Descreva brevemente a situação..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="localizacao" class="form-label">Localização (opcional)</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao" 
                               placeholder="Ex: Bloco A, Apartamento 101">
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
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar mapa
    window.vigilantesMap = L.map('map').setView([-23.5505, -46.6333], 15); // Coordenadas de São Paulo
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(window.vigilantesMap);
    
    // Capturar localização do usuário para o botão de pânico
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            
            // Centralizar mapa na posição do usuário
            window.vigilantesMap.setView([latitude, longitude], 18);
            
            // Adicionar marcador do usuário
            L.marker([latitude, longitude])
                .addTo(window.vigilantesMap)
                .bindPopup('<strong>Você está aqui</strong>')
                .openPopup();
            
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
                    
                    // Recarregar a página para atualizar as solicitações
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function() {
                alert('Erro ao enviar solicitação de pânico. Tente novamente.');
            }
        });
    });
    
    // Envio de comentários em alertas
    $('.comentario-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const alertaId = form.data('alerta-id');
        const conteudo = form.find('input[name="conteudo"]').val();
        
        $.ajax({
            url: '{{ route("comentarios.store") }}',
            method: 'POST',
            data: {
                conteudo: conteudo,
                tipo: 'alerta',
                alerta_id: alertaId,
                publico: true
            },
            success: function(response) {
                // Limpar campo
                form.find('input[name="conteudo"]').val('');
                
                // Recarregar a página para mostrar o novo comentário
                location.reload();
            },
            error: function() {
                alert('Erro ao enviar comentário. Tente novamente.');
            }
        });
    });
    
    // Envio de comentários gerais
    $('.comentario-geral-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("comentarios.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Limpar campos
                form.find('textarea[name="conteudo"]').val('');
                form.find('select[name="tipo"]').val('');
                
                // Mostrar mensagem de sucesso
                const alert = $(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        Comentário enviado com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                $('.container').prepend(alert);
                
                // Scroll para o topo
                $('html, body').animate({ scrollTop: 0 }, 500);
            },
            error: function() {
                alert('Erro ao enviar comentário. Tente novamente.');
            }
        });
    });
});
</script>
@endsection
