<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PostoTrabalhoController;
use App\Http\Controllers\EscalaController;
use App\Http\Controllers\CartaoProgramaController;

// Rota principal
Route::get("/", function () {
    return redirect("/admin");
});

// Rotas de autenticação
Route::get("/admin", function () {
    return view("admin.login");
})->name("admin.login");

Route::post("/admin", [AdminController::class, "authenticate"])->name("admin.authenticate");

// Grupo de rotas protegidas por middleware admin
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get("/dashboard", [AdminController::class, "dashboard"])->name("dashboard");
    
    // Logout
    Route::post("/logout", [AdminController::class, "logout"])->name("logout");
    
    // Gerenciamento de Usuários
    Route::resource('usuarios', UsuarioController::class);
    Route::post('usuarios/{usuario}/toggle-status', [UsuarioController::class, 'toggleStatus'])->name('usuarios.toggle-status');

    // Gerenciamento de Postos de Trabalho
    Route::resource('postos', PostoTrabalhoController::class);
    Route::get('postos/{posto}/pontos-base', [PostoTrabalhoController::class, 'pontosBase'])->name('postos.pontos-base');
    Route::get('postos/{posto}/pontos-base/create', [PostoTrabalhoController::class, 'createPontoBase'])->name('postos.pontos-base.create');
    Route::post('postos/{posto}/pontos-base', [PostoTrabalhoController::class, 'storePontoBase'])->name('postos.pontos-base.store');
    
    // Gerenciamento de Escalas
    Route::resource('escalas', EscalaController::class);
    Route::get('escalas-relatorio', [EscalaController::class, 'relatorio'])->name('escalas.relatorio');
    
    // Gerenciamento de Cartões Programa
    Route::resource('cartoes-programa', CartaoProgramaController::class)->parameters([
        'cartoes-programa' => 'cartaoPrograma'
    ]);
    Route::post('cartoes-programa/{cartaoPrograma}/adicionar-ponto', [CartaoProgramaController::class, 'adicionarPonto'])->name('cartoes-programa.adicionar-ponto');
    Route::delete('cartoes-programa/{cartaoPrograma}/remover-ponto/{pontoId}', [CartaoProgramaController::class, 'removerPonto'])->name('cartoes-programa.remover-ponto');
    Route::get('cartoes-programa/{cartaoPrograma}/ponto/{pontoId}', [CartaoProgramaController::class, 'buscarPonto'])->name('cartoes-programa.buscar-ponto');
    Route::patch('cartoes-programa/{cartaoPrograma}/editar-ponto/{pontoId}', [CartaoProgramaController::class, 'editarPonto'])->name('cartoes-programa.editar-ponto');
    Route::patch('cartoes-programa/{cartaoPrograma}/reordenar-pontos', [CartaoProgramaController::class, 'reordenarPontos'])->name('cartoes-programa.reordenar-pontos');
    Route::post('cartoes-programa/{cartaoPrograma}/duplicar', [CartaoProgramaController::class, 'duplicar'])->name('cartoes-programa.duplicar');
    Route::get('cartoes-programa/por-posto/{posto}', [CartaoProgramaController::class, 'porPosto'])->name('cartoes-programa.por-posto');
    
    // API Routes
    Route::get('api/escalas/{usuario}/{dia}', [EscalaController::class, 'getEscalasByUsuario'])->name('api.escalas.usuario');
});

// Rota de teste
Route::get("/test", function () {
    return view("test");
});

Route::get("/cores-demo", function () {
    return view("admin.cores-demo");
})->name("cores.demo");
