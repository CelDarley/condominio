@extends("layouts.app")

@section("title", "Escalas")
@section("page-title", "Escalas")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Escalas</h6>
                <div>
                    <a href="{{ route('admin.escalas.relatorio') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Relatório
                    </a>
                    <a href="{{ route('admin.escalas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Escala
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($escalas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Posto</th>
                                    <th>Cartão Programa</th>
                                    <th>Dia da Semana</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($escalas as $escala)
                                <tr>
                                    <td>{{ $escala->id }}</td>
                                    <td>{{ $escala->usuario->nome ?? 'N/A' }}</td>
                                    <td>{{ $escala->postoTrabalho->nome ?? 'N/A' }}</td>
                                    <td>
                                        @if($escala->cartaoPrograma)
                                            <span class="badge badge-primary">
                                                {{ $escala->cartaoPrograma->nome }}
                                            </span>
                                            <br><small class="text-muted">{{ $escala->cartaoPrograma->horario_inicio }} - {{ $escala->cartaoPrograma->horario_fim }}</small>
                                        @else
                                            <span class="text-muted">Não definido</span>
                                        @endif
                                    </td>
                                    <td>{{ $escala->getDiaSemanaNome() }}</td>
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
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Nenhuma escala encontrada</p>
                        <a href="{{ route('admin.escalas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeira Escala
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 