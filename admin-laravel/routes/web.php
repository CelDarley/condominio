<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PostoTrabalhoController;
use App\Http\Controllers\EscalaController;
use App\Http\Controllers\CartaoProgramaController;
use App\Http\Controllers\MoradorController;

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
    Route::post('usuarios/{usuario}/deactivate', [UsuarioController::class, 'deactivate'])->name('usuarios.deactivate');
    Route::delete('usuarios/{usuario}/force-delete', [UsuarioController::class, 'forceDelete'])->name('usuarios.force-delete');
    
    // Rota de teste para debug
    Route::get('usuarios/{usuario}/test-update', function($id) {
        $usuario = \App\Models\Usuario::findOrFail($id);
        return response()->json([
            'usuario' => $usuario,
            'fillable' => $usuario->getFillable(),
            'can_update' => true
        ]);
    })->name('usuarios.test-update');

    // Gerenciamento de Moradores
    Route::resource('moradores', MoradorController::class)->parameters([
        'moradores' => 'morador'
    ]);
    Route::post('moradores/{morador}/toggle-status', [MoradorController::class, 'toggleStatus'])->name('moradores.toggle-status');
    Route::patch('moradores/{morador}/change-password', [MoradorController::class, 'changePassword'])->name('moradores.change-password');
    Route::post('moradores/{morador}/add-veiculo', [MoradorController::class, 'addVeiculo'])->name('moradores.add-veiculo');
    Route::delete('moradores/{morador}/remove-veiculo/{veiculo}', [MoradorController::class, 'removeVeiculo'])->name('moradores.remove-veiculo');

    // Gerenciamento de Postos de Trabalho
    Route::resource('postos', PostoTrabalhoController::class);
    Route::get('postos/{posto}/pontos-base', [PostoTrabalhoController::class, 'pontosBase'])->name('postos.pontos-base');
    Route::get('postos/{posto}/pontos-base/create', [PostoTrabalhoController::class, 'createPontoBase'])->name('postos.pontos-base.create');
    Route::post('postos/{posto}/pontos-base', [PostoTrabalhoController::class, 'storePontoBase'])->name('postos.pontos-base.store');
    Route::get('postos/{posto}/pontos-base/{ponto}/edit', [PostoTrabalhoController::class, 'editPontoBase'])->name('postos.pontos-base.edit');
    Route::put('postos/{posto}/pontos-base/{ponto}', [PostoTrabalhoController::class, 'updatePontoBase'])->name('postos.pontos-base.update');
    Route::delete('postos/{posto}/pontos-base/{ponto}', [PostoTrabalhoController::class, 'destroyPontoBase'])->name('postos.pontos-base.destroy');
    
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

    // Gerenciamento de Escala Diária
    Route::get('escala-diaria', [App\Http\Controllers\EscalaDiariaController::class, 'index'])->name('escala-diaria.index');
    Route::get('escala-diaria/calendario', [App\Http\Controllers\EscalaDiariaController::class, 'calendario'])->name('escala-diaria.calendario');
    Route::post('escala-diaria', [App\Http\Controllers\EscalaDiariaController::class, 'store'])->name('escala-diaria.store');
    Route::put('escala-diaria/{escalaDiaria}', [App\Http\Controllers\EscalaDiariaController::class, 'update'])->name('escala-diaria.update');
    Route::delete('escala-diaria/{escalaDiaria}', [App\Http\Controllers\EscalaDiariaController::class, 'destroy'])->name('escala-diaria.destroy');
    Route::get('escala-diaria/cartoes-programa', [App\Http\Controllers\EscalaDiariaController::class, 'cartoesPrograma'])->name('escala-diaria.cartoes-programa');
    
    // API para filtro de vigilante
    Route::get('api/escalas-vigilante/{vigilante}/{ano}/{mes}', [App\Http\Controllers\EscalaDiariaController::class, 'escalasVigilante'])->name('api.escalas-vigilante');
    
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
