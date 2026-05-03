<?php

//-- use com alias para facilitar e nao precisar escrever url completa
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Responsavel\PassageiroController as PassageiroController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Motorista\DashboardController as MotoristaDashboard;
use App\Http\Controllers\Responsavel\DashboardController as ResponsavelDashboard;
use App\Http\Controllers\Auth\RegisterMotoristaController;
use App\Http\Controllers\Auth\RegisterResponsavelController;
use App\Enums\UserRole;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $usuario = auth()->user()?->loadMissing('responsavel');
    $painelUrl = null;

    if ($usuario) {
        $role = $usuario->role instanceof UserRole ? $usuario->role->value : $usuario->role;

        if ($role === UserRole::Responsavel->value) {
            $temPassageiro = $usuario->responsavel?->passageiros()->exists() ?? false;
            $painelUrl = $temPassageiro
                ? route('responsavel.dashboard')
                : route('responsavel.passageiros.create');
        } elseif ($role === UserRole::Motorista->value) {
            $painelUrl = route('motorista.dashboard');
        } elseif ($role === UserRole::Admin->value) {
            $painelUrl = route('admin.dashboard');
        }
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'painelUrl' => $painelUrl ?? '',
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

//-- Rota com autenticação para dashboard de admin (controller AdminDashboard)
Route::middleware(['auth', 'role:admin'/*, 'verified' //isso aqui faria validacao de email (mandaria cod para confirmacao no email*/ ])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
});

//-- Rota com autenticação para dashboard de reponsavel (controller ResponsavelDashboard)
Route::middleware(['auth', 'role:responsavel'/*, 'verified' //isso aqui faria validacao de email (mandaria cod para confirmacao no email*/ ])->prefix('responsavel')->name('responsavel.')->group(function(){
    Route::get('/dashboard', [ResponsavelDashboard::class, 'index'])->name('dashboard');
    Route::get('/passageiros/criar', [PassageiroController::class, 'create'])->name('passageiros.create');
    Route::post('/passageiros/essencial', [PassageiroController::class, 'storeEssencial'])->name('passageiros.store.essencial');
    Route::get('/passageiros/criar/enderecos', [PassageiroController::class, 'createEnderecos'])->name('passageiros.create.enderecos');
    Route::post('/passageiros', [PassageiroController::class, 'store'])->name('passageiros.store');
});

//-- Rota com autenticação para dashboard de Motorista (controller MotoristaDashboard)
Route::middleware(['auth', 'role:motorista'/*, 'verified' //isso aqui faria validacao de email (mandaria cod para confirmacao no email*/ ])->prefix('motorista')->name('motorista.')->group(function(){
    Route::get('/dashboard', [MotoristaDashboard::class, 'index'])->name('dashboard');
});

Route::middleware('guest')->group(function () {
    // Responsável
    Route::get('/cadastro/responsavel', [RegisterResponsavelController::class, 'create'])->name('register.responsavel');
    Route::post('/cadastro/responsavel', [RegisterResponsavelController::class, 'store'])->name('register.responsavel.store');

    // Motorista
    Route::get('/cadastro/motorista', [RegisterMotoristaController::class, 'create'])->name('register.motorista');
    Route::post('/cadastro/motorista', [RegisterMotoristaController::class, 'store'])->name('register.motorista.store');
});

/*Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
