<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoradorController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\PanicoController;

// Rotas públicas
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/login', [MoradorController::class, 'showLoginForm'])->name('login');
Route::post('/login', [MoradorController::class, 'login']);
Route::get('/register', [MoradorController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [MoradorController::class, 'register']);

// Rotas protegidas por autenticação
Route::middleware(['auth.morador'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('/alertas/{alerta}', [AlertaController::class, 'show'])->name('alertas.show');
    
    // Comentários
    Route::post('/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::delete('/comentarios/{comentario}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');
    
    // Botão de pânico
    Route::post('/panico', [PanicoController::class, 'ativar'])->name('panico.ativar');
    Route::get('/panico/status', [PanicoController::class, 'status'])->name('panico.status');
    
    // Logout
    Route::post('/logout', [MoradorController::class, 'logout'])->name('logout');
});

// API para localização em tempo real dos vigilantes
Route::get('/api/vigilantes/posicao', [DashboardController::class, 'getVigilantesPosicao']);
Route::get('/api/alertas/ativos', [AlertaController::class, 'getAlertasAtivos']);
