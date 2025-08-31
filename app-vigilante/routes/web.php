<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostoController;
use App\Http\Controllers\PresencaController;
use App\Http\Controllers\AvisoController;
use Illuminate\Support\Facades\Route;

// Redirecionar raiz para dashboard ou login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// Rota de teste para debug
Route::get('/test', function () {
    return '<h1>Teste Simples</h1><p>Se você vê isso, o Laravel está funcionando!</p>';
})->name('test');

// Rotas de autenticação
Route::group(['prefix' => 'auth'], function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Rotas protegidas por autenticação de vigilante
Route::group(['middleware' => 'auth.vigilante'], function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/postos-por-data/{data}', [DashboardController::class, 'postosPorData'])->name('api.postos-por-data');
    
    // Postos de Trabalho
    Route::group(['prefix' => 'posto'], function () {
        Route::get('/{posto}', [PostoController::class, 'show'])->name('posto.show');
        Route::get('/{posto}/status-pontos', [PostoController::class, 'statusPontos'])->name('posto.status-pontos');
    });
    
    // Registro de Presença
    Route::group(['prefix' => 'presenca'], function () {
        Route::post('/registrar/{ponto}', [PresencaController::class, 'registrar'])->name('presenca.registrar');
        Route::get('/historico', [PresencaController::class, 'historico'])->name('presenca.historico');
        Route::get('/relatorio', [PresencaController::class, 'relatorio'])->name('presenca.relatorio');
    });
    
    // Avisos
    Route::group(['prefix' => 'avisos'], function () {
        Route::get('/', [AvisoController::class, 'index'])->name('avisos.index');
        Route::get('/criar', [AvisoController::class, 'create'])->name('avisos.create');
        Route::post('/', [AvisoController::class, 'store'])->name('avisos.store');
        Route::get('/{aviso}', [AvisoController::class, 'show'])->name('avisos.show');
        Route::get('/{aviso}/editar', [AvisoController::class, 'edit'])->name('avisos.edit');
        Route::put('/{aviso}', [AvisoController::class, 'update'])->name('avisos.update');
        Route::delete('/{aviso}', [AvisoController::class, 'destroy'])->name('avisos.destroy');
        
        // API para avisos rápidos
        Route::post('/enviar-rapido', [AvisoController::class, 'enviarRapido'])->name('avisos.enviar-rapido');
        Route::post('/panico', [AvisoController::class, 'panico'])->name('avisos.panico');
    });
    
});

// Rota de teste
Route::get('/teste', function () {
    return view('test');
});
