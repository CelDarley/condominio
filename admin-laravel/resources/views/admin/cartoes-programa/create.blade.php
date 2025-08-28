@extends("layouts.app")

@section("title", "Novo Cartão Programa")
@section("page-title", "Novo Cartão Programa")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-id-card"></i> Cadastrar Novo Cartão Programa
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cartoes-programa.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Cartão Programa <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}" 
                                       required 
                                       placeholder="Ex: Ronda Noturna - Portaria Principal">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Nome identificador do cartão programa</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="posto_trabalho_id" class="form-label">Posto de Trabalho <span class="text-danger">*</span></label>
                                <select class="form-control @error('posto_trabalho_id') is-invalid @enderror" 
                                        id="posto_trabalho_id" 
                                        name="posto_trabalho_id" 
                                        required>
                                    <option value="">Selecione o posto</option>
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
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                          id="descricao" 
                                          name="descricao" 
                                          rows="3"
                                          placeholder="Descreva o objetivo e características deste cartão programa...">{{ old('descricao') }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Configurações de Horário -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-warning mb-3">
                                <div class="card-body">
                                    <h6 class="text-warning">
                                        <i class="fas fa-clock"></i> Configurações de Horário
                                    </h6>
                                    <p class="mb-2 text-muted small">Define o período de funcionamento deste cartão programa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="horario_inicio" class="form-label">Horário de Início <span class="text-danger">*</span></label>
                                <input type="time" 
                                       class="form-control @error('horario_inicio') is-invalid @enderror" 
                                       id="horario_inicio" 
                                       name="horario_inicio" 
                                       value="{{ old('horario_inicio', '08:00') }}"
                                       required>
                                @error('horario_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Quando inicia o período de atividade</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="horario_fim" class="form-label">Horário de Fim <span class="text-danger">*</span></label>
                                <input type="time" 
                                       class="form-control @error('horario_fim') is-invalid @enderror" 
                                       id="horario_fim" 
                                       name="horario_fim" 
                                       value="{{ old('horario_fim', '18:00') }}"
                                       required>
                                @error('horario_fim')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Quando termina o período de atividade</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="ativo" 
                                           name="ativo" 
                                           {{ old('ativo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Cartão programa ativo
                                    </label>
                                </div>
                                <small class="form-text text-muted">Cartões inativos não podem ser usados em escalas</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informações sobre próximos passos -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Próximos Passos
                        </h6>
                        <p class="mb-2">Após criar o cartão programa, você poderá:</p>
                        <ul class="mb-0">
                            <li><strong>Adicionar pontos base</strong> à sequência do cartão</li>
                            <li><strong>Definir tempos</strong> de permanência e deslocamento para cada ponto</li>
                            <li><strong>Configurar instruções específicas</strong> para cada ponto na sequência</li>
                            <li><strong>Reordenar pontos</strong> para otimizar o itinerário</li>
                        </ul>
                    </div>
                    
                    @if($postos->isEmpty())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Não há postos de trabalho cadastrados. 
                        <a href="{{ route('admin.postos.create') }}" class="alert-link">Cadastre um posto primeiro</a>.
                    </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.cartoes-programa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary" {{ $postos->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-save"></i> Criar Cartão Programa
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Card de Ajuda -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-lightbulb"></i> Dicas para Criar um Bom Cartão Programa
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-check-circle"></i> Boas Práticas
                        </h6>
                        <ul class="small">
                            <li><strong>Nome descritivo:</strong> Use nomes que identifiquem claramente o propósito</li>
                            <li><strong>Horários realistas:</strong> Considere o tempo real de operação</li>
                            <li><strong>Descrição detalhada:</strong> Explique o objetivo do cartão programa</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-clock"></i> Exemplos de Cartões
                        </h6>
                        <ul class="small">
                            <li><strong>Ronda Noturna:</strong> 22:00 - 06:00</li>
                            <li><strong>Verificação Diurna:</strong> 08:00 - 18:00</li>
                            <li><strong>Ronda Expressa:</strong> 06:00 - 22:00</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação de horários
    const horaInicio = document.getElementById('horario_inicio');
    const horaFim = document.getElementById('horario_fim');
    
    function validarHorarios() {
        const inicio = horaInicio.value;
        const fim = horaFim.value;
        
        if (inicio && fim && inicio >= fim) {
            horaFim.setCustomValidity('O horário de fim deve ser posterior ao horário de início');
        } else {
            horaFim.setCustomValidity('');
        }
    }
    
    horaInicio.addEventListener('change', validarHorarios);
    horaFim.addEventListener('change', validarHorarios);
});
</script>

<style>
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endsection 