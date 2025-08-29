@extends("layouts.app")

@section("title", "Editar Escala")
@section("page-title", "Editar Escala")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Editar Escala
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.escalas.update', $escala) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usuario_id" class="form-label">Vigilante <span class="text-danger">*</span></label>
                                <select class="form-control @error('usuario_id') is-invalid @enderror" 
                                        id="usuario_id" 
                                        name="usuario_id" 
                                        required>
                                    <option value="">Selecione um vigilante</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}" {{ (old('usuario_id') ?? $escala->usuario_id) == $usuario->id ? 'selected' : '' }}>
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
                                        <option value="{{ $posto->id }}" {{ (old('posto_trabalho_id') ?? $escala->posto_trabalho_id) == $posto->id ? 'selected' : '' }}>
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
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="cartao_programa_id" class="form-label">Cartão Programa</label>
                                <select class="form-control @error('cartao_programa_id') is-invalid @enderror" 
                                        id="cartao_programa_id" 
                                        name="cartao_programa_id">
                                    <option value="">Carregando...</option>
                                </select>
                                @error('cartao_programa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Opcional: Selecione um cartão programa específico para esta escala. Se não selecionado, pode ser definido posteriormente.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dia_semana" class="form-label">Dia da Semana <span class="text-danger">*</span></label>
                                <select class="form-control @error('dia_semana') is-invalid @enderror" 
                                        id="dia_semana" 
                                        name="dia_semana" 
                                        required>
                                    <option value="">Selecione o dia</option>
                                    @foreach($diasSemana as $numero => $nome)
                                        <option value="{{ $numero }}" {{ (old('dia_semana') ?? $escala->dia_semana) == $numero ? 'selected' : '' }}>
                                            {{ $nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dia_semana')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="ativo" 
                                           name="ativo" 
                                           {{ (old('ativo') ?? $escala->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Escala ativa
                                    </label>
                                </div>
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
                        <strong>Observação:</strong> Cada vigilante pode ter apenas uma escala ativa por dia da semana.
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.escalas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <div>
                            <button type="submit" class="btn btn-primary" {{ ($usuarios->isEmpty() || $postos->isEmpty()) ? 'disabled' : '' }}>
                                <i class="fas fa-save"></i> Atualizar Escala
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function carregarCartoesPrograma() {
    const postoId = document.getElementById('posto_trabalho_id').value;
    const cartaoSelect = document.getElementById('cartao_programa_id');
    const cartaoAtual = {{ $escala->cartao_programa_id ?? 'null' }};
    
    // Limpar opções existentes
    cartaoSelect.innerHTML = '<option value="">Carregando...</option>';
    
    if (!postoId) {
        cartaoSelect.innerHTML = '<option value="">Primeiro selecione um posto de trabalho</option>';
        return;
    }
    
    // Buscar cartões programa via AJAX
    fetch(`/admin/cartoes-programa/por-posto/${postoId}`)
        .then(response => response.json())
        .then(data => {
            cartaoSelect.innerHTML = '<option value="">Nenhum cartão programa específico</option>';
            
            if (data.length === 0) {
                cartaoSelect.innerHTML += '<option value="" disabled>Nenhum cartão programa disponível para este posto</option>';
            } else {
                data.forEach(cartao => {
                    const option = document.createElement('option');
                    option.value = cartao.id;
                    option.textContent = `${cartao.nome} (${cartao.horario_inicio} - ${cartao.horario_fim})`;
                    
                    // Selecionar o cartão atual se for o caso
                    if (cartao.id == cartaoAtual) {
                        option.selected = true;
                    }
                    
                    cartaoSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Erro ao carregar cartões programa:', error);
            cartaoSelect.innerHTML = '<option value="">Erro ao carregar cartões programa</option>';
        });
}

// Carregar cartões programa ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    const postoSelect = document.getElementById('posto_trabalho_id');
    if (postoSelect.value) {
        carregarCartoesPrograma();
    }
});
</script>

@endsection 