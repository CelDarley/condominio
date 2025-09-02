<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $posto->nome }} - SegCond Vigilante</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-dark: #364659;
            --primary-medium: #566273;
            --light-gray: #F2F2F2;
            --white: #ffffff;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
        }
        
        body {
            background: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(45deg, var(--primary-dark) 0%, var(--primary-medium) 100%);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-dark) 0%, var(--primary-medium) 100%);
            border: none;
            border-radius: 8px;
        }
        
        .btn-success {
            background: var(--success);
            border: none;
            border-radius: 8px;
        }
        
        .btn-danger {
            background: var(--danger);
            border: none;
            border-radius: 8px;
        }
        
        .ponto-card {
            border-left: 4px solid var(--primary-medium);
            transition: all 0.3s ease;
        }
        
        .ponto-card.presente {
            border-left-color: var(--success);
            background-color: rgba(40, 167, 69, 0.1);
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-arrow-left me-2"></i>
                {{ $posto->nome }}
            </a>
            
            <div class="navbar-nav ms-auto">
                <button class="btn btn-outline-light btn-sm" onclick="atualizarStatus()">
                    <i class="fas fa-sync-alt me-1"></i>Atualizar
                </button>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container-fluid py-4">
        
        <!-- Informações da Escala -->
        @if($cartaoPrograma)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-id-card me-2"></i>
                    Programa de Trabalho
                </h6>
            </div>
            <div class="card-body">
                <h5>{{ $cartaoPrograma->nome }}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Horário:</strong> {{ $cartaoPrograma->getHorarioInicioFormatado() }} - {{ $cartaoPrograma->getHorarioFimFormatado() }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Duração:</strong> {{ $cartaoPrograma->getDuracaoFormatada() }}</p>
                    </div>
                </div>
                @if($cartaoPrograma->descricao)
                    <p class="text-muted mb-0">{{ $cartaoPrograma->descricao }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Pontos Base -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-map-marked-alt me-2"></i>
                    Pontos de Verificação
                    <span class="badge bg-info ms-2" id="total-pontos">{{ $pontosBase->count() }} verificações</span>
                </h6>
            </div>
            <div class="card-body" id="pontos-container">
                @if($pontosBase->count() > 0)
                    <div class="row">
                        @foreach($pontosBase as $pontoPrograma)
                        <div class="col-lg-6 col-xl-4 mb-3">
                            <div class="card ponto-card" id="ponto-card-{{ $pontoPrograma->ponto_base_id }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">
                                            <span class="badge bg-secondary me-2">#{{ $pontoPrograma->ordem }}</span>
                                            {{ $pontoPrograma->pontoBase->nome }}
                                        </h6>
                                        <span class="status-badge badge" id="status-{{ $pontoPrograma->ponto_base_id }}">
                                            Verificando...
                                        </span>
                                    </div>
                                    
                                    <!-- Horário programado -->
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $pontoPrograma->horario_inicio ? $pontoPrograma->horario_inicio->format('H:i') : '--:--' }} - 
                                                {{ $pontoPrograma->horario_fim ? $pontoPrograma->horario_fim->format('H:i') : '--:--' }}
                                            </span>
                                            <small class="text-muted">{{ $pontoPrograma->tempo_permanencia }}min</small>
                                        </div>
                                    </div>
                                    
                                    @if($pontoPrograma->pontoBase->descricao)
                                        <p class="card-text small text-muted mb-2">{{ $pontoPrograma->pontoBase->descricao }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between text-muted small mb-3">
                                        @if($pontoPrograma->tempo_permanencia)
                                            <span><i class="fas fa-clock me-1"></i>{{ $pontoPrograma->tempo_permanencia }}min</span>
                                        @endif
                                        @if($pontoPrograma->tempo_deslocamento)
                                            <span><i class="fas fa-route me-1"></i>{{ $pontoPrograma->tempo_deslocamento }}min</span>
                                        @endif
                                    </div>
                                    
                                    @if($pontoPrograma->instrucoes_especificas)
                                        <div class="alert alert-info py-2 mb-3">
                                            <small><i class="fas fa-info-circle me-1"></i>{{ $pontoPrograma->instrucoes_especificas }}</small>
                                        </div>
                                    @endif
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-success btn-chegada" 
                                                data-ponto-id="{{ $pontoPrograma->ponto_base_id }}" 
                                                data-ponto-nome="{{ $pontoPrograma->pontoBase->nome }}"
                                                onclick="registrarChegada({{ $pontoPrograma->ponto_base_id }}, '{{ $pontoPrograma->pontoBase->nome }}')">
                                            <i class="fas fa-sign-in-alt me-2"></i>Chegada
                                        </button>
                                        <button class="btn btn-danger btn-saida" 
                                                data-ponto-id="{{ $pontoPrograma->ponto_base_id }}" 
                                                data-ponto-nome="{{ $pontoPrograma->pontoBase->nome }}"
                                                onclick="registrarSaida({{ $pontoPrograma->ponto_base_id }}, '{{ $pontoPrograma->pontoBase->nome }}')"
                                                disabled>
                                            <i class="fas fa-sign-out-alt me-2"></i>Saída
                                        </button>
                                    </div>
                                    
                                    <div class="mt-2 small text-muted" id="ultimo-registro-{{ $pontoPrograma->ponto_base_id }}">
                                        <!-- Último registro será carregado via JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-map fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum ponto definido</h5>
                        <p class="text-muted">Não há pontos de verificação configurados para este posto.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let statusAtual = {};
        
        // Função para registrar chegada
        async function registrarChegada(pontoId, pontoNome) {
            if (!confirm(`Confirmar chegada no ponto "${pontoNome}"?`)) {
                return;
            }
            
            try {
                setLoading(pontoId, true);
                
                // Tentar obter localização
                const posicao = await obterLocalizacao();
                
                const response = await fetch(`/presenca/chegada/${pontoId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        latitude: posicao?.latitude,
                        longitude: posicao?.longitude,
                        observacoes: ''
                    })
                });
                
                // Verificar se houve erro 419 (CSRF)
                if (response.status === 419) {
                    mostrarMensagem('Sessão expirada. Recarregando a página...', 'warning');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    return;
                }
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    mostrarMensagem(data.message, 'success');
                    atualizarStatusPonto(pontoId, true, data.registro);
                } else {
                    mostrarMensagem(data.message, 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                mostrarMensagem('Erro ao registrar chegada', 'error');
            } finally {
                setLoading(pontoId, false);
            }
        }
        
        // Função para registrar saída
        async function registrarSaida(pontoId, pontoNome) {
            if (!confirm(`Confirmar saída do ponto "${pontoNome}"?`)) {
                return;
            }
            
            try {
                setLoading(pontoId, true);
                
                // Tentar obter localização
                const posicao = await obterLocalizacao();
                
                const response = await fetch(`/presenca/saida/${pontoId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        latitude: posicao?.latitude,
                        longitude: posicao?.longitude,
                        observacoes: ''
                    })
                });
                
                // Verificar se houve erro 419 (CSRF)
                if (response.status === 419) {
                    mostrarMensagem('Sessão expirada. Recarregando a página...', 'warning');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    return;
                }
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    mostrarMensagem(data.message, 'success');
                    atualizarStatusPonto(pontoId, false, data.registro);
                } else {
                    mostrarMensagem(data.message, 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                mostrarMensagem('Erro ao registrar saída', 'error');
            } finally {
                setLoading(pontoId, false);
            }
        }
        
        // Atualizar status dos pontos
        async function atualizarStatus() {
            try {
                console.log('🔄 Iniciando atualização de status...');
                
                const response = await fetch('/presenca/status-hoje');
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('📡 Resposta da API:', data);
                
                // Verificar se é uma resposta de erro
                if (data.status === 'error') {
                    mostrarMensagem(data.message, 'error');
                    return;
                }
                
                // Verificar se temos os dados esperados
                if (!data.pontos || !Array.isArray(data.pontos)) {
                    console.warn('⚠️ Dados de pontos não encontrados na resposta:', data);
                    mostrarMensagem('Dados de pontos não encontrados', 'error');
                    return;
                }
                
                console.log('📍 Pontos encontrados na API:', data.pontos.length);
                
                // Debug: Listar todos os cards disponíveis no DOM
                const cardsDisponiveis = document.querySelectorAll('[id^="ponto-card-"]');
                console.log('🎯 Cards disponíveis no DOM:', Array.from(cardsDisponiveis).map(card => card.id));
                
                // Atualizar status de cada ponto
                let atualizacoesSucesso = 0;
                let atualizacoesFalha = 0;
                
                data.pontos.forEach(ponto => {
                    console.log(`🔍 Processando ponto ID ${ponto.id}:`, ponto);
                    
                    const sucesso = atualizarStatusPonto(ponto.id, ponto.presente, ponto.ultimo_registro);
                    if (sucesso) {
                        atualizacoesSucesso++;
                    } else {
                        atualizacoesFalha++;
                    }
                });
                
                console.log(`✅ Status atualizado: ${atualizacoesSucesso} sucessos, ${atualizacoesFalha} falhas`);
                
                if (atualizacoesFalha > 0) {
                    mostrarMensagem(`Alguns pontos não foram encontrados (${atualizacoesFalha} falhas)`, 'warning');
                } else {
                    mostrarMensagem(`Status atualizado: ${atualizacoesSucesso} pontos`, 'success');
                }
                
            } catch (error) {
                console.error('❌ Erro ao atualizar status:', error);
                mostrarMensagem('Erro ao atualizar status dos pontos: ' + error.message, 'error');
            }
        }
        
        // Atualizar status visual de um ponto
        function atualizarStatusPonto(pontoId, presente, ultimoRegistro) {
            try {
                console.log(`🔧 Atualizando ponto ${pontoId}, presente: ${presente}`);
                
                const card = document.getElementById(`ponto-card-${pontoId}`);
                const status = document.getElementById(`status-${pontoId}`);
                const ultimoRegistroDiv = document.getElementById(`ultimo-registro-${pontoId}`);
                
                if (!card) {
                    console.error(`❌ Card não encontrado para ponto ${pontoId}`);
                    console.log('🔍 IDs de cards existentes:', Array.from(document.querySelectorAll('[id^="ponto-card-"]')).map(el => el.id));
                    return false;
                }
                
                const btnChegada = card.querySelector('.btn-chegada');
                const btnSaida = card.querySelector('.btn-saida');
                
                if (!btnChegada || !btnSaida) {
                    console.error(`❌ Botões não encontrados para ponto ${pontoId}`);
                    return false;
                }
                
                // Atualizar classe do card
                if (presente) {
                    card.classList.add('presente');
                    if (status) {
                        status.className = 'status-badge badge bg-success';
                        status.textContent = 'Presente';
                    }
                    btnChegada.disabled = true;
                    btnSaida.disabled = false;
                } else {
                    card.classList.remove('presente');
                    if (status) {
                        status.className = 'status-badge badge bg-secondary';
                        status.textContent = 'Ausente';
                    }
                    btnChegada.disabled = false;
                    btnSaida.disabled = true;
                }
                
                // Mostrar último registro
                if (ultimoRegistroDiv) {
                    if (ultimoRegistro) {
                        ultimoRegistroDiv.innerHTML = `
                            <i class="fas fa-${ultimoRegistro.tipo === 'chegada' ? 'sign-in-alt' : 'sign-out-alt'} me-1"></i>
                            ${ultimoRegistro.tipo === 'chegada' ? 'Chegada' : 'Saída'}: ${ultimoRegistro.data_hora}
                        `;
                    } else {
                        ultimoRegistroDiv.innerHTML = 'Nenhum registro hoje';
                    }
                }
                
                statusAtual[pontoId] = presente;
                console.log(`✅ Status atualizado para ponto ${pontoId}: ${presente ? 'presente' : 'ausente'}`);
                
                return true;
                
            } catch (error) {
                console.error(`❌ Erro ao atualizar status do ponto ${pontoId}:`, error);
                return false;
            }
        }
        
        // Obter localização do usuário
        function obterLocalizacao() {
            return new Promise((resolve) => {
                if (!navigator.geolocation) {
                    resolve(null);
                    return;
                }
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        });
                    },
                    () => {
                        resolve(null);
                    },
                    { timeout: 5000 }
                );
            });
        }
        
        // Definir loading em um ponto
        function setLoading(pontoId, loading) {
            const card = document.getElementById(`ponto-card-${pontoId}`);
            if (loading) {
                card.classList.add('loading');
            } else {
                card.classList.remove('loading');
            }
        }
        
        // Mostrar mensagem
        function mostrarMensagem(mensagem, tipo) {
            // Mapear tipos para classes Bootstrap
            let alertClass = 'success';
            if (tipo === 'error') alertClass = 'danger';
            else if (tipo === 'warning') alertClass = 'warning';
            else if (tipo === 'info') alertClass = 'info';
            
            // Criar div de alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${alertClass} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Remover automaticamente após 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Função para renovar token CSRF
        async function renovarTokenCSRF() {
            try {
                const response = await fetch('/login', {
                    method: 'GET',
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newToken = doc.querySelector('meta[name="csrf-token"]');
                    
                    if (newToken) {
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken.getAttribute('content'));
                        console.log('🔄 Token CSRF renovado');
                    }
                }
            } catch (error) {
                console.error('❌ Erro ao renovar token CSRF:', error);
            }
        }
        
        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            atualizarStatus();
            
            // Atualizar status a cada 30 segundos
            setInterval(atualizarStatus, 30000);
            
            // Renovar token CSRF a cada 10 minutos
            setInterval(renovarTokenCSRF, 600000);
        });
    </script>
</body>
</html> 