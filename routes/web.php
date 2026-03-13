<?php

//-- use com alias para facilitar e nao precisar escrever url completa
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Responsavel\AlunoController as AlunoController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Motorista\DashboardController as MotoristaDashboard;
use App\Http\Controllers\Responsavel\DashboardController as ResponsavelDashboard;
use App\Http\Controllers\Auth\RegisterMotoristaController;
use App\Http\Controllers\Auth\RegisterResponsavelController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//-- Rota com autenticação para dashboard de admin (controller AdminDashboard)
Route::middleware(['auth'/*, 'verified' //isso aqui faria validacao de email (mandaria cod para confirmacao no email*/ ])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
});

//-- Rota com autenticação para dashboard de reponsavel (controller ResponsavelDashboard)
Route::middleware(['auth'/*, 'verified' //isso aqui faria validacao de email (mandaria cod para confirmacao no email*/ ])->prefix('responsavel')->name('responsavel.')->group(function(){
    Route::get('/dashboard', [ResponsavelDashboard::class, 'index'])->name('dashboard');
    Route::get('/alunos/criar', [AlunoController::class, 'create'])->name('alunos.create');
    Route::post('/alunos', [AlunoController::class, 'store'])->name('alunos.store');
});

//-- Rota com autenticação para dashboard de Motorista (controller MotoristaDashboard)
Route::middleware(['auth'/*, 'verified' //isso aqui faria validacao de email (mandaria cod para confirmacao no email*/ ])->prefix('motorista')->name('motorista.')->group(function(){
    Route::get('/dashboard', [MotoristaDashboard::class, 'index'])->name('dashboard');
});

// Responsável
Route::get('/cadastro/responsavel', [RegisterResponsavelController::class, 'create'])->name('register.responsavel');
Route::post('/cadastro/responsavel', [RegisterResponsavelController::class, 'store'])->name('register.responsavel.store');

// Motorista
Route::get('/cadastro/motorista', [RegisterMotoristaController::class, 'create'])->name('register.motorista');
Route::post('/cadastro/motorista', [RegisterMotoristaController::class, 'store'])->name('register.motorista.store');

/*Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
