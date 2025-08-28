@extends('layouts.app')

@section('title', 'Entrar')

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
                    Entre em contato com a administração do condomínio para solicitar acesso ao sistema.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <small class="text-muted">
                        <i class="fas fa-phone me-1"></i>
                        (11) 99999-9999
                    </small>
                    <small class="text-muted">
                        <i class="fas fa-envelope me-1"></i>
                        admin@condominio.com
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
