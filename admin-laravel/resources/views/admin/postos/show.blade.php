@extends("layouts.app")

@section("title", "Posto de Trabalho - " . $posto->nome)
@section("page-title", "Detalhes do Posto de Trabalho")

@section("content")
<div class="row">
    <div class="col-12">
        <!-- Cabeçalho com informações principais -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-building"></i> {{ $posto->nome }}
                </h6>
                <div>
                    <a href="{{ route('admin.postos.edit', $posto) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('admin.postos.index') }}" class="btn btn-secondary btn-sm">
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
                                <td width="120"><strong>ID:</strong></td>
                                <td><span class="badge badge-primary">{{ $posto->id }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td>{{ $posto->nome }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($posto->ativo)
                                        <span class="badge badge-success">Ativo</span>
                                    @else
                                        <span class="badge badge-secondary">Inativo</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-chart-bar"></i> Estatísticas</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="160"><strong>Pontos Base:</strong></td>
                                <td><span class="badge badge-info">{{ $posto->pontosBase->count() }} pontos</span></td>
                            </tr>
                            <tr>
                                <td><strong>Cartões Programa:</strong></td>
                                <td><span class="badge badge-warning">{{ $posto->cartoesPrograma->count() ?? 0 }} cartões</span></td>
                            </tr>
                            <tr>
                                <td><strong>Criado em:</strong></td>
                                <td>{{ $posto->created_at ? $posto->created_at->format('d/m/Y H:i') : 'Não informado' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($posto->descricao)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary"><i class="fas fa-file-text"></i> Descrição</h6>
                        <p class="text-muted">{{ $posto->descricao }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Pontos Base do Posto -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-map-marker-alt"></i> Pontos Base
                </h6>
                <div>
                    <span class="badge badge-info">{{ $posto->pontosBase->count() }} pontos</span>
                    <a href="{{ route('admin.postos.pontos-base', $posto) }}" class="btn btn-primary btn-sm ml-2">
                        <i class="fas fa-cog"></i> Gerenciar Pontos
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($posto->pontosBase->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">Ordem</th>
                                    <th>Nome</th>
                                    <th>Localização</th>
                                    <th>QR Code</th>
                                    <th>Status</th>
                                    <th>Cartões Programa</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($posto->pontosBase as $ponto)
                                <tr>
                                    <td>
                                        <span class="badge badge-secondary">{{ $ponto->ordem ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $ponto->nome }}</strong>
                                        @if($ponto->descricao)
                                            <br><small class="text-muted">{{ Str::limit($ponto->descricao, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ponto->endereco)
                                            <small>{{ Str::limit($ponto->endereco, 40) }}</small>
                                        @endif
                                        @if($ponto->latitude && $ponto->longitude)
                                            <br><small class="text-info">
                                                <i class="fas fa-map-pin"></i> GPS: {{ $ponto->latitude }}, {{ $ponto->longitude }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ponto->qr_code)
                                            <span class="badge badge-success">
                                                <i class="fas fa-qrcode"></i> Gerado
                                            </span>
                                            <br><small class="text-muted">{{ $ponto->qr_code }}</small>
                                        @else
                                            <span class="badge badge-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Pendente
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ponto->ativo)
                                            <span class="badge badge-success">Ativo</span>
                                        @else
                                            <span class="badge badge-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $ponto->cartaoProgramaPontos->count() }} cartões
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" title="Editar ponto" onclick="editarPontoBase({{ $ponto->id }})">
                                                <i class="fas fa-edit"></i>
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
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum ponto base cadastrado</h5>
                        <p class="text-muted">Este posto ainda não possui pontos base definidos.</p>
                        <a href="{{ route('admin.postos.pontos-base', $posto) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Adicionar Primeiro Ponto Base
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Cartões Programa Associados -->
        @if($posto->cartoesPrograma && $posto->cartoesPrograma->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-id-card"></i> Cartões Programa Associados
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($posto->cartoesPrograma as $cartao)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $cartao->nome }}</strong>
                                        <br><small class="text-muted">
                                            {{ $cartao->cartaoProgramaPontos->count() }} pontos base
                                        </small>
                                        @if($cartao->horario_inicio && $cartao->horario_fim)
                                            <br><small class="text-info">
                                                <i class="fas fa-clock"></i> 
                                                {{ \Carbon\Carbon::parse($cartao->horario_inicio)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($cartao->horario_fim)->format('H:i') }}
                                            </small>
                                        @endif
                                    </div>
                                    <div>
                                        @if($cartao->ativo)
                                            <span class="badge badge-success">Ativo</span>
                                        @else
                                            <span class="badge badge-secondary">Inativo</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.cartoes-programa.show', $cartao) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Ações Rápidas -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt"></i> Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.postos.pontos-base', $posto) }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-map-marker-alt"></i><br>
                            <small>Gerenciar Pontos Base</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.cartoes-programa.create') }}?posto={{ $posto->id }}" class="btn btn-outline-success btn-block">
                            <i class="fas fa-id-card"></i><br>
                            <small>Novo Cartão Programa</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.postos.edit', $posto) }}" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-edit"></i><br>
                            <small>Editar Posto</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-info btn-block" onclick="gerarRelatorio({{ $posto->id }})">
                            <i class="fas fa-chart-bar"></i><br>
                            <small>Relatório do Posto</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editarPontoBase(pontoId) {
    // Redirecionar para a página de gerenciamento de pontos base
    window.location.href = `{{ route('admin.postos.pontos-base', $posto) }}#ponto-${pontoId}`;
}

function gerarRelatorio(postoId) {
    alert('Funcionalidade de relatório será implementada em breve.');
}
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
</style>
@endsection 