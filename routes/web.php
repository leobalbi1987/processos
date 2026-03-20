<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/admin/login', function () {
    return redirect('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/relatorio/processo/{processo}', [RelatorioController::class, 'processo'])->name('relatorio.processo');
    Route::get('/relatorio/processos-lote', [RelatorioController::class, 'processosEmLote'])->name('relatorio.processos_lote');
});

Route::get('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout.get');

require __DIR__.'/auth.php';
