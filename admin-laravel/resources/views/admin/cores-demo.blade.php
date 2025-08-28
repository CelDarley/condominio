@extends("layouts.app")

@section("title", "Demonstração de Cores")
@section("page-title", "Paleta de Cores do Sistema")

@section("content")
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-palette"></i> Paleta de Cores Personalizada
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Cores Primárias -->
                    <div class="col-md-6 mb-4">
                        <h5 class="text-primary">Cores Primárias</h5>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="p-4 text-center text-white" style="background-color: #364659; border-radius: 8px;">
                                    <strong>#364659</strong><br>
                                    <small>Primary Dark</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-4 text-center text-white" style="background-color: #566273; border-radius: 8px;">
                                    <strong>#566273</strong><br>
                                    <small>Primary Medium</small>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="p-4 text-center" style="background-color: #F2F2F2; border-radius: 8px; border: 1px solid #ddd;">
                                    <strong>#F2F2F2</strong><br>
                                    <small>Light Gray</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Componentes de Exemplo -->
                    <div class="col-md-6 mb-4">
                        <h5 class="text-primary">Componentes de Exemplo</h5>
                        
                        <!-- Botões -->
                        <div class="mb-3">
                            <button class="btn btn-primary me-2">Botão Primário</button>
                            <button class="btn btn-secondary me-2">Botão Secundário</button>
                            <button class="btn btn-outline-primary">Outline Primary</button>
                        </div>

                        <!-- Badges -->
                        <div class="mb-3">
                            <span class="badge badge-primary me-2">Primary</span>
                            <span class="badge badge-warning me-2">Warning</span>
                            <span class="badge badge-info me-2">Info</span>
                            <span class="badge badge-success">Success</span>
                        </div>

                        <!-- Alertas -->
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i>
                            <strong>Alerta Info</strong> - Com a nova paleta de cores
                        </div>
                    </div>
                </div>

                <!-- Cards de Estatísticas Demonstração -->
                <div class="row">
                    <div class="col-12 mb-3">
                        <h5 class="text-primary">Cards de Estatísticas</h5>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card dashboard-stat-card shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Exemplo 1</div>
                                        <div class="h5 mb-0 font-weight-bold text-primary-custom">1,234</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x dashboard-stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card dashboard-stat-card shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Exemplo 2</div>
                                        <div class="h5 mb-0 font-weight-bold text-primary-custom">567</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shield-alt fa-2x dashboard-stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card dashboard-stat-card shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Exemplo 3</div>
                                        <div class="h5 mb-0 font-weight-bold text-primary-custom">89</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-map-marker-alt fa-2x dashboard-stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card dashboard-stat-card shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Exemplo 4</div>
                                        <div class="h5 mb-0 font-weight-bold text-primary-custom">12</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x dashboard-stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela Demonstração -->
                <div class="row">
                    <div class="col-12 mb-3">
                        <h5 class="text-primary">Tabela com Nova Paleta</h5>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Status</th>
                                        <th>Tipo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>João Silva</td>
                                        <td><span class="badge badge-primary">Ativo</span></td>
                                        <td><span class="badge badge-warning">Vigilante</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Maria Santos</td>
                                        <td><span class="badge badge-secondary">Inativo</span></td>
                                        <td><span class="badge badge-info">Morador</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Formulário Demonstração -->
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary">Formulário com Nova Paleta</h5>
                        <form>
                            <div class="mb-3">
                                <label for="demo-input" class="form-label">Campo de Exemplo</label>
                                <input type="text" class="form-control" id="demo-input" placeholder="Digite algo...">
                            </div>
                            <div class="mb-3">
                                <label for="demo-select" class="form-label">Seleção</label>
                                <select class="form-control" id="demo-select">
                                    <option>Opção 1</option>
                                    <option>Opção 2</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="demo-check">
                                    <label class="form-check-label" for="demo-check">
                                        Checkbox de exemplo
                                    </label>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary">Salvar Exemplo</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-primary">Variações de Gradiente</h5>
                        <div class="mb-3 p-3 text-white bg-primary-custom" style="border-radius: 8px;">
                            <strong>Background Primary Custom</strong><br>
                            <small>Gradiente personalizado</small>
                        </div>
                        <div class="mb-3 p-3 bg-light-custom" style="border-radius: 8px; border: 1px solid #ddd;">
                            <strong>Background Light Custom</strong><br>
                            <small>Cinza claro personalizado</small>
                        </div>
                        <div class="p-3 border-primary-custom" style="border: 2px solid; border-radius: 8px;">
                            <strong>Border Primary Custom</strong><br>
                            <small>Borda personalizada</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para demonstração */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection 