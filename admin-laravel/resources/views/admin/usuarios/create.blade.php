@extends("layouts.app")

@section("title", "Novo Usuário")
@section("page-title", "Novo Usuário")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-plus"></i> Cadastrar Novo Usuário
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.usuarios.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}" 
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
                                       value="{{ old('email') }}" 
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
                                       value="{{ old('telefone') }}" 
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
                                    <option value="vigilante" {{ old('tipo') == 'vigilante' ? 'selected' : '' }}>Vigilante</option>
                                    <option value="morador" {{ old('tipo') == 'morador' ? 'selected' : '' }}>Morador</option>
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
                                <label for="senha" class="form-label">Senha <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control @error('senha') is-invalid @enderror" 
                                       id="senha" 
                                       name="senha" 
                                       required 
                                       minlength="6"
                                       placeholder="Mínimo 6 caracteres">
                                @error('senha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirmar_senha" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirmar_senha" 
                                       name="confirmar_senha" 
                                       required 
                                       minlength="6"
                                       placeholder="Confirme a senha">
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
                                           checked>
                                    <label class="form-check-label" for="ativo">
                                        Usuário ativo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Usuário
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
    const senha = document.getElementById('senha');
    const confirmarSenha = document.getElementById('confirmar_senha');
    
    function validarSenhas() {
        if (senha.value !== confirmarSenha.value) {
            confirmarSenha.setCustomValidity('As senhas não coincidem');
        } else {
            confirmarSenha.setCustomValidity('');
        }
    }
    
    senha.addEventListener('change', validarSenhas);
    confirmarSenha.addEventListener('keyup', validarSenhas);
});
</script>
@endsection 