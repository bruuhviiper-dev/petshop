<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AgendamentoPublicoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComissaoController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\PdvController;
use App\Http\Controllers\PetCarteirinhaController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public scheduling link (rate limited)
Route::prefix('agendar')->name('publico.')->middleware(['throttle:agendamento-publico'])->group(function () {
    Route::get('/{slug}', [AgendamentoPublicoController::class, 'show'])->name('agendar');
    Route::get('/{slug}/horarios', [AgendamentoPublicoController::class, 'horarios'])->name('horarios');
    Route::post('/{slug}', [AgendamentoPublicoController::class, 'store'])->name('store');
});

// Carteirinha digital do pet (pública, acesso por token)
Route::get('/carteirinha/{token}', [PetCarteirinhaController::class, 'show'])->name('pet.carteirinha');

Route::get('/', function () {
    // Resolve o petshop de demonstração dinamicamente para o link público da home.
    // Preferimos o slug histórico do seed; se ele foi renomeado, caímos no
    // primeiro petshop ativo. Assim o link nunca aponta para um slug inexistente.
    // Envolto em try/catch para a home pública nunca quebrar (banco indisponível/sem migração).
    $demoSlug = null;
    try {
        $demoSlug = \App\Models\Petshop::query()
            ->where('active', true)
            ->orderByRaw("CASE WHEN slug = 'pet-tosa-ana' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->value('slug');
    } catch (\Throwable $e) {
        // Sem banco/tabela: a home apenas oculta o link público.
    }

    return view('home', ['demoSlug' => $demoSlug]);
})->name('home');

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

    // PDV / Frente de caixa
    Route::prefix('pdv')->name('pdv.')->group(function () {
        Route::get('/', [PdvController::class, 'index'])->name('index');
        Route::post('/', [PdvController::class, 'store'])->name('store');
        Route::get('/historico', [PdvController::class, 'historico'])->name('historico');
    });

    // Produtos / Estoque
    Route::prefix('produtos')->name('produtos.')->group(function () {
        Route::get('/', [ProdutoController::class, 'index'])->name('index');
        Route::post('/', [ProdutoController::class, 'store'])->name('store');
        Route::put('/{produto}', [ProdutoController::class, 'update'])->name('update');
        Route::delete('/{produto}', [ProdutoController::class, 'destroy'])->name('destroy');
        Route::post('/{produto}/estoque', [ProdutoController::class, 'ajustarEstoque'])->name('estoque');
    });

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
        Route::post('/colaboradores', [ConfiguracaoController::class, 'storeColaborador'])->name('colaboradores.store');
        Route::put('/colaboradores/{colaborador}', [ConfiguracaoController::class, 'updateColaborador'])->name('colaboradores.update');
        Route::delete('/colaboradores/{colaborador}', [ConfiguracaoController::class, 'destroyColaborador'])->name('colaboradores.destroy');
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
