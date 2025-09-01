@extends("layouts.app")

@section("title", "Escalas")
@section("page-title", "Escalas")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Escalas</h6>
                <div>
                    <a href="{{ route('admin.escalas.relatorio') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Relatório
                    </a>
                    <a href="{{ route('admin.escalas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Escala
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($escalas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Posto</th>
                                    <th>Cartão Programa</th>
                                    <th>Dia da Semana</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($escalas as $escala)
                                <tr>
                                    <td>{{ $escala->id }}</td>
                                    <td>{{ $escala->usuario->nome ?? 'N/A' }}</td>
                                    <td>{{ $escala->postoTrabalho->nome ?? 'N/A' }}</td>
                                    <td>
                                        @if($escala->cartaoPrograma)
                                            <span class="badge badge-primary">
                                                {{ $escala->cartaoPrograma->nome }}
                                            </span>
                                            <br><small class="text-muted">{{ $escala->cartaoPrograma->horario_inicio }} - {{ $escala->cartaoPrograma->horario_fim }}</small>
                                        @else
                                            <span class="text-muted">Não definido</span>
                                        @endif
                                    </td>
                                    <td>{{ $escala->getDiasSemanaNomes() }}</td>
                                    <td>
                                        <span class="badge badge-{{ $escala->ativo ? 'success' : 'secondary' }}">
                                            {{ $escala->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.escalas.show', $escala) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.escalas.edit', $escala) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Excluir"
                                                    onclick="confirmarExclusaoEscala({{ $escala->id }}, '{{ $escala->usuario->nome ?? 'N/A' }}', '{{ $escala->getDiasSemanaNomes() }}')">
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
                        <i class="fas fa-clock fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Nenhuma escala encontrada</p>
                        <a href="{{ route('admin.escalas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeira Escala
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Formulário de exclusão (oculto) -->
<form id="form-exclusao-escala" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Modal de confirmação para exclusão de escala -->
<div class="modal fade" id="modalConfirmacaoEscala" tabindex="-1" aria-labelledby="modalConfirmacaoEscalaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalConfirmacaoEscalaLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h6>Tem certeza que deseja excluir esta escala?</h6>
                </div>

                <div class="alert alert-warning">
                    <div class="row">
                        <div class="col-12">
                            <strong>Detalhes da Escala:</strong>
                        </div>
                        <div class="col-6">
                            <strong>Usuário:</strong> <span id="escala-usuario"></span>
                        </div>
                        <div class="col-6">
                            <strong>Dia:</strong> <span id="escala-dia"></span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> A escala será desativada (soft delete) e não aparecerá mais nas listagens, mas os dados não serão perdidos permanentemente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-exclusao-escala">
                    <i class="fas fa-trash"></i> Confirmar Exclusão
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusaoEscala(escalaId, nomeUsuario, diaSemana) {
    // Preencher dados no modal
    document.getElementById('escala-usuario').textContent = nomeUsuario;
    document.getElementById('escala-dia').textContent = diaSemana;

    // Configurar formulário de exclusão
    const form = document.getElementById('form-exclusao-escala');
    form.action = `/admin/escalas/${escalaId}`;

    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmacaoEscala'));
    modal.show();
}

// Evento de confirmação
document.getElementById('btn-confirmar-exclusao-escala').addEventListener('click', function() {
    // Mostrar loading
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';

    // Submeter formulário
    document.getElementById('form-exclusao-escala').submit();
});

// Log para debug
console.log('Script de exclusão de escalas carregado');
</script>
@endsection
