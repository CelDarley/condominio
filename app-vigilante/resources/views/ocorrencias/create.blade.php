@extends('layouts.app')

@section('title', 'Registrar Ocorrência')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-plus me-2"></i>
            Registrar Nova Ocorrência
        </h4>
        <a href="{{ route('ocorrencias.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('ocorrencias.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="titulo" class="form-label">Título da Ocorrência *</label>
                                <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                       id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="posto_trabalho_id" class="form-label">Posto</label>
                                <select class="form-select @error('posto_trabalho_id') is-invalid @enderror" 
                                        id="posto_trabalho_id" name="posto_trabalho_id">
                                    <option value="">Selecione o posto</option>
                                    @foreach($postos as $posto)
                                        <option value="{{ $posto->id }}" {{ old('posto_trabalho_id') == $posto->id ? 'selected' : '' }}>
                                            {{ $posto->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('posto_trabalho_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo" class="form-label">Tipo *</label>
                                <select class="form-select @error('tipo') is-invalid @enderror" 
                                        id="tipo" name="tipo" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="incidente" {{ old('tipo') == 'incidente' ? 'selected' : '' }}>Incidente</option>
                                    <option value="manutencao" {{ old('tipo') == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                                    <option value="seguranca" {{ old('tipo') == 'seguranca' ? 'selected' : '' }}>Segurança</option>
                                    <option value="outros" {{ old('tipo') == 'outros' ? 'selected' : '' }}>Outros</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="prioridade" class="form-label">Prioridade *</label>
                                <select class="form-select @error('prioridade') is-invalid @enderror" 
                                        id="prioridade" name="prioridade" required>
                                    <option value="">Selecione a prioridade</option>
                                    <option value="baixa" {{ old('prioridade') == 'baixa' ? 'selected' : '' }}>Baixa</option>
                                    <option value="media" {{ old('prioridade') == 'media' ? 'selected' : '' }}>Média</option>
                                    <option value="alta" {{ old('prioridade') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgente" {{ old('prioridade') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                </select>
                                @error('prioridade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição Detalhada *</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" name="descricao" rows="6" required>{{ old('descricao') }}</textarea>
                            <div class="form-text">Descreva detalhadamente o que aconteceu, incluindo data, hora, local e pessoas envolvidas.</div>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="anexos" class="form-label">Anexos (Fotos, Documentos)</label>
                            <input type="file" class="form-control @error('anexos.*') is-invalid @enderror" 
                                   id="anexos" name="anexos[]" multiple 
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <div class="form-text">
                                Máximo 10MB por arquivo. Formatos aceitos: JPG, PNG, PDF, DOC, DOCX. 
                                Você pode selecionar múltiplos arquivos.
                            </div>
                            @error('anexos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('ocorrencias.index') }}" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Registrar Ocorrência
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview dos arquivos selecionados
document.getElementById('anexos').addEventListener('change', function(e) {
    const files = e.target.files;
    const container = document.getElementById('file-preview');
    
    if (container) {
        container.remove();
    }
    
    if (files.length > 0) {
        const preview = document.createElement('div');
        preview.id = 'file-preview';
        preview.className = 'mt-2';
        
        const title = document.createElement('small');
        title.className = 'text-muted d-block mb-2';
        title.textContent = 'Arquivos selecionados:';
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