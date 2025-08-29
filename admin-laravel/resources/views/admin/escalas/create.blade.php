@extends("layouts.app")

@section("title", "Nova Escala")
@section("page-title", "Nova Escala")

@section("content")
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock"></i> Cadastrar Nova Escala
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.escalas.store') }}" id="form-escala">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usuario_id" class="form-label">Vigilante/Profissional <span class="text-danger">*</span></label>
                                <select class="form-control @error('usuario_id') is-invalid @enderror" 
                                        id="usuario_id" 
                                        name="usuario_id" 
                                        required>
                                    <option value="">Selecione um profissional</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                            {{ $usuario->nome }} ({{ $usuario->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('usuario_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="posto_trabalho_id" class="form-label">Posto de Trabalho <span class="text-danger">*</span></label>
                                <select class="form-control @error('posto_trabalho_id') is-invalid @enderror" 
                                        id="posto_trabalho_id" 
                                        name="posto_trabalho_id" 
                                        required
                                        onchange="carregarCartoesPrograma()">
                                    <option value="">Selecione um posto</option>
                                    @foreach($postos as $posto)
                                        <option value="{{ $posto->id }}" {{ old('posto_trabalho_id') == $posto->id ? 'selected' : '' }}>
                                            {{ $posto->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('posto_trabalho_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Dias da Semana -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-info">
                                <i class="fas fa-calendar-week"></i> Selecionar Dias da Semana
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($diasSemana as $numero => $nome)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card dia-card">
                                        <div class="card-body p-3">
                                            <div class="form-check mb-2">
                                                <input type="checkbox" 
                                                       class="form-check-input dia-checkbox" 
                                                       id="dia_{{ $numero }}" 
                                                       name="dias[{{ $numero }}][ativo]" 
                                                       value="1"
                                                       onchange="toggleDiaOptions({{ $numero }})">
                                                <label class="form-check-label fw-bold" for="dia_{{ $numero }}">
                                                    {{ $nome }}
                                                </label>
                                            </div>
                                            
                                            <div class="dia-options" id="options_{{ $numero }}" style="display: none;">
                                                <div class="mb-2">
                                                    <label class="form-label small">Cartão Programa:</label>
                                                    <select class="form-control form-control-sm cartao-select" 
                                                            name="dias[{{ $numero }}][cartao_programa_id]" 
                                                            id="cartao_{{ $numero }}">
                                                        <option value="">Selecione primeiro um posto</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    @if($usuarios->isEmpty())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Não há vigilantes cadastrados. 
                        <a href="{{ route('admin.usuarios.create') }}" class="alert-link">Cadastre um vigilante primeiro</a>.
                    </div>
                    @endif
                    
                    @if($postos->isEmpty())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Não há postos de trabalho cadastrados. 
                        <a href="{{ route('admin.postos.create') }}" class="alert-link">Cadastre um posto primeiro</a>.
                    </div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Como usar:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Selecione o profissional e o posto de trabalho</li>
                            <li>Marque os dias da semana desejados</li>
                            <li>Para cada dia, você pode escolher um cartão programa específico (opcional)</li>
                            <li>Cada profissional pode ter apenas uma escala ativa por dia</li>
                        </ul>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.escalas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary" {{ ($usuarios->isEmpty() || $postos->isEmpty()) ? 'disabled' : '' }}>
                            <i class="fas fa-save"></i> Salvar Escalas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.dia-card {
    border: 2px solid #e3e6f0;
    transition: all 0.3s ease;
}

.dia-card.selected {
    border-color: #4e73df;
    background-color: #f8f9fc;
}

.dia-options {
    border-top: 1px solid #e3e6f0;
    padding-top: 10px;
    margin-top: 10px;
}
</style>

<script>
let cartoesPrograma = [];

function carregarCartoesPrograma() {
    const postoId = document.getElementById('posto_trabalho_id').value;
    
    if (!postoId) {
        // Limpar todos os selects de cartão
        document.querySelectorAll('.cartao-select').forEach(select => {
            select.innerHTML = '<option value="">Primeiro selecione um posto de trabalho</option>';
        });
        return;
    }
    
    // Mostrar loading em todos os selects
    document.querySelectorAll('.cartao-select').forEach(select => {
        select.innerHTML = '<option value="">Carregando...</option>';
    });
    
    // Buscar cartões programa via AJAX
    fetch(`/admin/cartoes-programa/por-posto/${postoId}`)
        .then(response => response.json())
        .then(data => {
            cartoesPrograma = data;
            
            // Atualizar todos os selects de cartão
            document.querySelectorAll('.cartao-select').forEach(select => {
                select.innerHTML = '<option value="">Nenhum cartão específico</option>';
                
                if (data.length === 0) {
                    select.innerHTML += '<option value="" disabled>Nenhum cartão disponível</option>';
                } else {
                    data.forEach(cartao => {
                        const option = document.createElement('option');
                        option.value = cartao.id;
                        option.textContent = `${cartao.nome} (${cartao.horario_inicio} - ${cartao.horario_fim})`;
                        select.appendChild(option);
                    });
                }
            });
        })
        .catch(error => {
            console.error('Erro ao carregar cartões programa:', error);
            document.querySelectorAll('.cartao-select').forEach(select => {
                select.innerHTML = '<option value="">Erro ao carregar cartões</option>';
            });
        });
}

function toggleDiaOptions(dia) {
    const checkbox = document.getElementById(`dia_${dia}`);
    const options = document.getElementById(`options_${dia}`);
    const card = checkbox.closest('.dia-card');
    
    if (checkbox.checked) {
        options.style.display = 'block';
        card.classList.add('selected');
    } else {
        options.style.display = 'none';
        card.classList.remove('selected');
        // Limpar seleção do cartão
        document.getElementById(`cartao_${dia}`).value = '';
    }
}

// Carregar cartões se há posto selecionado
document.addEventListener('DOMContentLoaded', function() {
    const postoSelect = document.getElementById('posto_trabalho_id');
    if (postoSelect.value) {
        carregarCartoesPrograma();
    }
});

// Validação do formulário
document.getElementById('form-escala').addEventListener('submit', function(e) {
    console.log('Formulário sendo enviado...');
    
    const diasSelecionados = document.querySelectorAll('.dia-checkbox:checked').length;
    console.log('Dias selecionados:', diasSelecionados);
    
    if (diasSelecionados === 0) {
        e.preventDefault();
        alert('Selecione pelo menos um dia da semana.');
        return false;
    }
    
    // Verificar dados do formulário
    const formData = new FormData(this);
    console.log('Dados do formulário:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    console.log('Formulário válido, enviando...');
});
</script>

@endsection 