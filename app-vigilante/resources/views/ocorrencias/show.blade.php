@extends('layouts.app')

@section('title', 'Detalhes da Ocorrência')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>
            Detalhes da Ocorrência
        </h4>
        <div>
            <a href="{{ route('ocorrencias.edit', $ocorrencia) }}" class="btn btn-outline-warning me-2">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="{{ route('ocorrencias.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $ocorrencia->titulo }}</h5>
                    <span class="badge {{ $ocorrencia->getStatusClass() }} fs-6">
                        {{ $ocorrencia->getStatusFormatado() }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <strong class="text-muted d-block">Tipo</strong>
                            <span class="badge bg-secondary">{{ $ocorrencia->getTipoFormatado() }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted d-block">Prioridade</strong>
                            <span class="badge bg-light text-dark {{ $ocorrencia->getPrioridadeClass() }}">
                                {{ $ocorrencia->getPrioridadeFormatada() }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted d-block">Data/Hora</strong>
                            {{ $ocorrencia->getDataFormatada() }}
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted d-block">Posto</strong>
                            {{ $ocorrencia->postoTrabalho ? $ocorrencia->postoTrabalho->nome : 'Não informado' }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong class="text-muted d-block mb-2">Descrição</strong>
                        <div class="bg-light p-3 rounded">
                            {{ $ocorrencia->descricao }}
                        </div>
                    </div>

                    @if($ocorrencia->observacoes)
                    <div class="mb-4">
                        <strong class="text-muted d-block mb-2">Observações</strong>
                        <div class="bg-light p-3 rounded">
                            {{ $ocorrencia->observacoes }}
                        </div>
                    </div>
                    @endif

                    @if($ocorrencia->temAnexos())
                    <div class="mb-4">
                        <strong class="text-muted d-block mb-3">Anexos ({{ $ocorrencia->getQuantidadeAnexos() }})</strong>
                        <div class="row g-3">
                            @foreach($ocorrencia->anexos as $index => $anexo)
                            <div class="col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            @if(str_contains($anexo['tipo'], 'image'))
                                                <i class="fas fa-image text-primary me-2"></i>
                                            @elseif(str_contains($anexo['tipo'], 'pdf'))
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                            @else
                                                <i class="fas fa-file text-secondary me-2"></i>
                                            @endif
                                            <small class="fw-bold">{{ $anexo['nome'] }}</small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                Tamanho: {{ number_format($anexo['tamanho'] / 1024 / 1024, 2) }} MB
                                            </small>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('ocorrencias.download-anexo', [$ocorrencia, $index]) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                            @if(str_contains($anexo['tipo'], 'image'))
                                            <button type="button" class="btn btn-outline-info btn-sm" 
                                                    onclick="mostrarImagem('{{ Storage::url($anexo['caminho']) }}', '{{ $anexo['nome'] }}')">
                                                <i class="fas fa-eye me-1"></i>Visualizar
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Informações do Registro</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-muted d-block">Registrado por</strong>
                        {{ $ocorrencia->usuario->nome }}
                    </div>
                    <div class="mb-3">
                        <strong class="text-muted d-block">Data de criação</strong>
                        {{ $ocorrencia->created_at->format('d/m/Y H:i') }}
                    </div>
                    @if($ocorrencia->updated_at != $ocorrencia->created_at)
                    <div class="mb-3">
                        <strong class="text-muted d-block">Última atualização</strong>
                        {{ $ocorrencia->updated_at->format('d/m/Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Ações</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('ocorrencias.edit', $ocorrencia) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Ocorrência
                        </a>
                        <button type="button" class="btn btn-outline-info" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar imagens -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Visualizar Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Imagem da ocorrência">
            </div>
        </div>
    </div>
</div>

<script>
function mostrarImagem(url, nome) {
    document.getElementById('modalImage').src = url;
    document.getElementById('imageModalLabel').textContent = nome;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

<style>
@media print {
    .btn, .card-header, .modal { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endsection 