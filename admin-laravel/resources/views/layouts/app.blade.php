<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("title", "RBX-Security Admin")</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- CSS Customizado com Paleta de Cores -->
    <link href="{{ asset('css/admin-custom.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">RBX-Security Admin</h4>
                        <small class="text-white-50">Sistema de Segurança</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is("admin/dashboard") ? "active" : "" }}" href="{{ route("admin.dashboard") }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is("admin/usuarios*") ? "active" : "" }}" href="{{ route("admin.usuarios.index") }}">
                                <i class="fas fa-users me-2"></i>
                                Usuários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is("admin/moradores*") ? "active" : "" }}" href="{{ route("admin.moradores.index") }}">
                                <i class="fas fa-home me-2"></i>
                                Moradores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is("admin/postos*") ? "active" : "" }}" href="{{ route("admin.postos.index") }}">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Postos de Trabalho
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is("admin/escalas*") ? "active" : "" }}" href="{{ route("admin.escalas.index") }}">
                                <i class="fas fa-clock me-2"></i>
                                Escalas
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->is("admin/cartoes-programa*") ? "active" : "" }}" href="{{ route("admin.cartoes-programa.index") }}">
                                <i class="fas fa-id-card me-2"></i>
                                Cartões Programa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is("admin/escalas-relatorio") ? "active" : "" }}" href="{{ route("admin.escalas.relatorio") }}">
                                <i class="fas fa-chart-bar me-2"></i>
                                Relatórios
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield("page-title", "Dashboard")</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        @auth
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i>
                                    {{ Auth::user()->nome }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="alert('Configurações em desenvolvimento')"><i class="fas fa-cog me-2"></i>Configurações</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route("admin.logout") }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endauth
                    </div>
                </div>

                @if(session("success"))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session("success") }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session("error"))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session("error") }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield("content")
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Debug para verificar se JavaScript está funcionando
        console.log('JavaScript carregado corretamente');

        // Verificar se Bootstrap está funcionando
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado');

            // Testar dropdowns
            var dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            console.log('Dropdowns encontrados:', dropdowns.length);

            // Adicionar event listeners para links
            var navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    console.log('Link clicado:', this.textContent.trim());
                });
            });
        });

        // Função para testar alerts
        function testAlert() {
            alert('Teste de JavaScript funcionando!');
        }
    </script>

    <!-- Scripts necessários -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack("scripts")
</body>
</html>
