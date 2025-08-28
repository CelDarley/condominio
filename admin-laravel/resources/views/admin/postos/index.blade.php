@extends("layouts.app")

@section("title", "Postos de Trabalho")
@section("page-title", "Postos de Trabalho")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Postos de Trabalho</h6>
                <a href="{{ route('admin.postos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Posto
                </a>
            </div>
            <div class="card-body">
                @if($postos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($postos as $posto)
                                <tr>
                                    <td>{{ $posto->id }}</td>
                                    <td>{{ $posto->nome }}</td>
                                    <td>{{ $posto->descricao ?? 'Sem descrição' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $posto->ativo ? 'success' : 'secondary' }}">
                                            {{ $posto->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.postos.show', $posto) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.postos.edit', $posto) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.postos.pontos-base', $posto) }}" class="btn btn-secondary btn-sm" title="Pontos Base">
                                                <i class="fas fa-map-marker"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    title="Excluir"
                                                    onclick="confirmarExclusao({{ $posto->id }}, '{{ $posto->nome }}', {{ $posto->pontosBase->count() }}, {{ $posto->cartoesPrograma->count() }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-map-marker-alt fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Nenhum posto de trabalho encontrado</p>
                        <a href="{{ route('admin.postos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeiro Posto
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Formulário de exclusão (oculto) -->
<form id="form-exclusao" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Modal de confirmação -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Atenção!</strong> Esta ação irá desativar o posto de trabalho.
                </div>

                <p>Tem certeza que deseja desativar o posto <strong id="nome-posto"></strong>?</p>

                <div id="detalhes-posto" class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-left-info">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase">Pontos Base</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800" id="count-pontos">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-warning">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase">Cartões Programa</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800" id="count-cartoes">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> O posto será desativado (soft delete) e não aparecerá mais nas listagens, mas os dados não serão perdidos permanentemente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-exclusao">
                    <i class="fas fa-trash"></i> Confirmar Exclusão
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(postoId, nomePosto, countPontos, countCartoes) {
    // Preencher dados no modal
    document.getElementById('nome-posto').textContent = nomePosto;
    document.getElementById('count-pontos').textContent = countPontos;
    document.getElementById('count-cartoes').textContent = countCartoes;

    // Configurar formulário de exclusão
    const form = document.getElementById('form-exclusao');
    form.action = `/admin/postos/${postoId}`;

    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
    modal.show();
}

// Evento de confirmação
document.getElementById('btn-confirmar-exclusao').addEventListener('click', function() {
    // Mostrar loading
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';

    // Submeter formulário
    document.getElementById('form-exclusao').submit();
});

// Log para debug
console.log('Script de exclusão de postos carregado');
</script>

<style>
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.modal-body .card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.modal-body .text-xs {
    font-size: 0.7rem;
}

.modal-body .h6 {
    font-size: 1.25rem;
}
</style>
@endsection
