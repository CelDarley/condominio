@extends("layouts.app")

@section("title", "Gerenciar Usuários")
@section("page-title", "Usuários")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Usuários</h6>
                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Usuário
                </a>
            </div>
            <div class="card-body">
                @if($usuarios->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Telefone</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->id }}</td>
                                    <td>{{ $usuario->nome }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>{{ $usuario->telefone ?? 'Não informado' }}</td>
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
                                    <td>{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.usuarios.show', $usuario) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($usuario->ativo)
                                                <!-- Botão para desativar -->
                                                <form method="POST" action="{{ route('admin.usuarios.deactivate', $usuario) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-secondary btn-sm"
                                                            title="Desativar usuário"
                                                            onclick="return confirm('Tem certeza que deseja desativar este usuário?')">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Botão para ativar -->
                                                <form method="POST" action="{{ route('admin.usuarios.toggle-status', $usuario) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                            title="Ativar usuário"
                                                            onclick="return confirm('Tem certeza que deseja ativar este usuário?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                <!-- Botão para exclusão permanente -->
                                                <form method="POST" action="{{ route('admin.usuarios.force-delete', $usuario) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                            title="Excluir permanentemente"
                                                            onclick="return confirm('ATENÇÃO: Esta ação é irreversível! O usuário será excluído permanentemente do banco de dados. Tem certeza?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
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
                        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeiro Usuário
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
