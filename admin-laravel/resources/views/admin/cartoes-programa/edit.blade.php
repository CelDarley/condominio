@extends("layouts.app")

@section("title", "Editar Cartão Programa")
@section("page-title", "Editar Cartão Programa")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Editar Cartão Programa: {{ $cartaoPrograma->nome }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cartoes-programa.update', $cartaoPrograma) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Cartão Programa <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome', $cartaoPrograma->nome) }}" 
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
                                        <option value="{{ $posto->id }}" {{ old('posto_trabalho_id', $cartaoPrograma->posto_trabalho_id) == $posto->id ? 'selected' : '' }}>
                                            {{ $posto->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('posto_trabalho_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($cartaoPrograma->posto_trabalho_id)
                                    <small class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Alterar o posto pode afetar os pontos base já configurados
                                    </small>
                                @endif
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
                                          placeholder="Descreva o objetivo e características deste cartão programa...">{{ old('descricao', $cartaoPrograma->descricao) }}</textarea>
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
                                       value="{{ old('horario_inicio', $cartaoPrograma->horario_inicio ? \Carbon\Carbon::parse($cartaoPrograma->horario_inicio)->format('H:i') : '') }}"
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
                                       value="{{ old('horario_fim', $cartaoPrograma->horario_fim ? \Carbon\Carbon::parse($cartaoPrograma->horario_fim)->format('H:i') : '') }}"
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
                                           {{ old('ativo', $cartaoPrograma->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Cartão programa ativo
                                    </label>
                                </div>
                                <small class="form-text text-muted">Cartões inativos não podem ser usados em escalas</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informações sobre pontos existentes -->
                    @if($cartaoPrograma->cartaoProgramaPontos->count() > 0)
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Pontos Base Configurados
                        </h6>
                        <p class="mb-2">Este cartão programa possui <strong>{{ $cartaoPrograma->cartaoProgramaPontos->count() }} pontos base</strong> configurados:</p>
                        <ul class="mb-2">
                            @foreach($cartaoPrograma->cartaoProgramaPontos->sortBy('ordem') as $ponto)
                                <li>{{ $ponto->ordem }}º - {{ $ponto->pontoBase->nome ?? 'Ponto removido' }}</li>
                            @endforeach
                        </ul>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb"></i> 
                            Acesse a página de detalhes do cartão para gerenciar os pontos base individualmente.
                        </small>
                    </div>
                    @endif
                    
                    @if($postos->isEmpty())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Não há postos de trabalho cadastrados. 
                        <a href="{{ route('admin.postos.create') }}" class="alert-link">Cadastre um posto primeiro</a>.
                    </div>
                    @endif
                    
                    <hr>
                    
                                         <div class="d-flex justify-content-between">
                         <div>
                             <a href="{{ route('admin.cartoes-programa.show', $cartaoPrograma) }}" class="btn btn-secondary">
                                 <i class="fas fa-arrow-left"></i> Voltar
                             </a>
                         </div>
                         <div>
                             <button type="submit" class="btn btn-primary" {{ $postos->isEmpty() ? 'disabled' : '' }}>
                                 <i class="fas fa-save"></i> Salvar Alterações
                             </button>
                             <a href="{{ route('admin.cartoes-programa.show', $cartaoPrograma) }}" class="btn btn-outline-secondary ml-2">
                                 <i class="fas fa-times"></i> Cancelar
                             </a>
                         </div>
                     </div>
                </form>
            </div>
        </div>
        
        <!-- Card de Ajuda -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-lightbulb"></i> Dicas para Editar o Cartão Programa
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-exclamation-triangle"></i> Cuidados ao Editar
                        </h6>
                        <ul class="small">
                            <li><strong>Posto de trabalho:</strong> Alterar pode remover pontos incompatíveis</li>
                            <li><strong>Horários:</strong> Verifique se são compatíveis com as escalas existentes</li>
                            <li><strong>Status ativo:</strong> Cartões inativos não aparecem em novas escalas</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-cogs"></i> Após a Edição
                        </h6>
                        <ul class="small">
                            <li><strong>Revise os pontos:</strong> Verifique se todos os pontos ainda são válidos</li>
                            <li><strong>Teste a sequência:</strong> Confirme que a ordem faz sentido</li>
                            <li><strong>Atualize escalas:</strong> Escalas existentes podem precisar de ajustes</li>
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
    
    // Aviso ao alterar posto de trabalho
    const postoSelect = document.getElementById('posto_trabalho_id');
    const postoOriginal = postoSelect.value;
    
    postoSelect.addEventListener('change', function() {
        if (this.value !== postoOriginal && {{ $cartaoPrograma->cartaoProgramaPontos->count() }}) {
            if (!confirm('Alterar o posto de trabalho pode afetar os pontos base já configurados. Deseja continuar?')) {
                this.value = postoOriginal;
            }
        }
    });
});
</script>

<style>
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endsection 