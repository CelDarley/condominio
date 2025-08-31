<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SegCond Vigilante')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="application-name" content="SegCond Vigilante">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SegCond Vigilante">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#364659">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-dark: #364659;
            --primary-medium: #566273;
            --light-gray: #F2F2F2;
            --white: #ffffff;
            --text-dark: #2c3e50;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
            color: var(--text-dark);
            padding-bottom: 80px; /* Espaço para navbar fixa */
        }

        /* Header fixo */
        .header-mobile {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-medium) 100%);
            color: var(--white);
            padding: 1rem;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .main-content {
            margin-top: 80px;
            margin-bottom: 20px;
            padding: 1rem;
        }

        /* Navegação inferior fixa */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: var(--white);
            border-top: 1px solid #e0e0e0;
            padding: 0.5rem 0;
            z-index: 1000;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }

        .nav-item {
            text-align: center;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-item.active {
            color: var(--primary-dark);
        }

        .nav-item i {
            font-size: 1.2rem;
            margin-bottom: 0.2rem;
        }

        .nav-item span {
            font-size: 0.7rem;
            display: block;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .card-header {
            background: linear-gradient(90deg, var(--light-gray) 0%, var(--white) 100%);
            border-radius: 12px 12px 0 0 !important;
            border-bottom: 1px solid #e0e0e0;
        }

        /* Botões */
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-dark) 0%, var(--primary-medium) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
        }

        .btn-success {
            background-color: var(--success);
            border: none;
            border-radius: 8px;
        }

        .btn-warning {
            background-color: var(--warning);
            border: none;
            border-radius: 8px;
        }

        .btn-danger {
            background-color: var(--danger);
            border: none;
            border-radius: 8px;
        }

        .btn-outline-primary {
            color: var(--primary-dark);
            border-color: var(--primary-dark);
            border-radius: 8px;
        }

        /* Status badges */
        .status-pendente {
            background-color: var(--warning);
            color: #000;
        }

        .status-presente {
            background-color: var(--info);
            color: var(--white);
        }

        .status-concluido {
            background-color: var(--success);
            color: var(--white);
        }

        /* Date Carousel */
        .date-carousel-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .carousel-nav-btn {
            background: var(--primary-dark);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .carousel-nav-btn:hover {
            background: var(--primary-medium);
            transform: scale(1.1);
        }

        .date-carousel {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            flex: 1;
            padding: 0.5rem 0;
            scroll-behavior: smooth;
        }

        .date-btn {
            min-width: 65px;
            height: 70px;
            border-radius: 12px;
            border: 2px solid #ddd;
            background: var(--white);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            flex-shrink: 0;
            cursor: pointer;
        }

        .date-btn.active {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: var(--white);
            transform: scale(1.05);
        }

        .date-btn.today {
            border-color: var(--success);
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        }

        .date-btn.today.active {
            background: var(--success);
            border-color: var(--success);
        }

        .date-btn:hover:not(.active) {
            background: var(--light-gray);
            border-color: var(--primary-medium);
            transform: translateY(-2px);
        }

        .today-indicator {
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--success);
            color: var(--white);
            font-size: 0.6rem;
            padding: 0.1rem 0.3rem;
            border-radius: 8px;
            white-space: nowrap;
        }

        /* Day selector - mantido para compatibilidade */
        .day-selector {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 1rem 0;
            margin-bottom: 1rem;
        }

        .day-btn {
            min-width: 60px;
            height: 60px;
            border-radius: 12px;
            border: 2px solid #ddd;
            background: var(--white);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .day-btn.active {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            color: var(--white);
        }

        .day-btn:hover {
            background: var(--primary-medium);
            border-color: var(--primary-medium);
            color: var(--white);
        }

        /* Alertas mobile */
        .alert {
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        /* Loading spinner */
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner-border {
            color: var(--primary-dark);
        }

        /* Responsive utilities */
        @media (max-width: 576px) {
            .main-content {
                padding: 0.5rem;
            }
            
            .card {
                margin-bottom: 0.5rem;
            }
        }

        /* Hide scrollbars but keep functionality */
        .day-selector::-webkit-scrollbar {
            height: 4px;
        }

        .day-selector::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .day-selector::-webkit-scrollbar-thumb {
            background: var(--primary-medium);
            border-radius: 2px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Header Mobile -->
    <div class="header-mobile">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">@yield('page-title', 'SegCond')</h5>
                @if(auth()->check())
                    <small class="opacity-75">Olá, {{ auth()->user()->nome }}</small>
                @endif
            </div>
            <div class="d-flex gap-2">
                @stack('header-actions')
                @if(auth()->check())
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
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

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bottom Navigation -->
    @auth
    <div class="bottom-nav">
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <a href="{{ route('dashboard') }}" class="nav-item d-block {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Início</span>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('presenca.historico') }}" class="nav-item d-block {{ request()->routeIs('presenca.*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>Presença</span>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('avisos.index') }}" class="nav-item d-block {{ request()->routeIs('avisos.*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn"></i>
                        <span>Avisos</span>
                    </a>
                </div>
                <div class="col-3">
                    <a href="#" onclick="showQuickActions()" class="nav-item d-block">
                        <i class="fas fa-plus-circle"></i>
                        <span>Ações</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // CSRF Token para AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        window.axios = {
            defaults: {
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
                }
            }
        };

        // Loading helper
        function showLoading() {
            document.querySelector('.loading')?.style.setProperty('display', 'block');
        }

        function hideLoading() {
            document.querySelector('.loading')?.style.setProperty('display', 'none');
        }

        // Toast notifications
        function showToast(message, type = 'success') {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toast = toastContainer.lastElementChild;
            new bootstrap.Toast(toast).show();
            
            setTimeout(() => toast.remove(), 5000);
        }

        // Quick Actions Modal
        function showQuickActions() {
            const modal = new bootstrap.Modal(document.getElementById('quickActionsModal'));
            modal.show();
        }

        // Funções específicas do dashboard
        function refreshDashboard() {
            window.location.reload();
        }
        
        function showAvisoModal() {
            const modal = new bootstrap.Modal(document.getElementById('avisoModal'));
            modal.show();
        }
        
        function confirmarPanico() {
            const modal = new bootstrap.Modal(document.getElementById('panicoModal'));
            modal.show();
        }
        
        function enviarAviso() {
            // Implementar funcionalidade de envio de aviso
            alert('Funcionalidade de aviso será implementada');
        }
        
        function enviarPanico() {
            // Implementar funcionalidade de pânico
            alert('Funcionalidade de pânico será implementada');
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html> 