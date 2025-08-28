@extends("layouts.app")

@section("title", "Editar Ponto Base - " . $ponto->nome)
@section("page-title", "Editar Ponto Base: " . $ponto->nome)

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Editar Ponto Base
                </h6>
            </div>
            <div class="card-body">
                <!-- Informações do Posto -->
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="alert-heading">
                                <i class="fas fa-map-marker-alt"></i> {{ $posto->nome }}
                            </h6>
                            <p class="mb-0">{{ $posto->descricao ?? 'Sem descrição' }}</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge badge-{{ $posto->ativo ? 'success' : 'secondary' }} badge-lg">
                                {{ $posto->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.postos.pontos-base.update', ['posto' => $posto, 'ponto' => $ponto]) }}" id="form-edit-ponto-base">
                    @csrf
                    @method('PUT')
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Ponto Base <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('nome') is-invalid @enderror"
                                       id="nome"
                                       name="nome"
                                       value="{{ old('nome', $ponto->nome) }}"
                                       required
                                       placeholder="Ex: Portaria Principal, Guarita Sul, Estacionamento A, etc.">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="endereco" class="form-label">Endereço/Localização <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('endereco') is-invalid @enderror"
                                       id="endereco"
                                       name="endereco"
                                       value="{{ old('endereco', $ponto->endereco) }}"
                                       required
                                       placeholder="Ex: Rua das Flores, 123 - Próximo ao portão principal">
                                @error('endereco')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Localização específica do ponto base</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror"
                                          id="descricao"
                                          name="descricao"
                                          rows="4"
                                          placeholder="Descreva a localização e características deste ponto base...">{{ old('descricao', $ponto->descricao) }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Descrição detalhada do ponto base (opcional)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Latitude (opcional)</label>
                                <input type="text"
                                       class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude"
                                       name="latitude"
                                       value="{{ old('latitude', $ponto->latitude) }}"
                                       placeholder="-19.912998"
                                       pattern="-?\d+\.?\d*">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Coordenada geográfica (GPS)</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Longitude (opcional)</label>
                                <input type="text"
                                       class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude"
                                       name="longitude"
                                       value="{{ old('longitude', $ponto->longitude) }}"
                                       placeholder="-43.940933"
                                       pattern="-?\d+\.?\d*">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Coordenada geográfica (GPS)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="ativo"
                                           name="ativo"
                                           {{ old('ativo', $ponto->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Ponto base ativo
                                    </label>
                                    <small class="form-text text-muted d-block">Pontos inativos não aparecem nas rondas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.postos.pontos-base', $posto) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar para Pontos Base
                            </a>
                            <a href="{{ route('admin.postos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list"></i> Todos os Postos
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <i class="fas fa-save"></i> Atualizar Ponto Base
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-edit-ponto-base');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Prevenir múltiplos envios
    form.addEventListener('submit', function(e) {
        console.log('Evento submit disparado');
        
        // Desabilitar botão e mostrar loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Atualizando...';
        
        // Adicionar classe de loading ao formulário
        form.classList.add('loading');
        
        // Log para debug
        console.log('Atualizando ponto base...');
        console.log('Dados do formulário:', new FormData(form));
        
        // Permitir que o formulário continue
        return true;
    });
    
    // Log para debug
    console.log('Formulário de edição de ponto base carregado');
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid var(--primary-dark) !important;
}

.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.card-body .small {
    font-size: 0.875rem;
}

/* Estilos para loading */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading .form-control {
    background-color: #f8f9fa;
}

/* Estilos para botão desabilitado */
.btn:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

/* Animação de loading */
.fa-spin {
    animation: fa-spin 2s infinite linear;
}

@keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection
