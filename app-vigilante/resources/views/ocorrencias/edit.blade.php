@extends('layouts.app')

@section('title', 'Editar Ocorrência')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-edit me-2"></i>
            Editar Ocorrência
        </h4>
        <a href="{{ route('ocorrencias.show', $ocorrencia) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('ocorrencias.update', $ocorrencia) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="titulo" class="form-label">Título da Ocorrência *</label>
                                <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                       id="titulo" name="titulo" value="{{ old('titulo', $ocorrencia->titulo) }}" required>
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="aberta" {{ old('status', $ocorrencia->status) == 'aberta' ? 'selected' : '' }}>Aberta</option>
                                    <option value="em_andamento" {{ old('status', $ocorrencia->status) == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                    <option value="resolvida" {{ old('status', $ocorrencia->status) == 'resolvida' ? 'selected' : '' }}>Resolvida</option>
                                    <option value="fechada" {{ old('status', $ocorrencia->status) == 'fechada' ? 'selected' : '' }}>Fechada</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="posto_trabalho_id" class="form-label">Posto</label>
                                <select class="form-select @error('posto_trabalho_id') is-invalid @enderror" 
                                        id="posto_trabalho_id" name="posto_trabalho_id">
                                    <option value="">Selecione o posto</option>
                                    @foreach($postos as $posto)
                                        <option value="{{ $posto->id }}" {{ old('posto_trabalho_id', $ocorrencia->posto_trabalho_id) == $posto->id ? 'selected' : '' }}>
                                            {{ $posto->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('posto_trabalho_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tipo" class="form-label">Tipo *</label>
                                <select class="form-select @error('tipo') is-invalid @enderror" 
                                        id="tipo" name="tipo" required>
                                    <option value="incidente" {{ old('tipo', $ocorrencia->tipo) == 'incidente' ? 'selected' : '' }}>Incidente</option>
                                    <option value="manutencao" {{ old('tipo', $ocorrencia->tipo) == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                                    <option value="seguranca" {{ old('tipo', $ocorrencia->tipo) == 'seguranca' ? 'selected' : '' }}>Segurança</option>
                                    <option value="outros" {{ old('tipo', $ocorrencia->tipo) == 'outros' ? 'selected' : '' }}>Outros</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="prioridade" class="form-label">Prioridade *</label>
                                <select class="form-select @error('prioridade') is-invalid @enderror" 
                                        id="prioridade" name="prioridade" required>
                                    <option value="baixa" {{ old('prioridade', $ocorrencia->prioridade) == 'baixa' ? 'selected' : '' }}>Baixa</option>
                                    <option value="media" {{ old('prioridade', $ocorrencia->prioridade) == 'media' ? 'selected' : '' }}>Média</option>
                                    <option value="alta" {{ old('prioridade', $ocorrencia->prioridade) == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgente" {{ old('prioridade', $ocorrencia->prioridade) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('prioridade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição Detalhada *</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" name="descricao" rows="6" required>{{ old('descricao', $ocorrencia->descricao) }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações Adicionais</label>
                            <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                      id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $ocorrencia->observacoes) }}</textarea>
                            <div class="form-text">Campo para anotações de acompanhamento ou atualizações.</div>
                            @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($ocorrencia->temAnexos())
                        <div class="mb-3">
                            <label class="form-label">Anexos Atuais</label>
                            <div class="row g-2">
                                @foreach($ocorrencia->anexos as $index => $anexo)
                                <div class="col-md-6" id="anexo-{{ $index }}">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if(str_contains($anexo['tipo'], 'image'))
                                                        <i class="fas fa-image text-primary me-1"></i>
                                                    @elseif(str_contains($anexo['tipo'], 'pdf'))
                                                        <i class="fas fa-file-pdf text-danger me-1"></i>
                                                    @else
                                                        <i class="fas fa-file text-secondary me-1"></i>
                                                    @endif
                                                    <small>{{ $anexo['nome'] }}</small>
                                                </div>
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="removerAnexo({{ $ocorrencia->id }}, {{ $index }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label for="anexos" class="form-label">Adicionar Novos Anexos</label>
                            <input type="file" class="form-control @error('anexos.*') is-invalid @enderror" 
                                   id="anexos" name="anexos[]" multiple 
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <div class="form-text">
                                Máximo 10MB por arquivo. Formatos aceitos: JPG, PNG, PDF, DOC, DOCX.
                            </div>
                            @error('anexos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('ocorrencias.show', $ocorrencia) }}" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function removerAnexo(ocorrenciaId, indice) {
    if (!confirm('Tem certeza que deseja remover este anexo?')) {
        return;
    }

    try {
        const response = await fetch(`/ocorrencias/${ocorrenciaId}/anexo`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ indice: indice })
        });

        if (response.ok) {
            document.getElementById(`anexo-${indice}`).remove();
            alert('Anexo removido com sucesso!');
        } else {
            alert('Erro ao remover anexo.');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao remover anexo.');
    }
}

// Preview dos novos arquivos selecionados
document.getElementById('anexos').addEventListener('change', function(e) {
    const files = e.target.files;
    const container = document.getElementById('new-file-preview');
    
    if (container) {
        container.remove();
    }
    
    if (files.length > 0) {
        const preview = document.createElement('div');
        preview.id = 'new-file-preview';
        preview.className = 'mt-2';
        
        const title = document.createElement('small');
        title.className = 'text-muted d-block mb-2';
        title.textContent = 'Novos arquivos selecionados:';
        preview.appendChild(title);
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileInfo = document.createElement('div');
            fileInfo.className = 'badge bg-light text-dark me-2 mb-1';
            fileInfo.innerHTML = `<i class="fas fa-file me-1"></i>${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            preview.appendChild(fileInfo);
        }
        
        e.target.parentNode.appendChild(preview);
    }
});
</script>
@endsection 