@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Estatísticas -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Usuários</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsuarios ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Vigilantes Ativos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $vigilantesAtivos ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Postos de Trabalho</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPostos ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Escalas Ativas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $escalasAtivas ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Usuários Recentes -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Usuários Recentes</h6>
                <a href="#" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Novo Usuário
                </a>
            </div>
            <div class="card-body">
                @if(isset($usuariosRecentes) && count($usuariosRecentes) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuariosRecentes as $usuario)
                                <tr>
                                    <td>{{ $usuario->nome }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $usuario->tipo == 'admin' ? 'danger' : ($usuario->tipo == 'vigilante' ? 'warning' : 'info') }}">
                                            {{ ucfirst($usuario->tipo) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $usuario->ativo ? 'success' : 'secondary' }}">
                                            {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Nenhum usuário encontrado</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Atividades Recentes -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Atividades Recentes</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Novo usuário cadastrado</h6>
                            <p class="timeline-text">João Silva foi cadastrado como vigilante</p>
                            <small class="text-muted">Há 2 horas</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Escala atualizada</h6>
                            <p class="timeline-text">Escala do posto A foi modificada</p>
                            <small class="text-muted">Há 4 horas</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Alerta de segurança</h6>
                            <p class="timeline-text">Movimento detectado no posto B</p>
                            <small class="text-muted">Há 6 horas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Atividade -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Atividade Semanal</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    const activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
            datasets: [{
                label: 'Registros de Presença',
                data: [65, 59, 80, 81, 56, 55, 40],
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.chart-area {
    position: relative;
    height: 20rem;
    width: 100%;
}
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}
.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.timeline-content {
    padding-left: 10px;
}
.timeline-title {
    margin-bottom: 0.25rem;
    font-weight: 600;
}
.timeline-text {
    margin-bottom: 0.25rem;
    color: #6c757d;
}
</style>
