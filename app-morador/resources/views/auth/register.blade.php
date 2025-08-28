@extends('layouts.pwa')

@section('title', 'Cadastrar')

@section('styles')
<style>
    body {
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        min-height: 100vh;
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(54, 70, 89, 0.1);
    }
    .card-header {
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        border-radius: 15px 15px 0 0;
    }
    .btn-success {
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        border: none;
        border-radius: 10px;
        transition: all 0.3s;
    }
    .btn-success:hover {
        background: linear-gradient(135deg, #566273 0%, #364659 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(54, 70, 89, 0.4);
    }
    .btn-outline-primary {
        border-color: #364659;
        color: #364659;
    }
    .btn-outline-primary:hover {
        background-color: #364659;
        border-color: #364659;
    }
    .input-group-text {
        background-color: #F2F2F2;
        border-color: #566273;
        color: #364659;
    }
    .form-control {
        border-color: #566273;
    }
    .form-control:focus {
        border-color: #364659;
        box-shadow: 0 0 0 0.2rem rgba(54, 70, 89, 0.25);
    }
    .btn-outline-secondary {
        border-color: #566273;
        color: #566273;
    }
    .btn-outline-secondary:hover {
        background-color: #566273;
        border-color: #566273;
    }
    .alert-info {
        background-color: #F2F2F2;
        border-color: #566273;
        color: #364659;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    Criar Nova Conta
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome Completo *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" name="nome" value="{{ old('nome') }}" required>
                            </div>
                            @error('nome')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="cpf" class="form-label">CPF *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" class="form-control @error('cpf') is-invalid @enderror" 
                                       id="cpf" name="cpf" value="{{ old('cpf') }}" required 
                                       placeholder="000.000.000-00">
                            </div>
                            @error('cpf')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" class="form-control @error('endereco') is-invalid @enderror" 
                                   id="endereco" name="endereco" value="{{ old('endereco') }}" required>
                        </div>
                        @error('endereco')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" class="form-control @error('telefone') is-invalid @enderror" 
                                       id="telefone" name="telefone" value="{{ old('telefone') }}" 
                                       placeholder="(11) 99999-9999">
                            </div>
                            @error('telefone')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="apartamento" class="form-label">Apartamento *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-home"></i>
                                </span>
                                <input type="text" class="form-control @error('apartamento') is-invalid @enderror" 
                                       id="apartamento" name="apartamento" value="{{ old('apartamento') }}" required>
                            </div>
                            @error('apartamento')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="bloco" class="form-label">Bloco</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-building"></i>
                                </span>
                                <input type="text" class="form-control @error('bloco') is-invalid @enderror" 
                                       id="bloco" name="bloco" value="{{ old('bloco') }}" placeholder="A, B, C...">
                            </div>
                            @error('bloco')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Senha *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Senha *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                    <i class="fas fa-eye" id="eyeIconConfirmation"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Importante:</strong> Todos os campos marcados com * são obrigatórios. 
                        Sua conta será verificada pela equipe de segurança antes da ativação.
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-user-plus me-2"></i>
                            Criar Conta
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="text-muted mb-2">Já tem uma conta?</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Fazer Login
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Termos e Condições -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title text-muted">
                    <i class="fas fa-file-contract me-2"></i>
                    Termos e Condições
                </h6>
                <p class="card-text small text-muted">
                    Ao criar uma conta, você concorda com os termos de uso do sistema e confirma que 
                    as informações fornecidas são verdadeiras e precisas.
                </p>
                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Suas informações são protegidas e não serão compartilhadas com terceiros.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Funcionalidade do olhinho para mostrar/ocultar senha
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const eyeIcon = $('#eyeIcon');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        
        if (type === 'text') {
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    $('#togglePasswordConfirmation').click(function() {
        const passwordField = $('#password_confirmation');
        const eyeIcon = $('#eyeIconConfirmation');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        
        if (type === 'text') {
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Código existente de validação de senha
    // Máscara para CPF
    $('#cpf').mask('000.000.000-00');
    
    // Máscara para telefone
    $('#telefone').mask('(00) 00000-0000');
    
    // Validação de senha
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();
        
        if (password !== confirmation) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
@endsection
