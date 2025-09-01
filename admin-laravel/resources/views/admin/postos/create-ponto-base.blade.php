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

                <form method="POST" action="{{ route('admin.postos.pontos-base.store', $posto) }}" id="form-ponto-base">
                    @csrf
                    <input type="hidden" name="posto_trabalho_id" value="{{ $posto->id }}">

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
                                       value="{{ old('nome') }}"
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
                                           {{ old('ativo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Ponto base ativo
                                    </label>
                                    <small class="form-text text-muted d-block">Pontos inativos não aparecem nas rondas</small>
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
                                                <li><strong>Pontos estratégicos:</strong> Inclua locais de maior risco ou importância</li>
                                                <li><strong>Localização precisa:</strong> Defina endereços claros e específicos</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="mb-0 small">
                                                <li><strong>Endereço preciso:</strong> Facilite a localização do vigilante</li>
                                                <li><strong>Coordenadas GPS:</strong> Ajudam na localização precisa</li>
                                                <li><strong>Descrição clara:</strong> Detalhe características importantes do local</li>
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
                                            <button type="submit" class="btn btn-primary" id="btn-submit">
                        <i class="fas fa-save"></i> Salvar Ponto Base
                    </button>

                    <!-- Botão de teste para debug -->
                    <button type="button" class="btn btn-outline-info ms-2" onclick="testarFormulario()">
                        <i class="fas fa-bug"></i> Testar
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-ponto-base');
    const submitBtn = form.querySelector('button[type="submit"]');

        // Prevenir múltiplos envios
    form.addEventListener('submit', function(e) {
        console.log('Evento submit disparado');

        // Log para debug antes de qualquer modificação
        const formDataBefore = new FormData(form);
        console.log('Dados do formulário ANTES do submit:');
        for (let [key, value] of formDataBefore.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Verificar campo posto_trabalho_id especificamente
        const campoPosto = form.querySelector('input[name="posto_trabalho_id"]');
        console.log('Campo posto_trabalho_id no submit:', campoPosto ? campoPosto.value : 'não encontrado');

        // Desabilitar botão e mostrar loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';

        // Adicionar classe de loading ao formulário
        form.classList.add('loading');

        // Log para debug
        console.log('Enviando formulário...');
        console.log('Dados do formulário:', new FormData(form));

        // Permitir que o formulário continue
        return true;
    });

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

    // Log para debug
    console.log('Formulário de ponto base carregado');

    // Teste de funcionalidade
    console.log('Formulário encontrado:', form);
    console.log('Botão de submit encontrado:', submitBtn);

    // Verificar se todos os campos estão presentes
    const campos = ['nome', 'endereco', 'descricao', 'latitude', 'longitude', 'ativo'];
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        console.log(`Campo ${campo}:`, elemento ? 'encontrado' : 'não encontrado');
    });

    // Verificar campo hidden posto_trabalho_id
    const campoPosto = form.querySelector('input[name="posto_trabalho_id"]');
    console.log('Campo posto_trabalho_id:', campoPosto ? 'encontrado' : 'não encontrado');
    if (campoPosto) {
        console.log('Valor do campo posto_trabalho_id:', campoPosto.value);
    }
});

// Função para testar o formulário
window.testarFormulario = function() {
    console.log('=== TESTE DO FORMULÁRIO ===');

    const form = document.getElementById('form-ponto-base');
    const formData = new FormData(form);

    console.log('Dados do formulário:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    // Verificar validação HTML5
    if (form.checkValidity()) {
        console.log('✅ Formulário válido (HTML5)');
    } else {
        console.log('❌ Formulário inválido (HTML5)');
        form.reportValidity();
    }

    // Verificar se todos os campos obrigatórios estão preenchidos
    const nome = document.getElementById('nome').value;
    const endereco = document.getElementById('endereco').value;

    console.log(`Nome: "${nome}" (${nome ? 'preenchido' : 'vazio'})`);
    console.log(`Endereço: "${endereco}" (${endereco ? 'preenchido' : 'vazio'})`);

    if (nome && endereco) {
        console.log('✅ Campos obrigatórios preenchidos');
    } else {
        console.log('❌ Campos obrigatórios não preenchidos');
    }
};
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
