@extends('layouts.app')

@section('title', 'Visualizar Morador')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user me-2"></i>{{ $morador->nome }}</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.moradores.edit', $morador) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Editar
        </a>
        <a href="{{ route('admin.moradores.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Informações do Morador -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-user me-2"></i>Informações Pessoais</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nome:</strong> {{ $morador->nome }}</p>
                        <p><strong>E-mail:</strong> {{ $morador->email }}</p>
                        <p><strong>Telefone:</strong> {{ $morador->telefone ?? 'Não informado' }}</p>
                        <p><strong>CPF:</strong> {{ $morador->cpf }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Endereço:</strong> {{ $morador->endereco }}</p>
                        <p><strong>Apartamento:</strong> {{ $morador->apartamento }}</p>
                        <p><strong>Bloco:</strong> {{ $morador->bloco ?? 'Não informado' }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $morador->ativo ? 'success' : 'danger' }}">
                                {{ $morador->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Veículos -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-car me-2"></i>Veículos</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVeiculoModal">
                    <i class="fas fa-plus me-2"></i>Adicionar Veículo
                </button>
            </div>
            <div class="card-body">
                @if($morador->veiculos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Placa</th>
                                    <th>Cor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($morador->veiculos as $veiculo)
                                    <tr>
                                        <td>{{ $veiculo->marca }}</td>
                                        <td>{{ $veiculo->modelo }}</td>
                                        <td><span class="badge bg-info">{{ $veiculo->placa }}</span></td>
                                        <td>{{ $veiculo->cor }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.moradores.remove-veiculo', [$morador, $veiculo]) }}" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Tem certeza que deseja remover este veículo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-car fa-3x mb-3"></i>
                        <p>Nenhum veículo cadastrado</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVeiculoModal">
                            <i class="fas fa-plus me-2"></i>Cadastrar Primeiro Veículo
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Ações -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cogs me-2"></i>Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fas fa-key me-2"></i>Alterar Senha
                    </button>
                    
                    <form method="POST" action="{{ route('admin.moradores.toggle-status', $morador) }}">
                        @csrf
                        <button type="submit" class="btn btn-{{ $morador->ativo ? 'secondary' : 'success' }} w-100">
                            <i class="fas fa-{{ $morador->ativo ? 'ban' : 'check' }} me-2"></i>
                            {{ $morador->ativo ? 'Desativar' : 'Ativar' }} Morador
                        </button>
                    </form>
                    
                    <hr>
                    
                    <form method="POST" action="{{ route('admin.moradores.destroy', $morador) }}" 
                          onsubmit="return confirm('ATENÇÃO: Esta ação é irreversível! Tem certeza que deseja excluir este morador e todos os seus dados?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Excluir Morador
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Informações</h5>
            </div>
            <div class="card-body">
                <p><strong>Cadastrado em:</strong><br>{{ $morador->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Última atualização:</strong><br>{{ $morador->updated_at->format('d/m/Y H:i') }}</p>
                <p><strong>Total de veículos:</strong> {{ $morador->veiculos->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Veículo -->
<div class="modal fade" id="addVeiculoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Veículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.moradores.add-veiculo', $morador) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="marca" class="form-label">Marca *</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo *</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="mb-3">
                        <label for="placa" class="form-label">Placa *</label>
                        <input type="text" class="form-control" id="placa" name="placa" required 
                               placeholder="ABC-1234">
                    </div>
                    <div class="mb-3">
                        <label for="cor" class="form-label">Cor *</label>
                        <input type="text" class="form-control" id="cor" name="cor" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar Veículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Alterar Senha -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alterar Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.moradores.change-password', $morador) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password" class="form-label">Nova Senha *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordModal">
                                <i class="fas fa-eye" id="eyeIconModal"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmationModal">
                                <i class="fas fa-eye" id="eyeIconConfirmationModal"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Alterar Senha</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para placa
    const placaInput = document.getElementById('placa');
    placaInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
        if (value.length <= 7) {
            value = value.replace(/([A-Z]{3})([0-9])/, '$1-$2');
        }
        e.target.value = value;
    });
    
    // Funcionalidade do olhinho para senha no modal
    const togglePasswordModal = document.getElementById('togglePasswordModal');
    const passwordFieldModal = document.getElementById('password');
    const eyeIconModal = document.getElementById('eyeIconModal');
    
    togglePasswordModal.addEventListener('click', function() {
        const type = passwordFieldModal.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordFieldModal.setAttribute('type', type);
        
        if (type === 'text') {
            eyeIconModal.classList.remove('fa-eye');
            eyeIconModal.classList.add('fa-eye-slash');
        } else {
            eyeIconModal.classList.remove('fa-eye-slash');
            eyeIconModal.classList.add('fa-eye');
        }
    });
    
    // Funcionalidade do olhinho para confirmar senha no modal
    const togglePasswordConfirmationModal = document.getElementById('togglePasswordConfirmationModal');
    const passwordConfirmationFieldModal = document.getElementById('password_confirmation');
    const eyeIconConfirmationModal = document.getElementById('eyeIconConfirmationModal');
    
    togglePasswordConfirmationModal.addEventListener('click', function() {
        const type = passwordConfirmationFieldModal.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmationFieldModal.setAttribute('type', type);
        
        if (type === 'text') {
            eyeIconConfirmationModal.classList.remove('fa-eye');
            eyeIconConfirmationModal.classList.add('fa-eye-slash');
        } else {
            eyeIconConfirmationModal.classList.remove('fa-eye-slash');
            eyeIconConfirmationModal.classList.add('fa-eye');
        }
    });
});
</script>
@endsection 