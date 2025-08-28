@extends("layouts.app")

@section("title", "Novo Posto")
@section("page-title", "Novo Posto de Trabalho")

@section("content")
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-map-marker-alt"></i> Cadastrar Novo Posto de Trabalho
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.postos.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Posto <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome') }}" 
                                       required 
                                       placeholder="Ex: Portaria Principal, Guarita Sul, etc.">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                          id="descricao" 
                                          name="descricao" 
                                          rows="4"
                                          placeholder="Descreva as características e responsabilidades deste posto de trabalho...">{{ old('descricao') }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="ativo" 
                                           name="ativo" 
                                           checked>
                                    <label class="form-check-label" for="ativo">
                                        Posto ativo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Dica:</strong> Após criar o posto, você poderá adicionar pontos base específicos para definir o itinerário de vigilância.
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.postos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Posto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 