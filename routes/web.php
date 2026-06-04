<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AgendamentoPublicoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComissaoController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public scheduling link
Route::prefix('agendar')->name('publico.')->group(function () {
    Route::get('/{slug}', [AgendamentoPublicoController::class, 'show'])->name('agendar');
    Route::get('/{slug}/horarios', [AgendamentoPublicoController::class, 'horarios'])->name('horarios');
    Route::post('/{slug}', [AgendamentoPublicoController::class, 'store'])->name('store');
});

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth', 'petshop.setup'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Agenda
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/', [AgendaController::class, 'index'])->name('index');
        Route::get('/semana', [AgendaController::class, 'semana'])->name('semana');
        Route::post('/', [AgendaController::class, 'store'])->name('store');
        Route::patch('/{agendamento}/status', [AgendaController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/{data}', [AgendaController::class, 'index'])->name('data');
    });

    // Clientes
    Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    Route::resource('clientes', ClienteController::class);

    // Pets (nested in clientes)
    Route::resource('clientes.pets', PetController::class)->except(['index']);
    Route::get('/pets/{pet}/ficha', [PetController::class, 'fichaCompleta'])->name('pets.ficha');

    // Financeiro
    Route::prefix('financeiro')->name('financeiro.')->group(function () {
        Route::get('/', [FinanceiroController::class, 'index'])->name('index');
        Route::get('/exportar', [FinanceiroController::class, 'exportar'])->name('exportar');
    });

    // Comissões
    Route::prefix('comissoes')->name('comissoes.')->group(function () {
        Route::get('/', [ComissaoController::class, 'index'])->name('index');
        Route::patch('/{comissao}/pagar', [ComissaoController::class, 'pagar'])->name('pagar');
    });

    // Configurações
    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        Route::get('/petshop', [ConfiguracaoController::class, 'petshop'])->name('petshop');
        Route::put('/petshop', [ConfiguracaoController::class, 'updatePetshop'])->name('petshop.update');
        Route::get('/servicos', [ConfiguracaoController::class, 'servicos'])->name('servicos');
        Route::post('/servicos', [ConfiguracaoController::class, 'storeServico'])->name('servicos.store');
        Route::put('/servicos/{servico}', [ConfiguracaoController::class, 'updateServico'])->name('servicos.update');
        Route::delete('/servicos/{servico}', [ConfiguracaoController::class, 'destroyServico'])->name('servicos.destroy');
        Route::get('/colaboradores', [ConfiguracaoController::class, 'colaboradores'])->name('colaboradores');
        Route::get('/horarios', [ConfiguracaoController::class, 'horarios'])->name('horarios');
        Route::put('/horarios', [ConfiguracaoController::class, 'updateHorarios'])->name('horarios.update');
        Route::get('/fidelidade', [ConfiguracaoController::class, 'fidelidade'])->name('fidelidade');
        Route::put('/fidelidade', [ConfiguracaoController::class, 'updateFidelidade'])->name('fidelidade.update');
        Route::get('/integracao', [ConfiguracaoController::class, 'integracao'])->name('integracao');
        Route::put('/integracao', [ConfiguracaoController::class, 'updateIntegracao'])->name('integracao.update');
    });

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
