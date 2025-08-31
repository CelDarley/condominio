<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SegCond Vigilante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; }
        .card { margin-bottom: 20px; border-radius: 8px; }
        .header { background: linear-gradient(135deg, #364659 0%, #566273 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-shield-alt me-2"></i>SegCond Vigilante</h1>
        <p class="mb-0">Olá, {{ $user->nome ?? 'Usuário' }}</p>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calendar-alt me-2"></i>Escala para {{ $dataBase->format('d/m/Y') }}</h5>
            </div>
            <div class="card-body">
                @if($postos->isNotEmpty())
                    @foreach($postos as $posto)
                        <div class="alert alert-success">
                            <h6><i class="fas fa-map-marker-alt me-2"></i>{{ $posto->nome }}</h6>
                            @if($posto->descricao)
                                <p class="mb-0">{{ $posto->descricao }}</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-calendar-times me-2"></i>
                        Nenhuma escala para este dia
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt me-2"></i>Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <button class="btn btn-warning w-100"><i class="fas fa-bullhorn"></i> Enviar Aviso</button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-danger w-100"><i class="fas fa-exclamation-triangle"></i> Botão Pânico</button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-info w-100"><i class="fas fa-history"></i> Histórico</button>
                    </div>
                    <div class="col-md-3">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-sign-out-alt"></i> Sair</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
