<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste - RBX-Security Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-vial me-2"></i>Teste de Funcionalidades</h3>
                    </div>
                    <div class="card-body">
                        <h5>Teste de Links e JavaScript</h5>
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" onclick="testAlert()">
                                <i class="fas fa-exclamation-circle me-2"></i>Testar Alert
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <a href="javascript:void(0)" onclick="testClick()" class="btn btn-success">
                                <i class="fas fa-mouse-pointer me-2"></i>Testar Link JavaScript
                            </a>
                        </div>
                        
                        <div class="mb-3">
                            <a href="{{ route('admin.login') }}" class="btn btn-info">
                                <i class="fas fa-home me-2"></i>Voltar para Login
                            </a>
                        </div>
                        
                        <div class="mb-3">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="testDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-list me-2"></i>Teste Dropdown
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="testDropdown">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="alert('Item 1 clicado')">Item 1</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="alert('Item 2 clicado')">Item 2</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="alert('Item 3 clicado')">Item 3</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Console:</strong> Abra o console do navegador (F12) para ver os logs de debug.
                        </div>
                        
                        <div id="status" class="alert alert-success" style="display: none;">
                            JavaScript está funcionando corretamente!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log('Página de teste carregada');
        
        function testAlert() {
            alert('Alert funcionando!');
            console.log('Alert testado');
        }
        
        function testClick() {
            document.getElementById('status').style.display = 'block';
            console.log('Link JavaScript funcionando');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado na página de teste');
            
            // Testar se Bootstrap está carregado
            var dropdown = document.querySelector('[data-bs-toggle="dropdown"]');
            if (dropdown) {
                console.log('Bootstrap dropdown encontrado');
            }
            
            // Adicionar listener para todos os links
            var links = document.querySelectorAll('a, button');
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    console.log('Elemento clicado:', this.textContent.trim());
                });
            });
        });
    </script>
</body>
</html> 