@extends('layouts.app')

@section('title', 'Minhas Ocorrências')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>
            Minhas Ocorrências
        </h4>
        <a href="{{ route('ocorrencias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nova Ocorrência
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($ocorrencias->count() > 0)
        <div class="row">
            @foreach($ocorrencias as $ocorrencia)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $ocorrencia->titulo }}</h6>
                        <span class="badge {{ $ocorrencia->getStatusClass() }}">
                            {{ $ocorrencia->getStatusFormatado() }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <small class="text-muted d-block">Tipo</small>
                                <span class="badge bg-secondary">{{ $ocorrencia->getTipoFormatado() }}</span>
                            </div>
                            <div class="col">
                                <small class="text-muted d-block">Prioridade</small>
                                <span class="badge bg-light text-dark {{ $ocorrencia->getPrioridadeClass() }}">
                                    {{ $ocorrencia->getPrioridadeFormatada() }}
                                </span>
                            </div>
                        </div>

                        <p class="card-text small">
                            {{ Str::limit($ocorrencia->descricao, 100) }}
                        </p>

                        <div class="row g-2 mb-3">
                            <div class="col">
                                <small class="text-muted d-block">Data</small>
                                <small>{{ $ocorrencia->getDataFormatada() }}</small>
                            </div>
                            @if($ocorrencia->postoTrabalho)
                            <div class="col">
                                <small class="text-muted d-block">Posto</small>
                                <small>{{ $ocorrencia->postoTrabalho->nome }}</small>
                            </div>
                            @endif
                        </div>

                        @if($ocorrencia->temAnexos())
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-paperclip me-1"></i>
                                {{ $ocorrencia->getQuantidadeAnexos() }} anexo(s)
                            </small>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="{{ route('ocorrencias.show', $ocorrencia) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-2"></i>Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="d-flex justify-content-center">
            {{ $ocorrencias->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma ocorrência registrada</h5>
                <p class="text-muted mb-4">Você ainda não registrou nenhuma ocorrência.</p>
                <a href="{{ route('ocorrencias.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Registrar Primera Ocorrência
                </a>
            </div>
        </div>
    @endif
</div>
@endsection 