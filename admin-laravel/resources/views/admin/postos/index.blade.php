@extends("layouts.app")

@section("title", "Postos de Trabalho")
@section("page-title", "Postos de Trabalho")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Postos de Trabalho</h6>
                <a href="{{ route('admin.postos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Posto
                </a>
            </div>
            <div class="card-body">
                @if($postos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($postos as $posto)
                                <tr>
                                    <td>{{ $posto->id }}</td>
                                    <td>{{ $posto->nome }}</td>
                                    <td>{{ $posto->descricao ?? 'Sem descrição' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $posto->ativo ? 'success' : 'secondary' }}">
                                            {{ $posto->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.postos.show', $posto) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.postos.edit', $posto) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.postos.pontos-base', $posto) }}" class="btn btn-secondary btn-sm" title="Pontos Base">
                                                <i class="fas fa-map-marker"></i>
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
                        <i class="fas fa-map-marker-alt fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Nenhum posto de trabalho encontrado</p>
                        <a href="{{ route('admin.postos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeiro Posto
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 