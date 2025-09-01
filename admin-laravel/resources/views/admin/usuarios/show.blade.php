@extends("layouts.app")

@section("title", "Visualizar Usuário")
@section("page-title", "Detalhes do Usuário")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user"></i> {{ $usuario->nome }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">ID:</label>
                            <p class="form-control-plaintext">{{ $usuario->id }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nome Completo:</label>
                            <p class="form-control-plaintext">{{ $usuario->nome }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">E-mail:</label>
                            <p class="form-control-plaintext">{{ $usuario->email }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Telefone:</label>
                            <p class="form-control-plaintext">{{ $usuario->telefone ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tipo de Usuário:</label>
                            <p class="form-control-plaintext">
                                <span class="badge badge-lg badge-{{ $usuario->tipo == 'admin' ? 'danger' : ($usuario->tipo == 'vigilante' ? 'warning' : 'info') }}">
                                    {{ ucfirst($usuario->tipo) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Status:</label>
                            <p class="form-control-plaintext">
                                <span class="badge badge-lg badge-{{ $usuario->ativo ? 'success' : 'secondary' }}">
                                    {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Data de Criação:</label>
                            <p class="form-control-plaintext">
                                {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i:s') : 'Não disponível' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Última Atualização:</label>
                            <p class="form-control-plaintext">
                                {{ $usuario->data_atualizacao ? $usuario->data_atualizacao->format('d/m/Y H:i:s') : 'Nunca atualizado' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($usuario->tipo == 'vigilante')
                <div class="row">
                    <div class="col-12">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6 class="text-warning">
                                    <i class="fas fa-shield-alt"></i> Informações de Vigilante
                                </h6>
                                <p class="mb-0">Este usuário pode ser escalado para postos de trabalho e acessar o sistema de vigilância.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </a>
                    <div>
                        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form method="POST" action="{{ route('admin.usuarios.toggle-status', $usuario) }}" style="display: inline;">
                            @csrf
                            <button type="submit"
                                    class="btn btn-{{ $usuario->ativo ? 'secondary' : 'success' }}"
                                    onclick="return confirm('Tem certeza que deseja {{ $usuario->ativo ? 'desativar' : 'ativar' }} este usuário?')">
                                <i class="fas fa-{{ $usuario->ativo ? 'ban' : 'check' }}"></i>
                                {{ $usuario->ativo ? 'Desativar' : 'Ativar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}
</style>
@endsection
