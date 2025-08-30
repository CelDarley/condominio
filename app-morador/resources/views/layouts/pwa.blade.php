<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'App')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#364659">
    
    <!-- Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/icon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #364659;
            --secondary-color: #566273;
            --accent-color: #F2F2F2;
            --status-bar-height: env(safe-area-inset-top);
            --bottom-bar-height: env(safe-area-inset-bottom);
        }
        
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding-top: var(--status-bar-height);
            padding-bottom: var(--bottom-bar-height);
            overflow-x: hidden;
        }
        
        .pwa-container {
            max-width: 100%;
            margin: 0;
            padding: 20px 15px;
            min-height: calc(100vh - var(--status-bar-height) - var(--bottom-bar-height));
        }
        
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(54, 70, 89, 0.4);
        }
        
        .form-control {
            border: 2px solid var(--accent-color);
            border-radius: 15px;
            padding: 15px;
            font-size: 16px; /* Previne zoom no iOS */
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(54, 70, 89, 0.25);
        }
        
        .input-group-text {
            background-color: var(--accent-color);
            border: 2px solid var(--accent-color);
            border-right: none;
            border-radius: 15px 0 0 15px;
            color: var(--primary-color);
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 15px 15px 0;
        }
        
        .btn-outline-secondary {
            border-color: var(--secondary-color);
            color: var(--secondary-color);
            border-radius: 0 15px 15px 0;
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .alert {
            border-radius: 15px;
            margin-bottom: 20px;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Navegação inferior para PWA */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(54, 70, 89, 0.1);
            padding: 10px 0;
            padding-bottom: calc(10px + var(--bottom-bar-height));
            z-index: 1000;
        }
        
        .bottom-nav .nav-item {
            flex: 1;
            text-align: center;
        }
        
        .bottom-nav .nav-link {
            color: var(--secondary-color);
            font-size: 12px;
            padding: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .bottom-nav .nav-link.active {
            color: var(--primary-color);
        }
        
        .bottom-nav .nav-link i {
            font-size: 20px;
            margin-bottom: 3px;
        }
        
        /* Ajustes para conteúdo quando há navegação inferior */
        .with-bottom-nav {
            padding-bottom: 80px;
        }
        
        /* Estilos específicos para PWA */
        @media (display-mode: standalone) {
            .pwa-only {
                display: block !important;
            }
            .web-only {
                display: none !important;
            }
        }
        
        .pwa-only {
            display: none;
        }
        
        /* Loading spinner */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--accent-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Loading Spinner -->
    <div id="loading" class="loading d-none">
        <div class="spinner"></div>
    </div>

    <!-- Main Content -->
    <div class="pwa-container @yield('container-class')">
        <!-- Alerts -->
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
                <strong>Erro!</strong> Verifique os campos abaixo:
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

    <!-- Bottom Navigation (só aparecer se logado) -->
    @if(Auth::guard('morador')->check())
        <nav class="bottom-nav">
            <div class="d-flex">
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span>Início</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('feed.*') ? 'active' : '' }}" href="{{ route('feed.index') }}">
                        <i class="fas fa-comments"></i>
                        <span>Feed</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link {{ request()->routeIs('alertas.*') ? 'active' : '' }}" href="{{ route('alertas.index') }}">
                        <i class="fas fa-bell"></i>
                        <span>Alertas</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="#" onclick="ativarPanico()">
                        <i class="fas fa-shield-alt"></i>
                        <span>Pânico</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                        <i class="fas fa-user"></i>
                        <span>Perfil</span>
                    </a>
                </div>
            </div>
        </nav>
    @endif

    <!-- Profile Modal -->
    @if(Auth::guard('morador')->check())
        <div class="modal fade" id="profileModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px;">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ Auth::guard('morador')->user()->nome }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Email:</strong> {{ Auth::guard('morador')->user()->email }}</p>
                        <p><strong>Apartamento:</strong> {{ Auth::guard('morador')->user()->apartamento }}</p>
                        @if(Auth::guard('morador')->user()->bloco)
                            <p><strong>Bloco:</strong> {{ Auth::guard('morador')->user()->bloco }}</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('logout') }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // PWA Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }

        // Configuração CSRF para AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Função de loading
        function showLoading() {
            document.getElementById('loading').classList.remove('d-none');
        }

        function hideLoading() {
            document.getElementById('loading').classList.add('d-none');
        }

        // Função de pânico
        function ativarPanico() {
            if (confirm('Tem certeza que deseja ativar o botão de pânico?')) {
                showLoading();
                
                // Aqui você faria a requisição AJAX para ativar o pânico
                fetch('/panico', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    alert('Alerta de pânico ativado! A segurança foi notificada.');
                })
                .catch(error => {
                    hideLoading();
                    alert('Erro ao ativar pânico. Tente novamente.');
                });
            }
        }

        // Prevenir refresh acidental em PWA
        let isStandalone = window.matchMedia('(display-mode: standalone)').matches;
        if (isStandalone) {
            window.addEventListener('beforeunload', function (e) {
                e.preventDefault();
                e.returnValue = '';
            });
        }

        // Auto-hide alerts
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @yield('scripts')
</body>
</html> 