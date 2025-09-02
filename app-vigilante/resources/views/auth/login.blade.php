@extends('layouts.app')

@section('title', 'Login - SegCond Vigilante')
@section('page-title', 'Login')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-medium) 100%);
        padding-bottom: 0;
    }

    .main-content {
        margin-top: 0;
        padding: 2rem 1rem;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-container {
        width: 100%;
        max-width: 400px;
    }

    .login-card {
        background: var(--white);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        text-align: center;
    }

    .login-header {
        margin-bottom: 2rem;
    }

    .login-icon {
        font-size: 3rem;
        color: var(--primary-dark);
        margin-bottom: 1rem;
    }

    .login-title {
        color: var(--primary-dark);
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .login-subtitle {
        color: #666;
        font-size: 0.9rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
        font-weight: 500;
    }

    .form-control {
        border-radius: 12px;
        border: 2px solid #e0e0e0;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-medium);
        box-shadow: 0 0 0 0.2rem rgba(86, 98, 115, 0.25);
    }

    .input-group {
        position: relative;
    }

    .input-group-text {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        z-index: 10;
    }

    .form-control.with-icon {
        padding-left: 3rem;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        z-index: 10;
    }

    .btn-login {
        width: 100%;
        background: linear-gradient(45deg, var(--primary-dark) 0%, var(--primary-medium) 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem;
        color: var(--white);
        font-weight: bold;
        font-size: 1.1rem;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(54, 70, 89, 0.4);
    }

    .remember-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 1rem 0;
    }

    .form-check {
        display: flex;
        align-items: center;
    }

    .form-check-input {
        margin-right: 0.5rem;
    }

    .form-check-label {
        font-size: 0.9rem;
        color: #666;
    }

    .login-footer {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e0e0e0;
        font-size: 0.8rem;
        color: #666;
    }

    .error-message {
        background-color: #fee;
        border: 1px solid #fcc;
        color: #c33;
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    @media (max-width: 576px) {
        .main-content {
            padding: 1rem;
        }
        
        .login-card {
            padding: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-shield-alt login-icon"></i>
            <h2 class="login-title">SegCond Vigilante</h2>
            <p class="login-subtitle">Sistema de Vigilância Mobile</p>
        </div>

        @if ($errors->any())
            <div class="error-message">
                <i class="fas fa-exclamation-triangle me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope me-1"></i>
                    Email
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control with-icon @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}"
                           placeholder="Digite seu email" 
                           autocomplete="email"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="senha">
                    <i class="fas fa-lock me-1"></i>
                    Senha
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" 
                           id="senha" 
                           name="senha" 
                           class="form-control with-icon @error('senha') is-invalid @enderror" 
                           placeholder="Digite sua senha" 
                           autocomplete="current-password"
                           required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </button>
                </div>
            </div>

            <div class="remember-section">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Lembrar-me
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>
                Entrar
            </button>
        </form>

        <div class="login-footer">
            <p class="mb-0">
                <i class="fas fa-info-circle me-1"></i>
                Acesso restrito a vigilantes autorizados
            </p>
            <p class="mb-0 mt-1">© 2024 SegCond - Todos os direitos reservados</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const senhaInput = document.getElementById('senha');
    const passwordIcon = document.getElementById('password-icon');
    
    if (senhaInput.type === 'password') {
        senhaInput.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        senhaInput.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}

// Auto-focus no primeiro campo
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('email').focus();
});

// Prevent double submission
document.querySelector('form').addEventListener('submit', function() {
    const button = document.querySelector('.btn-login');
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Entrando...';
    button.disabled = true;
});
</script>
@endpush 