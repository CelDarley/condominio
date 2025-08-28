@extends("layouts.app")

@section("title", "Pontos Base - " . $posto->nome)
@section("page-title", "Pontos Base do Posto: " . $posto->nome)

@section("content")
<div class="row">
    <div class="col-12">
        <!-- Informações do Posto -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-map-marker-alt"></i> {{ $posto->nome }}
                </h6>
                <div>
                    <a href="{{ route('admin.postos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar aos Postos
                    </a>
                    <a href="{{ route('admin.postos.pontos-base.create', $posto) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Ponto Base
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-1"><strong>Descrição:</strong> {{ $posto->descricao ?? 'Sem descrição' }}</p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge badge-{{ $posto->ativo ? 'success' : 'secondary' }}">
                                {{ $posto->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Total de Pontos Base:</strong> {{ $pontos->count() }}</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('admin.postos.edit', $posto) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Posto
                        </a>
                        <a href="{{ route('admin.postos.show', $posto) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Pontos Base -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-route"></i> Pontos Base do Itinerário
                </h6>
            </div>
            <div class="card-body">
                @if($pontos->count() > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Itinerário de Vigilância:</strong> Os pontos base definem o percurso que o vigilante deve seguir durante sua ronda neste posto.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Ordem</th>
                                    <th>Nome do Ponto</th>
                                    <th>Endereço</th>
                                    <th>Tempo Permanência</th>
                                    <th>Tempo Deslocamento</th>
                                    <th>Status</th>
                                    <th style="width: 120px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="pontos-sortable">
                                @foreach($pontos as $ponto)
                                <tr data-id="{{ $ponto->id }}">
                                    <td>
                                        <span class="badge badge-primary badge-lg">
                                            {{ $ponto->ordem }}º
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $ponto->nome }}</strong>
                                        @if($ponto->descricao)
                                            <br><small class="text-muted">{{ Str::limit($ponto->descricao, 40) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ Str::limit($ponto->endereco ?? 'Endereço não informado', 40) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <i class="fas fa-clock"></i> {{ $ponto->tempo_permanencia ?? 10 }}min
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">
                                            <i class="fas fa-route"></i> {{ $ponto->tempo_deslocamento ?? 5 }}min
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $ponto->ativo ? 'success' : 'secondary' }}">
                                            {{ $ponto->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-info btn-sm" 
                                                    title="Ver Detalhes"
                                                    onclick="mostrarDetalhes({{ $ponto->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning btn-sm" 
                                                    title="Editar"
                                                    onclick="editarPonto({{ $ponto->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                    title="Excluir"
                                                    onclick="excluirPonto({{ $ponto->id }}, '{{ $ponto->nome }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumo do Itinerário -->
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-left-primary">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-clock"></i> Tempo Total do Itinerário
                                        </h6>
                                        @php
                                            $tempoTotal = $pontos->sum(function($ponto) {
                                                return ($ponto->tempo_permanencia ?? 10) + ($ponto->tempo_deslocamento ?? 5);
                                            });
                                            $horas = floor($tempoTotal / 60);
                                            $minutos = $tempoTotal % 60;
                                        @endphp
                                        <div class="h4 mb-0 font-weight-bold text-primary-custom">
                                            {{ $horas }}h {{ $minutos }}min
                                        </div>
                                        <small class="text-muted">Tempo estimado para uma ronda completa</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-info">
                                    <div class="card-body">
                                        <h6 class="text-info">
                                            <i class="fas fa-route"></i> Estatísticas
                                        </h6>
                                        <ul class="mb-0 small">
                                            <li><strong>Total de Pontos:</strong> {{ $pontos->count() }}</li>
                                            <li><strong>Tempo Permanência:</strong> {{ $pontos->sum('tempo_permanencia') }}min</li>
                                            <li><strong>Tempo Deslocamento:</strong> {{ $pontos->sum('tempo_deslocamento') }}min</li>
                                            <li><strong>Pontos Ativos:</strong> {{ $pontos->where('ativo', true)->count() }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-warning">
                                    <div class="card-body">
                                        <h6 class="text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Orientações
                                        </h6>
                                        <ul class="mb-0 small">
                                            <li>Mantenha 3-5 pontos base por posto</li>
                                            <li>Defina tempos realistas</li>
                                            <li>Considere distâncias reais</li>
                                            <li>Teste antes de ativar</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-map-marked-alt fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">Nenhum Ponto Base Cadastrado</h5>
                        <p class="text-gray-500 mb-4">
                            Este posto ainda não possui pontos base definidos.<br>
                            Crie pontos base para estabelecer o itinerário de vigilância.
                        </p>
                        <a href="{{ route('admin.postos.pontos-base.create', $posto) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeiro Ponto Base
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalhes do Ponto Base -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">
                    <i class="fas fa-map-pin"></i> Detalhes do Ponto Base
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="pontoNome" class="mb-3 text-primary"></h6>
                <div id="qrCodeContainer">
                    <!-- Detalhes serão inseridos aqui -->
                </div>
                <p class="text-muted mt-3 small" id="qrCodeText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="editarPontoAtual()">
                    <i class="fas fa-edit"></i> Editar Ponto
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarDetalhes(pontoId) {
    // Buscar dados do ponto base e mostrar detalhes
    const pontos = @json($pontos);
    const ponto = pontos.find(p => p.id === pontoId);
    
    if (ponto) {
        pontoAtualId = pontoId; // Armazenar ID para edição
        
        const detalhes = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nome:</strong> ${ponto.nome}</p>
                    <p><strong>Endereço:</strong> ${ponto.endereco || 'Não informado'}</p>
                    <p><strong>Ordem na Sequência:</strong> ${ponto.ordem}º</p>
                    <p><strong>Status:</strong> <span class="badge badge-${ponto.ativo ? 'success' : 'secondary'}">${ponto.ativo ? 'Ativo' : 'Inativo'}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tempo de Permanência:</strong> <span class="badge badge-info">${ponto.tempo_permanencia || 10} minutos</span></p>
                    <p><strong>Tempo de Deslocamento:</strong> <span class="badge badge-warning">${ponto.tempo_deslocamento || 5} minutos</span></p>
                    <p><strong>Horário de Funcionamento:</strong> ${ponto.horario_inicio || '08:00'} - ${ponto.horario_fim || '18:00'}</p>
                    <p><strong>Tempo Total:</strong> <span class="badge badge-primary">${(ponto.tempo_permanencia || 10) + (ponto.tempo_deslocamento || 5)} min</span></p>
                </div>
            </div>
            <hr>
            ${ponto.descricao ? `<div class="mb-2"><strong>Descrição:</strong><br><span class="text-muted">${ponto.descricao}</span></div>` : ''}
            ${ponto.instrucoes ? `<div class="mb-2"><strong>Instruções para o Vigilante:</strong><br><span class="text-muted">${ponto.instrucoes}</span></div>` : ''}
        `;
        
        document.getElementById('pontoNome').textContent = ponto.nome;
        document.getElementById('qrCodeContainer').innerHTML = detalhes;
        document.getElementById('qrCodeText').textContent = '';
        
        new bootstrap.Modal(document.getElementById('qrCodeModal')).show();
    }
}

function editarPonto(id) {
    // Redirecionar para página de edição
    window.location.href = `/admin/postos/{{ $posto->id }}/pontos-base/${id}/edit`;
}

function excluirPonto(id, nome) {
    if (confirm(`Tem certeza que deseja excluir o ponto base "${nome}"?\n\nEsta ação não pode ser desfeita.`)) {
        // Criar form e submeter
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/postos/{{ $posto->id }}/pontos-base/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

let pontoAtualId = null;

function editarPontoAtual() {
    if (pontoAtualId) {
        editarPonto(pontoAtualId);
    }
}

// Sortable para reordenar pontos (opcional)
// Você pode integrar uma biblioteca como SortableJS se necessário
</script>

<style>
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.card-body .small {
    font-size: 0.875rem;
}
</style>
@endsection 