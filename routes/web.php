<?php

//-- use com alias para facilitar e nao precisar escrever url completa
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Responsavel\PassageiroController as PassageiroController;
use App\Http\Controllers\Responsavel\PerfilController as ResponsavelPerfilController;
use App\Http\Controllers\Responsavel\SolicitacaoController;
use App\Http\Controllers\Responsavel\PresencaController;
use App\Http\Controllers\Motorista\SolicitacaoController as MotoristaSOlicitacaoController;
use App\Http\Controllers\Responsavel\MarketplaceController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Motorista\DashboardController as MotoristaDashboard;
use App\Http\Controllers\Motorista\VanController;
use App\Http\Controllers\Motorista\DisponibilidadeController;
use App\Http\Controllers\Motorista\VinculoController;
use App\Http\Controllers\Motorista\PerfilController as MotoristaPerfilController;
use App\Http\Controllers\Motorista\RotaController;
use App\Http\Controllers\Responsavel\AcompanharController;
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
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::post('/motoristas/{id}/aprovar',  [AdminDashboard::class, 'aprovarMotorista'])->name('motoristas.aprovar');
    Route::post('/motoristas/{id}/rejeitar', [AdminDashboard::class, 'rejeitarMotorista'])->name('motoristas.rejeitar');
    Route::post('/vans/{id}/aprovar',        [AdminDashboard::class, 'aprovarVan'])->name('vans.aprovar');
    Route::post('/vans/{id}/rejeitar',       [AdminDashboard::class, 'rejeitarVan'])->name('vans.rejeitar');
});

//-- Rota com autenticação para dashboard de reponsavel (controller ResponsavelDashboard)
Route::middleware(['auth', 'role:responsavel'])->prefix('responsavel')->name('responsavel.')->group(function(){
    
    // Dashboard
    Route::get('/dashboard', [ResponsavelDashboard::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/perfil',  [ResponsavelPerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil',  [ResponsavelPerfilController::class, 'update'])->name('perfil.update');

    // Marketplace
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');

    // Solicitação de vaga — máximo 10 por minuto por usuário
    Route::post('/solicitacoes', [SolicitacaoController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('solicitacoes.store');
    Route::delete('/solicitacoes/{id}', [SolicitacaoController::class, 'cancelar'])
        ->name('solicitacoes.cancelar');

    // Presença diária
    Route::post('/presenca',      [PresencaController::class, 'store'])->name('presenca.store');
    Route::delete('/presenca/{id}', [PresencaController::class, 'destroy'])->name('presenca.destroy');

    // Acompanhar trajeto em tempo real (polling JSON)
    Route::get('/acompanhar', [AcompanharController::class, 'index'])->name('acompanhar');

    // Passageiro — cadastro inicial (onboarding)
    Route::get('/passageiros/criar', [PassageiroController::class, 'create'])->name('passageiros.create');
    Route::post('/passageiros/essencial', [PassageiroController::class, 'storeEssencial'])->name('passageiros.store.essencial');
    Route::get('/passageiros/criar/enderecos', [PassageiroController::class, 'createEnderecos'])->name('passageiros.create.enderecos');
    Route::post('/passageiros', [PassageiroController::class, 'store'])->name('passageiros.store');

    // Passageiro — adicionar pelo dashboard (após onboarding)
    Route::get('/passageiros/adicionar', [PassageiroController::class, 'adicionar'])->name('passageiros.adicionar');
    Route::post('/passageiros/adicionar', [PassageiroController::class, 'storeCompleto'])->name('passageiros.adicionar.store');

    // Passageiro — gestão (detalhes, edição, exclusão)
    Route::get('/passageiros/{id}', [PassageiroController::class, 'show'])->name('passageiros.show');
    Route::get('/passageiros/{id}/editar', [PassageiroController::class, 'edit'])->name('passageiros.edit');
    Route::put('/passageiros/{id}', [PassageiroController::class, 'update'])->name('passageiros.update');
    Route::get('/passageiros/{id}/enderecos', [PassageiroController::class, 'editEnderecos'])->name('passageiros.enderecos');
    Route::put('/passageiros/{id}/enderecos', [PassageiroController::class, 'updateEnderecos'])->name('passageiros.enderecos.update');
    Route::put('/passageiros/{id}/desativar', [PassageiroController::class, 'desativar'])->name('passageiros.desativar');
});

//-- Rota com autenticação para dashboard de Motorista (controller MotoristaDashboard)
Route::middleware(['auth', 'role:motorista'/*, 'verified' //isso aqui faria validacao de email (mandaria cod para confirmacao no email*/ ])->prefix('motorista')->name('motorista.')->group(function(){
    Route::get('/dashboard', [MotoristaDashboard::class, 'index'])->name('dashboard');
    Route::get('/van/criar', [VanController::class, 'create'])->name('van.create');
    Route::post('/van', [VanController::class, 'store'])->name('van.store');

    Route::get('/perfil',         [MotoristaPerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil',         [MotoristaPerfilController::class, 'update'])->name('perfil.update');

    Route::post('/solicitacoes/{id}/aceitar', [MotoristaSOlicitacaoController::class, 'aceitar'])->name('solicitacoes.aceitar');
    Route::post('/solicitacoes/{id}/recusar', [MotoristaSOlicitacaoController::class, 'recusar'])->name('solicitacoes.recusar');

    Route::post('/vinculos/{id}/encerrar', [VinculoController::class, 'encerrar'])->name('vinculos.encerrar');
    Route::post('/disponibilidades/{id}/reordenar', [VinculoController::class, 'reordenar'])->name('disponibilidades.reordenar');

    // Rotas GPS
    Route::post('/rotas/{disponibilidadeId}/iniciar',              [RotaController::class, 'iniciar'])->name('rotas.iniciar');
    Route::post('/rotas/{rotaId}/encerrar',                        [RotaController::class, 'encerrar'])->name('rotas.encerrar');
    Route::post('/rotas/{rotaId}/posicao',                         [RotaController::class, 'posicao'])->name('rotas.posicao');
    Route::post('/rotas/{rotaId}/paradas/{paradaId}/confirmar',    [RotaController::class, 'confirmar'])->name('rotas.confirmar');

    Route::get('/disponibilidades/criar',        [DisponibilidadeController::class, 'create'])->name('disponibilidades.create');
    Route::post('/disponibilidades',             [DisponibilidadeController::class, 'store'])->name('disponibilidades.store');
    Route::get('/disponibilidades/{id}/editar',  [DisponibilidadeController::class, 'edit'])->name('disponibilidades.edit');
    Route::put('/disponibilidades/{id}',         [DisponibilidadeController::class, 'update'])->name('disponibilidades.update');
    Route::delete('/disponibilidades/{id}',      [DisponibilidadeController::class, 'destroy'])->name('disponibilidades.destroy');
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
