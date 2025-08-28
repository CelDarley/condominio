@extends("layouts.app")

@section("title", "Novo Ponto Base - " . $posto->nome)
@section("page-title", "Novo Ponto Base para: " . $posto->nome)

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-map-pin"></i> Cadastrar Novo Ponto Base
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

                <form method="POST" action="{{ route('admin.postos.pontos-base.store', $posto) }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Ponto Base <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}" 
                                       required 
                                       placeholder="Ex: Portaria Principal, Guarita Sul, Estacionamento A, etc.">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="ordem" class="form-label">Ordem na Ronda <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('ordem') is-invalid @enderror" 
                                       id="ordem" 
                                       name="ordem" 
                                       value="{{ old('ordem', $proximaOrdem ?? 1) }}" 
                                       required 
                                       min="1"
                                       placeholder="1">
                                @error('ordem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Define a sequência da ronda</small>
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
                                       value="{{ old('endereco') }}" 
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
                                          placeholder="Descreva a localização e características deste ponto base...">{{ old('descricao') }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                       value="{{ old('latitude') }}" 
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
                                       value="{{ old('longitude') }}" 
                                       placeholder="-43.940933"
                                       pattern="-?\d+\.?\d*">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Coordenada geográfica (GPS)</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configurações de Itinerário -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-warning mb-3">
                                <div class="card-body">
                                    <h6 class="text-warning">
                                        <i class="fas fa-clock"></i> Configurações de Itinerário
                                    </h6>
                                    <p class="mb-2 text-muted small">Define os tempos de permanência e deslocamento para este ponto base</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="horario_inicio" class="form-label">Horário Início</label>
                                <input type="time" 
                                       class="form-control @error('horario_inicio') is-invalid @enderror" 
                                       id="horario_inicio" 
                                       name="horario_inicio" 
                                       value="{{ old('horario_inicio', '08:00') }}">
                                @error('horario_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Quando inicia a ronda</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="horario_fim" class="form-label">Horário Fim</label>
                                <input type="time" 
                                       class="form-control @error('horario_fim') is-invalid @enderror" 
                                       id="horario_fim" 
                                       name="horario_fim" 
                                       value="{{ old('horario_fim', '18:00') }}">
                                @error('horario_fim')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Quando termina a ronda</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="tempo_permanencia" class="form-label">Tempo Permanência</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('tempo_permanencia') is-invalid @enderror" 
                                           id="tempo_permanencia" 
                                           name="tempo_permanencia" 
                                           value="{{ old('tempo_permanencia', 10) }}" 
                                           min="1" 
                                           max="120"
                                           placeholder="10">
                                    <span class="input-group-text">min</span>
                                </div>
                                @error('tempo_permanencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tempo no ponto (minutos)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="tempo_deslocamento" class="form-label">Tempo Deslocamento</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('tempo_deslocamento') is-invalid @enderror" 
                                           id="tempo_deslocamento" 
                                           name="tempo_deslocamento" 
                                           value="{{ old('tempo_deslocamento', 5) }}" 
                                           min="1" 
                                           max="60"
                                           placeholder="5">
                                    <span class="input-group-text">min</span>
                                </div>
                                @error('tempo_deslocamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tempo até próximo ponto</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="instrucoes" class="form-label">Instruções para o Vigilante</label>
                                <textarea class="form-control @error('instrucoes') is-invalid @enderror" 
                                          id="instrucoes" 
                                          name="instrucoes" 
                                          rows="3"
                                          placeholder="Ex: Verificar se os portões estão fechados, observar movimentação suspeita...">{{ old('instrucoes') }}</textarea>
                                @error('instrucoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">O que o vigilante deve fazer neste ponto</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Opções</label>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="ativo" 
                                                   name="ativo" 
                                                   {{ old('ativo', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ativo">
                                                Ponto base ativo
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="gerar_qr" 
                                                   name="gerar_qr" 
                                                   checked>
                                            <label class="form-check-label" for="gerar_qr">
                                                Gerar QR Code automaticamente
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="obrigatorio" 
                                                   name="obrigatorio" 
                                                   {{ old('obrigatorio', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="obrigatorio">
                                                Verificação obrigatória
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dicas e Informações -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h6 class="text-primary">
                                        <i class="fas fa-lightbulb"></i> Dicas para Criar Pontos Base Eficazes
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="mb-0 small">
                                                <li><strong>Nome claro:</strong> Use nomes descritivos e fáceis de identificar</li>
                                                <li><strong>Ordem lógica:</strong> Defina uma sequência que faça sentido geograficamente</li>
                                                <li><strong>Pontos estratégicos:</strong> Inclua locais de maior risco ou importância</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="mb-0 small">
                                                <li><strong>Instruções claras:</strong> Detalhe o que deve ser verificado</li>
                                                <li><strong>Coordenadas GPS:</strong> Ajudam na localização precisa</li>
                                                <li><strong>QR Codes:</strong> Facilitam a verificação digital da ronda</li>
                                            </ul>
                                        </div>
                                    </div>
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Ponto Base
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-preenchimento de coordenadas GPS (se suportado pelo navegador)
    if (navigator.geolocation) {
        const btnGPS = document.createElement('button');
        btnGPS.type = 'button';
        btnGPS.className = 'btn btn-outline-info btn-sm mt-2';
        btnGPS.innerHTML = '<i class="fas fa-map-marker-alt"></i> Usar Localização Atual';
        btnGPS.onclick = function() {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                alert('Coordenadas GPS atualizadas!');
            }, function(error) {
                alert('Erro ao obter localização: ' + error.message);
            });
        };
        
        // Inserir botão após o campo longitude
        document.getElementById('longitude').parentNode.appendChild(btnGPS);
    }
    
    // Validação de coordenadas
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    function validarCoordenada(input, min, max) {
        input.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (this.value && (isNaN(value) || value < min || value > max)) {
                this.setCustomValidity(`Valor deve estar entre ${min} e ${max}`);
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    }
    
    validarCoordenada(latInput, -90, 90);
    validarCoordenada(lngInput, -180, 180);
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
</style>
@endsection 