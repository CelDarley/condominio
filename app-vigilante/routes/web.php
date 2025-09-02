<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostoController;
use App\Http\Controllers\PresencaController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\OcorrenciaController;
use App\Http\Controllers\CameraController;
use Illuminate\Support\Facades\Route;

// Redirecionar raiz para dashboard ou login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// Rota de teste para debug
Route::get('/test', function () {
    return '<h1>Teste Simples</h1><p>Se você vê isso, o Laravel está funcionando!</p>';
})->name('test');

// Rota de teste POST
Route::post('/test-post', function () {
    return response()->json(['status' => 'POST funcionando', 'data' => request()->all()]);
})->name('test-post');

// Rota de login direta (sem prefixo)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rotas de autenticação com prefixo (mantendo compatibilidade)
Route::group(['prefix' => 'auth'], function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
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
        Route::post('/chegada/{ponto}', [PresencaController::class, 'registrarChegada'])->name('presenca.chegada');
        Route::post('/saida/{ponto}', [PresencaController::class, 'registrarSaida'])->name('presenca.saida');
        Route::get('/status-hoje', [PresencaController::class, 'statusHoje'])->name('presenca.status-hoje');
        Route::get('/historico', [PresencaController::class, 'historico'])->name('presenca.historico');
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
    
    // Ocorrências
    Route::group(['prefix' => 'ocorrencias'], function () {
        Route::get('/', [OcorrenciaController::class, 'index'])->name('ocorrencias.index');
        Route::get('/criar', [OcorrenciaController::class, 'create'])->name('ocorrencias.create');
        Route::post('/', [OcorrenciaController::class, 'store'])->name('ocorrencias.store');
        Route::get('/{ocorrencia}', [OcorrenciaController::class, 'show'])->name('ocorrencias.show');
        Route::get('/{ocorrencia}/editar', [OcorrenciaController::class, 'edit'])->name('ocorrencias.edit');
        Route::put('/{ocorrencia}', [OcorrenciaController::class, 'update'])->name('ocorrencias.update');
        Route::delete('/{ocorrencia}/anexo', [OcorrenciaController::class, 'removeAnexo'])->name('ocorrencias.remove-anexo');
        Route::get('/{ocorrencia}/anexo/{indice}', [OcorrenciaController::class, 'downloadAnexo'])->name('ocorrencias.download-anexo');
    });
    
    // Câmeras Compartilhadas
    Route::group(['prefix' => 'cameras'], function () {
        Route::get('/', [CameraController::class, 'index'])->name('cameras.index');
        Route::get('/morador', [CameraController::class, 'camerasDoMorador'])->name('cameras.morador');
        Route::get('/visualizar/{id}', [CameraController::class, 'visualizar'])->name('cameras.visualizar');
        Route::get('/buscar', [CameraController::class, 'buscar'])->name('cameras.buscar');
        Route::get('/estatisticas', [CameraController::class, 'estatisticas'])->name('cameras.estatisticas');
    });
    
});

// Rota de teste
Route::get('/teste', function () {
    return view('test');
});
