<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS para mapas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .alert-priority-critica { border-left: 5px solid #dc3545; }
        .alert-priority-alta { border-left: 5px solid #fd7e14; }
        .alert-priority-media { border-left: 5px solid #ffc107; }
        .alert-priority-baixa { border-left: 5px solid #28a745; }
        .btn-panico {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            color: white;
            font-weight: bold;
            padding: 15px 30px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
            transition: all 0.3s ease;
        }
        .btn-panico:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.6);
            color: white;
        }
        .vigilante-online {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border-radius: 10px;
            padding: 10px;
            margin: 5px 0;
        }
        .vigilante-offline {
            background: #6c757d;
            color: white;
            border-radius: 10px;
            padding: 10px;
            margin: 5px 0;
        }
        #map {
            height: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-shield-alt me-2"></i>
                {{ config('app.name') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Início
                        </a>
                    </li>
                    @if(session('morador_id'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('alertas.index') }}">
                                <i class="fas fa-exclamation-triangle me-1"></i>Alertas
                            </a>
                        </li>
                    @endif
                </ul>
                
                <ul class="navbar-nav">
                    @if(session('morador_id'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>Minha Conta
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-1"></i>Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Cadastrar
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Erro!</strong> Por favor, verifique os campos abaixo:
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0 text-muted">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Leaflet JS para mapas -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Configuração CSRF para AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Função para atualizar posição dos vigilantes em tempo real
        function atualizarPosicaoVigilantes() {
            $.get('/api/vigilantes/posicao', function(data) {
                if (window.vigilantesMap) {
                    // Limpar marcadores existentes
                    window.vigilantesMap.eachLayer(function(layer) {
                        if (layer instanceof L.Marker) {
                            window.vigilantesMap.removeLayer(layer);
                        }
                    });
                    
                    // Adicionar novos marcadores
                    data.forEach(function(vigilante) {
                        if (vigilante.coordenadas_atual) {
                            const marker = L.marker([
                                vigilante.coordenadas_atual.latitude,
                                vigilante.coordenadas_atual.longitude
                            ]).addTo(window.vigilantesMap);
                            
                            marker.bindPopup(`
                                <strong>${vigilante.nome}</strong><br>
                                <small>Última atualização: ${new Date(vigilante.ultima_atualizacao_localizacao).toLocaleString('pt-BR')}</small>
                            `);
                        }
                    });
                }
            });
        }
        
        // Atualizar a cada 30 segundos
        setInterval(atualizarPosicaoVigilantes, 30000);
        
        // Inicializar quando a página carregar
        $(document).ready(function() {
            // Atualizar posição inicial
            atualizarPosicaoVigilantes();
        });
    </script>
    
    @yield('scripts')
</body>
</html>
