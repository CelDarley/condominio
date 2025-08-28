@extends('layouts.pwa')

@section('title', 'Entrar')

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
    .btn-primary {
        background: linear-gradient(135deg, #364659 0%, #566273 100%);
        border: none;
        border-radius: 10px;
        transition: all 0.3s;
    }
    .btn-primary:hover {
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
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Entrar
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
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
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Entrar
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="text-muted mb-2">Não tem uma conta?</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Criar Conta
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Informações Adicionais -->
        <div class="card mt-3">
            <div class="card-body text-center">
                <h6 class="card-title text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Precisa de Ajuda?
                </h6>
                <p class="card-text small text-muted">
                    Entre em contato com a portaria ou suporte técnico para solicitar acesso ao sistema.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <small class="text-muted">
                        <i class="fas fa-phone me-1"></i>
                        (11) 99999-9999
                    </small>
                    <small class="text-muted">
                        <i class="fas fa-envelope me-1"></i>
                        suporte@rbx-security.com
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        
        // Alterna o ícone
        if (type === 'text') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});
</script>
@endsection
