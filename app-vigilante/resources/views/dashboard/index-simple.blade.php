<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SegCond Vigilante - Dashboard</title>
    
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
            --text-dark: #2c3e50;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>
                SegCond Vigilante
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="#" onclick="logout()">
                    <i class="fas fa-sign-out-alt me-1"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container-fluid py-4">
        <h1 class="h3 mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            Dashboard
        </h1>

        <!-- Status Card -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-shield text-primary me-2"></i>
                            {{ auth()->user()->nome }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Escala Info -->
        @if(isset($escalaDiaria))
        <div class="row">
            <div class="col-12">
                <div class="card clickable-card" style="cursor: pointer; transition: all 0.3s ease;" 
                     onclick="window.location.href='{{ route('posto.show', $escalaDiaria->posto_trabalho_id) }}';"
                     onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)';"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.1)';">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            Escala de Hoje
                        </h6>
                        <span class="badge bg-success">
                            <i class="fas fa-mouse-pointer me-1"></i>Clique para acessar
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <strong>Posto:</strong><br>
                                <span class="text-primary">{{ $escalaDiaria->postoTrabalho->nome ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Horário:</strong><br>
                                <span class="text-muted">{{ $escalaDiaria->horario_inicio }} - {{ $escalaDiaria->horario_fim }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong><br>
                                @if($escalaDiaria->cartaoPrograma)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Programa Ativo
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Sem programa
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-3 text-end">
                                @if($escalaDiaria->cartaoPrograma)
                                    <div class="btn btn-primary btn-lg">
                                        <i class="fas fa-play me-2"></i>Iniciar Trabalho
                                    </div>
                                @else
                                    <div class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-times me-2"></i>Indisponível
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($escalaDiaria->cartaoPrograma)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        <strong>{{ $escalaDiaria->cartaoPrograma->nome }}</strong><br>
                                        <small>Clique no card para ver os pontos de verificação e registrar sua presença</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                        <h5>Nenhuma escala para hoje</h5>
                        <p class="text-muted">Você não possui escala definida para hoje.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

            <!-- Feed da Comunidade -->
    <div class="row">
        <div class="col-12">
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
                        <a href="http://localhost:8002/feed" class="btn btn-success">
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
        </div>
    </div>

    <!-- Câmeras Compartilhadas -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-video me-2"></i>
                        Ver Câmeras Compartilhadas
                    </h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-camera fa-3x text-info mb-3"></i>
                    <p class="text-muted mb-3">
                        Visualize as câmeras compartilhadas pelos moradores para monitoramento de segurança.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('cameras.index') }}" class="btn btn-info">
                            <i class="fas fa-video me-2"></i>
                            Ver Câmeras
                        </a>
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Monitoramento compartilhado pela comunidade
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="card-title">Ações Rápidas</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('ocorrencias.index') }}" class="btn btn-outline-primary position-relative">
                                <i class="fas fa-file-alt me-2"></i>Registrar Ocorrência
                                @if($ocorrenciasAbertas > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $ocorrenciasAbertas }}
                                        <span class="visually-hidden">ocorrências abertas</span>
                                    </span>
                                @endif
                            </a>
                            <button class="btn btn-outline-info" onclick="alert('Funcionalidade em desenvolvimento')">
                                <i class="fas fa-clipboard-list me-2"></i>Ver Relatórios
                            </button>
                            <button class="btn btn-outline-danger" onclick="alert('Botão de pânico acionado!')">
                                <i class="fas fa-exclamation-triangle me-2"></i>Pânico
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="card-title">Sistema</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-info" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt me-2"></i>Atualizar
                            </button>
                            <button class="btn btn-outline-secondary" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Logout
        function logout() {
            if (confirm('Deseja realmente sair?')) {
                fetch('/auth/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => {
                    window.location.href = '/login';
                });
            }
        }
        
        // Exibir mensagem de sucesso se houver
        @if(session('success'))
            alert('{{ session('success') }}');
        @endif
    </script>
</body>
</html>
