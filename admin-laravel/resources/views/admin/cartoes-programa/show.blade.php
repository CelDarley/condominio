@extends("layouts.app")

@section("title", "Cartão Programa - " . $cartaoPrograma->nome)
@section("page-title", "Detalhes do Cartão Programa")

@section("content")
<div class="row">
    <div class="col-12">
        <!-- Cabeçalho com informações principais -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-id-card"></i> {{ $cartaoPrograma->nome }}
                </h6>
                <div>
                    <a href="{{ route('admin.cartoes-programa.edit', $cartaoPrograma) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('admin.cartoes-programa.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-info-circle"></i> Informações Básicas</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="150"><strong>ID:</strong></td>
                                <td><span class="badge badge-primary">{{ $cartaoPrograma->id }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td>{{ $cartaoPrograma->nome }}</td>
                            </tr>
                            <tr>
                                <td><strong>Posto de Trabalho:</strong></td>
                                <td>{{ $cartaoPrograma->postoTrabalho->nome ?? 'Não definido' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($cartaoPrograma->ativo)
                                        <span class="badge badge-success">Ativo</span>
                                    @else
                                        <span class="badge badge-secondary">Inativo</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-clock"></i> Horários</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="150"><strong>Início:</strong></td>
                                <td>{{ $cartaoPrograma->horario_inicio ? \Carbon\Carbon::parse($cartaoPrograma->horario_inicio)->format('H:i') : 'Não definido' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fim:</strong></td>
                                <td>{{ $cartaoPrograma->horario_fim ? \Carbon\Carbon::parse($cartaoPrograma->horario_fim)->format('H:i') : 'Não definido' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Duração:</strong></td>
                                <td>
                                    @if($cartaoPrograma->horario_inicio && $cartaoPrograma->horario_fim)
                                        @php
                                            $duracao = $cartaoPrograma->getDuracaoTurno();
                                            $horas = intval($duracao / 60);
                                            $minutos = $duracao % 60;
                                            $turnoNoturno = $cartaoPrograma->horario_fim <= $cartaoPrograma->horario_inicio;
                                        @endphp
                                        {{ $horas }}h {{ $minutos }}min
                                        @if($turnoNoturno)
                                            <span class="badge badge-info ml-2">
                                                <i class="fas fa-moon"></i> Turno noturno
                                            </span>
                                        @endif
                                    @else
                                        Não calculado
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Criado em:</strong></td>
                                <td>{{ $cartaoPrograma->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($cartaoPrograma->descricao)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary"><i class="fas fa-file-text"></i> Descrição</h6>
                        <p class="text-muted">{{ $cartaoPrograma->descricao }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Pontos Base do Cartão -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-map-marker-alt"></i> Sequência de Pontos Base
                </h6>
                <div>
                    <span class="badge badge-info">{{ $cartaoPrograma->cartaoProgramaPontos->count() }} pontos</span>
                    @if($tempoTotalItinerario > 0)
                        <span class="badge badge-success ml-2">
                            <i class="fas fa-clock"></i> {{ $tempoTotalItinerario }} min total
                        </span>
                    @endif
                    <button type="button" class="btn btn-success btn-sm ml-2" id="btn-salvar-ordem" style="display: none;">
                        <i class="fas fa-save"></i> Salvar Nova Ordem
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($cartaoPrograma->cartaoProgramaPontos->count() > 0)
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Dica:</strong> Você pode reordenar os pontos base arrastando e soltando as linhas da tabela. 
                        Use o ícone <i class="fas fa-grip-vertical"></i> para arrastar. Clique em "Salvar Nova Ordem" após fazer as alterações.
                    </div>
                    @if($tempoTotalItinerario > 0)
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="card border-left-success">
                                    <div class="card-body py-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase">Tempo nos Pontos</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $tempoTotalPermanencia }} minutos</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-warning">
                                    <div class="card-body py-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase">Tempo de Itinerário</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $tempoTotalDeslocamento }} minutos</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-primary">
                                    <div class="card-body py-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase">Tempo Total</div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $tempoTotalItinerario }} minutos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">Ordem</th>
                                    <th>Ponto Base</th>
                                    <th>Tempo no Ponto</th>
                                    <th>
                                        <i class="fas fa-route text-primary"></i> Itinerário
                                        <br><small class="text-muted font-weight-normal">Próximo ponto e tempo</small>
                                    </th>
                                    <th>Instruções</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="pontos-sortable">
                                @foreach($cartaoPrograma->cartaoProgramaPontos->sortBy('ordem') as $ponto)
                                <tr data-id="{{ $ponto->id }}" data-ordem="{{ $ponto->ordem }}" class="ponto-item">
                                    <td>
                                        <span class="badge badge-primary">{{ $ponto->ordem }}</span>
                                        <i class="fas fa-grip-vertical text-muted ml-1" style="cursor: grab;"></i>
                                    </td>
                                    <td>
                                        <strong>{{ $ponto->pontoBase->nome ?? 'Ponto removido' }}</strong>
                                        @if($ponto->pontoBase)
                                            <br><small class="text-muted">{{ $ponto->pontoBase->descricao }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ponto->tempo_permanencia)
                                            <span class="badge badge-success">{{ $ponto->tempo_permanencia }} min</span>
                                        @else
                                            <span class="text-muted">Não definido</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $proximoPonto = $cartaoPrograma->cartaoProgramaPontos->sortBy('ordem')->where('ordem', '>', $ponto->ordem)->first();
                                        @endphp
                                        
                                        @if($proximoPonto)
                                            <div class="small">
                                                <i class="fas fa-arrow-right text-primary"></i>
                                                <strong>{{ $proximoPonto->pontoBase->nome ?? 'Ponto removido' }}</strong>
                                            </div>
                                            @if($ponto->tempo_deslocamento)
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-route"></i> {{ $ponto->tempo_deslocamento }} min de itinerário
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-question"></i> Tempo não definido
                                                </span>
                                            @endif
                                        @else
                                            <div class="text-center">
                                                <span class="badge badge-success">
                                                    <i class="fas fa-flag-checkered"></i> Último ponto
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ponto->instrucoes_especificas)
                                            <small>{{ Str::limit($ponto->instrucoes_especificas, 50) }}</small>
                                        @else
                                            <span class="text-muted">Nenhuma</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" title="Editar ponto" onclick="editarPonto({{ $ponto->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" title="Remover ponto" onclick="removerPonto({{ $ponto->id }}, '{{ $ponto->pontoBase->nome ?? 'Ponto' }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Resumo dos tempos -->
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Tempo Total de Permanência
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $cartaoPrograma->cartaoProgramaPontos->sum('tempo_permanencia') }} min
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-warning">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Tempo Total de Deslocamento
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $cartaoPrograma->cartaoProgramaPontos->sum('tempo_deslocamento') }} min
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Tempo Total Estimado
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $cartaoPrograma->cartaoProgramaPontos->sum('tempo_permanencia') + $cartaoPrograma->cartaoProgramaPontos->sum('tempo_deslocamento') }} min
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum ponto base cadastrado</h5>
                        <p class="text-muted">Este cartão programa ainda não possui pontos base em sua sequência.</p>
                        <button class="btn btn-primary" onclick="mostrarPontosDisponiveis()">
                            <i class="fas fa-plus"></i> Adicionar Primeiro Ponto
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pontos Base Disponíveis -->
        @if($pontosDisponiveis->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus-circle"></i> Pontos Base Disponíveis para Adicionar
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Pontos base do posto de trabalho que ainda não foram adicionados a este cartão programa:</p>
                <div class="row">
                    @foreach($pontosDisponiveis as $ponto)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-info">
                            <div class="card-body py-2">
                                <strong>{{ $ponto->nome }}</strong>
                                @if($ponto->descricao)
                                    <br><small class="text-muted">{{ Str::limit($ponto->descricao, 40) }}</small>
                                @endif
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="abrirModalAdicionarPonto({{ $ponto->id }}, '{{ $ponto->nome }}')">
                                        <i class="fas fa-plus"></i> Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal para Adicionar Ponto -->
<div class="modal fade" id="modalAdicionarPonto" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarPontoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAdicionarPontoLabel">
                    <i class="fas fa-plus-circle"></i> Adicionar Ponto Base
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="formAdicionarPonto" method="POST" action="{{ route('admin.cartoes-programa.adicionar-ponto', $cartaoPrograma) }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="ponto_base_id" name="ponto_base_id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Ponto selecionado:</strong> <span id="nomePontoSelecionado"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tempo_permanencia" class="form-label">Tempo de Permanência <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           id="tempo_permanencia" 
                                           name="tempo_permanencia" 
                                           min="1" 
                                           max="120" 
                                           value="5"
                                           required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Quanto tempo o vigilante deve permanecer neste ponto</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tempo_deslocamento" class="form-label">Tempo de Deslocamento <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           id="tempo_deslocamento" 
                                           name="tempo_deslocamento" 
                                           min="1" 
                                           max="60" 
                                           value="2"
                                           required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Tempo para se deslocar até o próximo ponto</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="instrucoes_especificas" class="form-label">Instruções Específicas</label>
                                <textarea class="form-control" 
                                          id="instrucoes_especificas" 
                                          name="instrucoes_especificas" 
                                          rows="3"
                                          placeholder="Ex: Verificar fechaduras, anotar movimentação, conferir iluminação..."></textarea>
                                <small class="form-text text-muted">Instruções específicas para este ponto (opcional)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="obrigatorio" 
                                       name="obrigatorio"
                                       checked>
                                <label class="form-check-label" for="obrigatorio">
                                    Ponto obrigatório
                                </label>
                                <small class="form-text text-muted">Se marcado, este ponto não pode ser pulado durante a ronda</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Adicionar Ponto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Ponto -->
<div class="modal fade" id="modalEditarPonto" tabindex="-1" role="dialog" aria-labelledby="modalEditarPontoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarPontoLabel">
                    <i class="fas fa-edit"></i> Editar Ponto Base
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="formEditarPonto" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <input type="hidden" id="edit_ponto_id" name="ponto_id">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-edit"></i>
                        <strong>Editando ponto:</strong> <span id="nomeEditandoPonto"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tempo_permanencia" class="form-label">Tempo de Permanência <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           id="edit_tempo_permanencia" 
                                           name="tempo_permanencia" 
                                           min="1" 
                                           max="120"
                                           required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Quanto tempo o vigilante deve permanecer neste ponto</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tempo_deslocamento" class="form-label">Tempo de Deslocamento <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           id="edit_tempo_deslocamento" 
                                           name="tempo_deslocamento" 
                                           min="1" 
                                           max="60"
                                           required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Tempo para se deslocar até o próximo ponto</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_instrucoes_especificas" class="form-label">Instruções Específicas</label>
                                <textarea class="form-control" 
                                          id="edit_instrucoes_especificas" 
                                          name="instrucoes_especificas" 
                                          rows="3"
                                          placeholder="Ex: Verificar fechaduras, anotar movimentação, conferir iluminação..."></textarea>
                                <small class="form-text text-muted">Instruções específicas para este ponto (opcional)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="edit_obrigatorio" 
                                       name="obrigatorio">
                                <label class="form-check-label" for="edit_obrigatorio">
                                    Ponto obrigatório
                                </label>
                                <small class="form-text text-muted">Se marcado, este ponto não pode ser pulado durante a ronda</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalAdicionarPonto(pontoId, nomePonto) {
    console.log('Abrindo modal para ponto:', {pontoId: pontoId, nome: nomePonto});
    
    // Verificar se os elementos existem
    const pontoInput = document.getElementById('ponto_base_id');
    const nomeSpan = document.getElementById('nomePontoSelecionado');
    const modalElement = document.getElementById('modalAdicionarPonto');
    
    if (!pontoInput || !nomeSpan || !modalElement) {
        console.error('Elementos do modal não encontrados:', {
            pontoInput: !!pontoInput,
            nomeSpan: !!nomeSpan,
            modalElement: !!modalElement
        });
        alert('Erro: Elementos do modal não encontrados. Recarregue a página.');
        return;
    }
    
    pontoInput.value = pontoId;
    nomeSpan.textContent = nomePonto;
    
    console.log('Valores definidos no modal:', {
        pontoBaseId: pontoInput.value,
        nomePonto: nomeSpan.textContent
    });
    
    // Usar Bootstrap 5 Modal API
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function mostrarPontosDisponiveis() {
    // Tentar encontrar a seção de pontos disponíveis
    const headers = document.querySelectorAll('h6');
    let pontosSection = null;
    
    headers.forEach(header => {
        if (header.textContent.includes('Pontos Base Disponíveis')) {
            pontosSection = header.closest('.card');
        }
    });
    
    if (pontosSection) {
        pontosSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // Destacar a seção brevemente
        pontosSection.style.boxShadow = '0 0 20px rgba(0,123,255,0.5)';
        setTimeout(() => {
            pontosSection.style.boxShadow = '';
        }, 2000);
    } else {
        alert('Não há pontos base disponíveis para adicionar. Verifique se o posto de trabalho possui pontos base cadastrados.');
    }
}

function removerPonto(pontoId, nomePonto) {
    if (confirm(`Tem certeza que deseja remover o ponto "${nomePonto}" da sequência?\n\nEsta ação não pode ser desfeita e irá reordenar automaticamente os pontos restantes.`)) {
        // Validar se tem token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Erro: Token de segurança não encontrado. Recarregue a página e tente novamente.');
            return;
        }
        
        // Criar form e submeter
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/cartoes-programa/{{ $cartaoPrograma->id }}/remover-ponto/${pontoId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken.getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function editarPonto(pontoId) {
    console.log('Editando ponto ID:', pontoId);
    
    // Buscar dados do ponto via AJAX
    fetch(`/admin/cartoes-programa/{{ $cartaoPrograma->id }}/ponto/${pontoId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao buscar dados do ponto');
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados do ponto:', data);
            
            // Preencher o formulário com os dados atuais
            document.getElementById('edit_ponto_id').value = data.id;
            document.getElementById('nomeEditandoPonto').textContent = data.ponto_base.nome;
            document.getElementById('edit_tempo_permanencia').value = data.tempo_permanencia;
            document.getElementById('edit_tempo_deslocamento').value = data.tempo_deslocamento;
            document.getElementById('edit_instrucoes_especificas').value = data.instrucoes_especificas || '';
            document.getElementById('edit_obrigatorio').checked = data.obrigatorio;
            
            // Definir a action do formulário
            const form = document.getElementById('formEditarPonto');
            form.action = `/admin/cartoes-programa/{{ $cartaoPrograma->id }}/editar-ponto/${pontoId}`;
            
            // Abrir o modal
            const modalElement = document.getElementById('modalEditarPonto');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        })
        .catch(error => {
            console.error('Erro ao carregar dados do ponto:', error);
            alert('Erro ao carregar dados do ponto. Tente novamente.');
        });
}

// Validação dos formulários
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário de adicionar ponto
    const formAdicionar = document.getElementById('formAdicionarPonto');
    
    if (formAdicionar) {
        formAdicionar.addEventListener('submit', function(e) {
            console.log('Formulário de adicionar sendo enviado...');
            
            // Verificar se o ponto_base_id foi definido
            const pontoBaseId = document.getElementById('ponto_base_id').value;
            if (!pontoBaseId) {
                e.preventDefault();
                alert('Erro: Ponto base não selecionado. Feche o modal e tente novamente.');
                console.error('ponto_base_id não definido:', pontoBaseId);
                return;
            }
            
            const tempoPermanencia = parseInt(document.getElementById('tempo_permanencia').value);
            const tempoDeslocamento = parseInt(document.getElementById('tempo_deslocamento').value);
            
            if (isNaN(tempoPermanencia) || tempoPermanencia < 1 || tempoPermanencia > 120) {
                e.preventDefault();
                alert('O tempo de permanência deve estar entre 1 e 120 minutos.');
                return;
            }
            
            if (isNaN(tempoDeslocamento) || tempoDeslocamento < 1 || tempoDeslocamento > 60) {
                e.preventDefault();
                alert('O tempo de deslocamento deve estar entre 1 e 60 minutos.');
                return;
            }
            
            console.log('Validação passou, enviando formulário...');
        });
    }
    
    // Validação do formulário de editar ponto
    const formEditar = document.getElementById('formEditarPonto');
    
    if (formEditar) {
        formEditar.addEventListener('submit', function(e) {
            console.log('Formulário de editar sendo enviado...');
            
            const tempoPermanencia = parseInt(document.getElementById('edit_tempo_permanencia').value);
            const tempoDeslocamento = parseInt(document.getElementById('edit_tempo_deslocamento').value);
            
            console.log('Dados do formulário de edição:', {
                tempoPermanencia: tempoPermanencia,
                tempoDeslocamento: tempoDeslocamento
            });
            
            if (isNaN(tempoPermanencia) || tempoPermanencia < 1 || tempoPermanencia > 120) {
                e.preventDefault();
                alert('O tempo de permanência deve estar entre 1 e 120 minutos.');
                return;
            }
            
            if (isNaN(tempoDeslocamento) || tempoDeslocamento < 1 || tempoDeslocamento > 60) {
                e.preventDefault();
                alert('O tempo de deslocamento deve estar entre 1 e 60 minutos.');
                return;
            }
            
            console.log('Validação da edição passou, enviando formulário...');
        });
    }
    
    // Implementar funcionalidade de arrastar e soltar para reordenar pontos
    implementarDragAndDrop();
});

// Função para implementar arrastar e soltar
function implementarDragAndDrop() {
    const tbody = document.getElementById('pontos-sortable');
    const btnSalvar = document.getElementById('btn-salvar-ordem');
    let draggedElement = null;
    let originalOrder = [];
    
    if (!tbody || !btnSalvar) return;
    
    // Armazenar ordem original
    function armazenarOrdemOriginal() {
        originalOrder = Array.from(tbody.querySelectorAll('tr')).map(tr => ({
            id: tr.dataset.id,
            ordem: parseInt(tr.dataset.ordem)
        }));
    }
    
    // Atualizar números de ordem na interface
    function atualizarNumerosOrdem() {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            const badge = row.querySelector('.badge-primary');
            if (badge) {
                badge.textContent = index + 1;
            }
            row.dataset.ordem = index + 1;
        });
    }
    
    // Mostrar botão de salvar quando houver mudanças
    function mostrarBotaoSalvar() {
        btnSalvar.style.display = 'inline-block';
        btnSalvar.classList.add('btn-warning');
        btnSalvar.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Salvar Nova Ordem';
    }
    
    // Verificar se houve mudanças na ordem
    function verificarMudancas() {
        const novaOrdem = Array.from(tbody.querySelectorAll('tr')).map(tr => ({
            id: tr.dataset.id,
            ordem: parseInt(tr.dataset.ordem)
        }));
        
        const mudou = JSON.stringify(originalOrder) !== JSON.stringify(novaOrdem);
        
        if (mudou) {
            mostrarBotaoSalvar();
        } else {
            btnSalvar.style.display = 'none';
        }
        
        return mudou;
    }
    
    // Configurar eventos de arrastar e soltar
    tbody.querySelectorAll('tr').forEach(row => {
        row.draggable = true;
        
        row.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
        });
        
        row.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
            draggedElement = null;
        });
        
        row.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });
        
        row.addEventListener('dragenter', function(e) {
            e.preventDefault();
            if (this !== draggedElement) {
                this.style.borderTop = '2px solid #007bff';
            }
        });
        
        row.addEventListener('dragleave', function(e) {
            this.style.borderTop = '';
        });
        
        row.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderTop = '';
            
            if (draggedElement && this !== draggedElement) {
                const allRows = Array.from(tbody.querySelectorAll('tr'));
                const draggedIndex = allRows.indexOf(draggedElement);
                const dropIndex = allRows.indexOf(this);
                
                if (draggedIndex < dropIndex) {
                    tbody.insertBefore(draggedElement, this.nextSibling);
                } else {
                    tbody.insertBefore(draggedElement, this);
                }
                
                atualizarNumerosOrdem();
                verificarMudancas();
            }
        });
    });
    
    // Evento de clique no botão salvar
    btnSalvar.addEventListener('click', function() {
        const novaOrdem = Array.from(tbody.querySelectorAll('tr')).map((row, index) => ({
            id: row.dataset.id,
            nova_ordem: index + 1
        }));
        
        // Mostrar loading
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
        
        // Enviar nova ordem para o servidor
        fetch(`{{ route('admin.cartoes-programa.reordenar-pontos', $cartaoPrograma) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ pontos: novaOrdem })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar ordem original
                armazenarOrdemOriginal();
                
                // Mostrar sucesso
                this.classList.remove('btn-warning');
                this.classList.add('btn-success');
                this.innerHTML = '<i class="fas fa-check"></i> Ordem Salva!';
                
                // Esconder botão após 2 segundos
                setTimeout(() => {
                    this.style.display = 'none';
                }, 2000);
                
                // Mostrar mensagem de sucesso
                mostrarMensagem('Ordem dos pontos base atualizada com sucesso!', 'success');
            } else {
                throw new Error(data.message || 'Erro ao salvar ordem');
            }
        })
        .catch(error => {
            console.error('Erro ao salvar ordem:', error);
            
            // Restaurar ordem original
            restaurarOrdemOriginal();
            
            // Mostrar erro
            this.classList.remove('btn-warning');
            this.classList.add('btn-danger');
            this.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erro!';
            
            // Restaurar botão após 2 segundos
            setTimeout(() => {
                this.classList.remove('btn-danger');
                this.classList.add('btn-warning');
                this.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Salvar Nova Ordem';
            }, 2000);
            
            mostrarMensagem('Erro ao salvar ordem. Tente novamente.', 'danger');
        });
    });
    
    // Função para restaurar ordem original
    function restaurarOrdemOriginal() {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const rowsOrdenados = originalOrder.map(item => 
            rows.find(row => row.dataset.id == item.id)
        ).filter(Boolean);
        
        rowsOrdenados.forEach(row => tbody.appendChild(row));
        atualizarNumerosOrdem();
        btnSalvar.style.display = 'none';
    }
    
    // Função para mostrar mensagens
    function mostrarMensagem(texto, tipo) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${texto}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Inicializar
    armazenarOrdemOriginal();
}
</script>

<style>
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

/* Estilos para arrastar e soltar */
.ponto-item {
    transition: all 0.2s ease;
}

.ponto-item:hover {
    background-color: #f8f9fa;
}

.ponto-item[draggable="true"] {
    cursor: grab;
}

.ponto-item[draggable="true"]:active {
    cursor: grabbing;
}

.ponto-item.dragging {
    opacity: 0.5;
    transform: rotate(2deg);
}

.ponto-item.drag-over {
    border-top: 2px solid #007bff !important;
}

/* Estilos para o botão de salvar ordem */
#btn-salvar-ordem {
    transition: all 0.3s ease;
}

#btn-salvar-ordem:disabled {
    opacity: 0.7;
    cursor: not-allowed;
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