@extends("layouts.app")

@section("title", "Cartões Programa")
@section("page-title", "Cartões Programa")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-id-card"></i> Lista de Cartões Programa
                </h6>
                <div>
                    <a href="{{ route('admin.cartoes-programa.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Cartão Programa
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($cartoes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Nome do Cartão</th>
                                    <th>Posto de Trabalho</th>
                                    <th>Pontos Base</th>
                                    <th>Tempo Total</th>
                                    <th>Horário</th>
                                    <th>Status</th>
                                    <th style="width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartoes as $cartao)
                                <tr>
                                    <td>
                                        <span class="badge badge-primary badge-lg">
                                            {{ $cartao->id }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $cartao->nome }}</strong>
                                        @if($cartao->descricao)
                                            <br><small class="text-muted">{{ Str::limit($cartao->descricao, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fas fa-building text-info"></i>
                                        {{ $cartao->postoTrabalho->nome ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <i class="fas fa-map-marker-alt"></i> {{ $cartao->getTotalPontos() }} pontos
                                        </span>
                                        @if($cartao->getPontosAtivos() != $cartao->getTotalPontos())
                                            <br><small class="text-warning">{{ $cartao->getPontosAtivos() }} obrigatórios</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> {{ $cartao->getTempoTotalFormatado() }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $cartao->getHorarioInicioFormatado() }} - {{ $cartao->getHorarioFimFormatado() }}
                                            @if($cartao->horario_fim <= $cartao->horario_inicio)
                                                <i class="fas fa-moon text-info ml-1" title="Turno noturno"></i>
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $cartao->ativo ? 'success' : 'secondary' }}">
                                            {{ $cartao->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.cartoes-programa.show', $cartao) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.cartoes-programa.edit', $cartao) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm" 
                                                    title="Excluir"
                                                    onclick="excluirCartao({{ $cartao->id }}, '{{ $cartao->nome }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    @if($cartoes->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $cartoes->links() }}
                        </div>
                    @endif

                    <!-- Estatísticas -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total de Cartões
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-primary-custom">
                                        {{ $cartoes->total() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Cartões Ativos
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-primary-custom">
                                        {{ $cartoes->where('ativo', true)->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total de Pontos
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-primary-custom">
                                        {{ $cartoes->sum(function($c) { return $c->getTotalPontos(); }) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Tempo Médio
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-primary-custom">
                                        @php
                                            $tempoMedio = $cartoes->count() > 0 ? 
                                                $cartoes->avg('tempo_total_estimado') : 0;
                                            $horas = floor($tempoMedio / 60);
                                            $mins = $tempoMedio % 60;
                                        @endphp
                                        {{ $horas > 0 ? "{$horas}h {$mins}min" : "{$mins}min" }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-id-card fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">Nenhum Cartão Programa Cadastrado</h5>
                        <p class="text-gray-500 mb-4">
                            Crie cartões programa para definir sequências de pontos base<br>
                            com tempos específicos de permanência e deslocamento.
                        </p>
                        <a href="{{ route('admin.cartoes-programa.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Primeiro Cartão Programa
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card de Ajuda -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-question-circle"></i> Como Funciona o Cartão Programa
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <h6 class="text-info">
                                    <i class="fas fa-1"></i> Criar Cartão
                                </h6>
                                <p class="mb-0 small">
                                    Defina um nome, posto de trabalho e horários de funcionamento para o cartão programa.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6 class="text-warning">
                                    <i class="fas fa-2"></i> Adicionar Pontos
                                </h6>
                                <p class="mb-0 small">
                                    Adicione pontos base em sequência, definindo tempos de permanência e deslocamento.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <h6 class="text-success">
                                    <i class="fas fa-3"></i> Usar em Escalas
                                </h6>
                                <p class="mb-0 small">
                                    Atribua o cartão programa a vigilantes nas escalas diárias.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function excluirCartao(id, nome) {
    if (confirm(`Tem certeza que deseja excluir o cartão programa "${nome}"?\n\nEsta ação não pode ser desfeita e removerá toda a sequência de pontos.`)) {
        // Criar form e submeter
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/cartoes-programa/${id}`;
        
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
</script>

<style>
.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.border-left-primary {
    border-left: 0.25rem solid var(--primary-dark) !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.text-xs {
    font-size: 0.75rem !important;
}
</style>
@endsection 