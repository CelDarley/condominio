@extends('layouts.app')

@section('title', 'Editar Morador')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-edit me-2"></i>Editar Morador</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.moradores.show', $morador) }}" class="btn btn-info">
            <i class="fas fa-eye me-2"></i>Visualizar
        </a>
        <a href="{{ route('admin.moradores.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.moradores.update', $morador) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" name="nome" value="{{ old('nome', $morador->nome) }}" required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">E-mail *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $morador->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control @error('telefone') is-invalid @enderror" 
                           id="telefone" name="telefone" value="{{ old('telefone', $morador->telefone) }}" 
                           placeholder="(11) 99999-9999">
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="cpf" class="form-label">CPF *</label>
                    <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                           id="cpf" name="cpf" value="{{ old('cpf', $morador->cpf) }}" required
                           placeholder="000.000.000-00">
                    @error('cpf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço *</label>
                <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
                       id="endereco" name="endereco" value="{{ old('endereco', $morador->endereco) }}" required>
                @error('endereco')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="apartamento" class="form-label">Apartamento *</label>
                    <input type="text" class="form-control @error('apartamento') is-invalid @enderror" 
                           id="apartamento" name="apartamento" value="{{ old('apartamento', $morador->apartamento) }}" required>
                    @error('apartamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="bloco" class="form-label">Bloco</label>
                    <input type="text" class="form-control @error('bloco') is-invalid @enderror" 
                           id="bloco" name="bloco" value="{{ old('bloco', $morador->bloco) }}">
                    @error('bloco')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Nota:</strong> Para alterar a senha, use o botão "Alterar Senha" na página de visualização do morador.
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Salvar Alterações
                </button>
                <a href="{{ route('admin.moradores.show', $morador) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>Visualizar
                </a>
                <a href="{{ route('admin.moradores.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    telefoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        e.target.value = value;
    });
    
    // Máscara para CPF
    const cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})/, '$1-$2');
        e.target.value = value;
    });
});
</script>
@endsection 