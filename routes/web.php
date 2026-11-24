<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaccionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Todos los módulos solo están disponibles para usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->middleware('verified')->name('dashboard');

    Route::resource('categorias', CategoriaController::class)
        ->except('show')
        ->parameters(['categorias' => 'categoria']);

    Route::resource('cuentas', CuentaController::class)
        ->except('show')
        ->parameters(['cuentas' => 'cuenta']);

    Route::resource('transacciones', TransaccionController::class)
        ->except('show')
        ->parameters(['transacciones' => 'transaccion']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
