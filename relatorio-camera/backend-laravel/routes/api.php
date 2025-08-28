<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoradorController;
use App\Http\Controllers\VeiculoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas para Moradores
Route::apiResource('moradores', MoradorController::class);

// Rotas para Veículos
Route::apiResource('veiculos', VeiculoController::class);
Route::get('veiculos/moradores/list', [VeiculoController::class, 'getMoradores']);
