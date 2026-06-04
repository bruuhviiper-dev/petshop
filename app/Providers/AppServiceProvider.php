<?php

namespace App\Providers;

use App\Events\AgendamentoConcluido;
use App\Events\AgendamentoCriado;
use App\Listeners\AgendarAvaliacaoPosServico;
use App\Listeners\DispararConfirmacaoWhatsapp;
use App\Listeners\IncrementarFidelidade;
use App\Listeners\LancarFinanceiro;
use App\Repositories\AgendamentoRepository;
use App\Repositories\RelatorioRepository;
use App\Services\AgendamentoService;
use App\Services\ClienteService;
use App\Services\PetService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AgendamentoRepository::class);
        $this->app->singleton(RelatorioRepository::class);
        $this->app->singleton(AgendamentoService::class);
        $this->app->singleton(ClienteService::class);
        $this->app->singleton(PetService::class);
    }

    public function boot(): void
    {
        Blade::component('layouts.landing', 'landing-layout');

        Event::listen(AgendamentoCriado::class, DispararConfirmacaoWhatsapp::class);
        Event::listen(AgendamentoConcluido::class, LancarFinanceiro::class);
        Event::listen(AgendamentoConcluido::class, IncrementarFidelidade::class);
        Event::listen(AgendamentoConcluido::class, AgendarAvaliacaoPosServico::class);
    }
}
