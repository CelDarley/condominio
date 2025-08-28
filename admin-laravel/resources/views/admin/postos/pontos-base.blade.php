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
                    <i class="fas fa-map-pin"></i> Pontos Base do Posto
                </h6>
            </div>
            <div class="card-body">
                                @if($pontos->count() > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Pontos Base:</strong> Os pontos base definem os locais físicos onde o vigilante deve fazer verificações durante sua ronda neste posto.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome do Ponto</th>
                                    <th>Endereço</th>
                                    <th>Descrição</th>
                                    <th>Status</th>
                                    <th style="width: 120px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="pontos-sortable">
                                @foreach($pontos as $ponto)
                                <tr data-id="{{ $ponto->id }}">
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
                                        <span class="text-muted">
                                            {{ Str::limit($ponto->descricao ?? 'Sem descrição', 50) }}
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
                            <div class="col-md-6">
                                <div class="card border-left-primary">
                                    <div class="card-body">
                                        <h6 class="text-primary">
                                            <i class="fas fa-map-marked-alt"></i> Resumo do Itinerário
                                        </h6>
                                        <div class="h4 mb-0 font-weight-bold text-primary-custom">
                                            {{ $pontos->count() }} Pontos Base
                                        </div>
                                        <small class="text-muted">Total de pontos na sequência de vigilância</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-left-info">
                                    <div class="card-body">
                                        <h6 class="text-info">
                                            <i class="fas fa-route"></i> Estatísticas
                                        </h6>
                                        <ul class="mb-0 small">
                                            <li><strong>Total de Pontos:</strong> {{ $pontos->count() }}</li>
                                            <li><strong>Pontos Ativos:</strong> {{ $pontos->where('ativo', true)->count() }}</li>
                                            <li><strong>Pontos Inativos:</strong> {{ $pontos->where('ativo', false)->count() }}</li>
                                            <li><strong>Com Coordenadas GPS:</strong> {{ $pontos->whereNotNull('latitude')->whereNotNull('longitude')->count() }}</li>
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
                    <p><strong>Status:</strong> <span class="badge badge-${ponto.ativo ? 'success' : 'secondary'}">${ponto.ativo ? 'Ativo' : 'Inativo'}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Coordenadas GPS:</strong>
                        ${ponto.latitude && ponto.longitude ? `${ponto.latitude}, ${ponto.longitude}` : 'Não informadas'}
                    </p>
                    <p><strong>Status:</strong> <span class="badge badge-${ponto.ativo ? 'success' : 'secondary'}">${ponto.ativo ? 'Ativo' : 'Inativo'}</span></p>
                </div>
            </div>
            <hr>
            ${ponto.descricao ? `<div class="mb-2"><strong>Descrição:</strong><br><span class="text-muted">${ponto.descricao}</span></div>` : ''}
        `;

        document.getElementById('pontoNome').textContent = ponto.nome;
        document.getElementById('qrCodeContainer').innerHTML = detalhes;
        document.getElementById('qrCodeText').textContent = '';

        new bootstrap.Modal(document.getElementById('qrCodeModal')).show();
    }
}

function editarPonto(id) {
    // Redirecionar para página de edição
    window.location.href = `{{ route('admin.postos.pontos-base.edit', ['posto' => $posto->id, 'ponto' => '__ID__']) }}`.replace('__ID__', id);
}

function excluirPonto(id, nome) {
    if (confirm(`Tem certeza que deseja desativar o ponto base "${nome}"?\n\nEsta ação irá desativar o ponto base, mas ele não será excluído permanentemente.`)) {
        // Criar form e submeter
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.postos.pontos-base.destroy', ['posto' => $posto->id, 'ponto' => '__ID__']) }}`.replace('__ID__', id);

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
