@extends("layouts.app")

@section("title", "Editar Posto de Trabalho")
@section("page-title", "Editar Posto de Trabalho")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Editar Posto: {{ $posto->nome }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.postos.update', $posto) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Posto <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nome') is-invalid @enderror" 
                               id="nome" 
                               name="nome" 
                               value="{{ old('nome', $posto->nome) }}" 
                               required 
                               placeholder="Ex: Posto Principal, Portaria Norte">
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Nome identificador do posto de trabalho</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                  id="descricao" 
                                  name="descricao" 
                                  rows="3"
                                  placeholder="Descreva a localização e características deste posto...">{{ old('descricao', $posto->descricao) }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="ativo" 
                                   name="ativo" 
                                   {{ old('ativo', $posto->ativo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ativo">
                                Posto ativo
                            </label>
                        </div>
                        <small class="form-text text-muted">Postos inativos não aparecem para criação de cartões programa</small>
                    </div>
                    
                    <!-- Informações sobre pontos e cartões existentes -->
                    @if($posto->pontosBase->count() > 0)
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Pontos Base Associados
                        </h6>
                        <p class="mb-2">Este posto possui <strong>{{ $posto->pontosBase->count() }} pontos base</strong> cadastrados:</p>
                        <ul class="mb-2">
                            @foreach($posto->pontosBase->take(5) as $ponto)
                                <li>{{ $ponto->nome }}</li>
                            @endforeach
                            @if($posto->pontosBase->count() > 5)
                                <li><em>... e mais {{ $posto->pontosBase->count() - 5 }} pontos</em></li>
                            @endif
                        </ul>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb"></i> 
                            Os pontos base não são afetados pela edição do posto.
                        </small>
                    </div>
                    @endif
                    
                    @if($posto->cartoesPrograma && $posto->cartoesPrograma->count() > 0)
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> Cartões Programa Associados
                        </h6>
                        <p class="mb-2">Este posto é usado por <strong>{{ $posto->cartoesPrograma->count() }} cartões programa</strong>.</p>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Desativar este posto pode afetar cartões programa em uso.
                        </small>
                    </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.postos.show', $posto) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            <a href="{{ route('admin.postos.show', $posto) }}" class="btn btn-outline-secondary ml-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Card de Ações Adicionais -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cogs"></i> Ações Adicionais
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-map-marker-alt"></i> Gerenciar Pontos Base
                        </h6>
                        <p class="small text-muted">Adicione, edite ou remova pontos base deste posto.</p>
                        <a href="{{ route('admin.postos.pontos-base', $posto) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-cog"></i> Gerenciar Pontos
                        </a>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">
                            <i class="fas fa-id-card"></i> Cartões Programa
                        </h6>
                        <p class="small text-muted">Crie novos cartões programa para este posto.</p>
                        <a href="{{ route('admin.cartoes-programa.create') }}?posto={{ $posto->id }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus"></i> Novo Cartão
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avisar sobre desativação do posto
    const ativoCheckbox = document.getElementById('ativo');
    const cartoesCount = {{ $posto->cartoesPrograma ? $posto->cartoesPrograma->count() : 0 }};
    
    ativoCheckbox.addEventListener('change', function() {
        if (!this.checked && cartoesCount > 0) {
            if (!confirm(`Este posto possui ${cartoesCount} cartão(ões) programa associado(s). Desativar o posto pode afetar seu funcionamento. Deseja continuar?`)) {
                this.checked = true;
            }
        }
    });
});
</script>
@endsection 