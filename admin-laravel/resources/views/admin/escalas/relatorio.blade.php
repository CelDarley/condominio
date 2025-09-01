@extends("layouts.app")

@section("title", "Relatório de Escalas")
@section("page-title", "Relatório de Escalas")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Relatório de Escalas por Dia da Semana</h6>
                <div>
                    <a href="{{ route('admin.escalas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Lista Geral
                    </a>
                    <a href="{{ route('admin.escalas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Escala
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtro de Vigilante -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.escalas.relatorio') }}" class="form-inline">
                            <div class="form-group mr-3">
                                <label for="vigilante_id" class="mr-2">Filtrar por Vigilante:</label>
                                <select name="vigilante_id" id="vigilante_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todos os Vigilantes</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}" {{ request('vigilante_id') == $usuario->id ? 'selected' : '' }}>
                                            {{ $usuario->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    @if(request('vigilante_id') && $totalHoras > 0)
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-clock"></i>
                                <strong>Total de Horas Trabalhadas:</strong>
                                {{ floor($totalHoras / 60) }}h {{ $totalHoras % 60 }}min por semana
                            </div>
                        </div>
                    @endif
                </div>

                @if(!empty($escalas))
                    @foreach($diasSemana as $diaNumero => $diaNome)
                        <div class="card mb-3 border-left-primary">
                            <div class="card-header">
                                <h6 class="m-0">
                                    <i class="fas fa-calendar-day"></i> {{ $diaNome }}
                                    @if(isset($escalas[$diaNumero]))
                                        <span class="badge badge-primary ml-2">{{ $escalas[$diaNumero]->count() }} escala(s)</span>
                                    @else
                                        <span class="badge badge-secondary ml-2">Nenhuma escala</span>
                                    @endif
                                </h6>
                            </div>
                            @if(isset($escalas[$diaNumero]) && $escalas[$diaNumero]->count() > 0)
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Vigilante</th>
                                                    <th>Posto</th>
                                                    <th>Horário</th>
                                                    <th>Status</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($escalas[$diaNumero] as $escala)
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-user-shield text-warning"></i>
                                                        {{ $escala->usuario->nome ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-map-marker-alt text-info"></i>
                                                        {{ $escala->postoTrabalho->nome ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-clock text-success"></i>
                                                        {{ \Carbon\Carbon::parse($escala->horario_inicio)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($escala->horario_fim)->format('H:i') }}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $escala->ativo ? 'success' : 'secondary' }}">
                                                            {{ $escala->ativo ? 'Ativo' : 'Inativo' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.escalas.show', $escala) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.escalas.edit', $escala) }}" class="btn btn-warning btn-sm" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="card-body text-center text-muted">
                                    <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                    <p>Nenhuma escala programada para {{ $diaNome }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">Nenhuma escala encontrada</h5>
                        <p class="text-gray-500">Comece criando escalas para organizar os turnos de trabalho.</p>
                        <a href="{{ route('admin.escalas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeira Escala
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
</style>
@endsection
