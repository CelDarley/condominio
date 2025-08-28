<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

// Rota principal
Route::get("/", function () {
    return redirect("/admin");
});

// Rotas de autenticação
Route::get("/admin", function () {
    return view("admin.login");
})->name("admin.login");

Route::post("/admin", [AdminController::class, 'authenticate'])->name("admin.authenticate");

// Rota do dashboard (protegida por middleware admin)
Route::get("/admin/dashboard", [AdminController::class, 'dashboard'])->name("admin.dashboard")->middleware('admin');

// Rota de logout
Route::post("/admin/logout", [AdminController::class, 'logout'])->name("admin.logout")->middleware('admin');

// Rota de teste
Route::get("/test", function () {
    return "Teste funcionando!";
});
