<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect('admin/login');
});

Auth::routes();

use App\Http\Controllers\RelatorioController;

Route::middleware(['auth'])->group(function () {
    Route::get('/relatorio/processo/{processo}', [RelatorioController::class, 'processo'])->name('relatorio.processo');
    Route::get('/relatorio/processos-lote', [RelatorioController::class, 'processosEmLote'])->name('relatorio.processos_lote');
});
