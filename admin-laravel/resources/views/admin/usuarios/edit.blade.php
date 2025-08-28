@extends("layouts.app")

@section("title", "Editar Usuário")
@section("page-title", "Editar Usuário")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-edit"></i> Editar: {{ $usuario->nome }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome', $usuario->nome) }}" 
                                       required 
                                       placeholder="Digite o nome completo">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $usuario->email) }}" 
                                       required 
                                       placeholder="Digite o e-mail">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" 
                                       class="form-control @error('telefone') is-invalid @enderror" 
                                       id="telefone" 
                                       name="telefone" 
                                       value="{{ old('telefone', $usuario->telefone) }}" 
                                       placeholder="(11) 99999-9999">
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo de Usuário <span class="text-danger">*</span></label>
                                <select class="form-control @error('tipo') is-invalid @enderror" 
                                        id="tipo" 
                                        name="tipo" 
                                        required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="vigilante" {{ old('tipo', $usuario->tipo) == 'vigilante' ? 'selected' : '' }}>Vigilante</option>
                                    <option value="morador" {{ old('tipo', $usuario->tipo) == 'morador' ? 'selected' : '' }}>Morador</option>
                                    <option value="admin" {{ old('tipo', $usuario->tipo) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nova_senha" class="form-label">Nova Senha</label>
                                <input type="password" 
                                       class="form-control @error('nova_senha') is-invalid @enderror" 
                                       id="nova_senha" 
                                       name="nova_senha" 
                                       minlength="6"
                                       placeholder="Deixe em branco para manter a atual">
                                @error('nova_senha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Deixe em branco se não quiser alterar a senha</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirmar_nova_senha" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirmar_nova_senha" 
                                       name="confirmar_nova_senha" 
                                       minlength="6"
                                       placeholder="Confirme a nova senha">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="ativo" 
                                           name="ativo" 
                                           {{ old('ativo', $usuario->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Usuário ativo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informações:</strong>
                        <ul class="mb-0 mt-2">
                            <li>ID do usuário: {{ $usuario->id }}</li>
                            <li>Criado em: {{ $usuario->data_criacao ? $usuario->data_criacao->format('d/m/Y H:i:s') : 'N/A' }}</li>
                            <li>Para manter a senha atual, deixe os campos de senha em branco</li>
                        </ul>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar para Lista
                            </a>
                            <a href="{{ route('admin.usuarios.show', $usuario) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Visualizar
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação de confirmação de senha
    const novaSenha = document.getElementById('nova_senha');
    const confirmarNovaSenha = document.getElementById('confirmar_nova_senha');
    
    function validarSenhas() {
        if (novaSenha.value && novaSenha.value !== confirmarNovaSenha.value) {
            confirmarNovaSenha.setCustomValidity('As senhas não coincidem');
        } else {
            confirmarNovaSenha.setCustomValidity('');
        }
    }
    
    novaSenha.addEventListener('change', validarSenhas);
    confirmarNovaSenha.addEventListener('keyup', validarSenhas);
});
</script>
@endsection 