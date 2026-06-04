<?php

namespace App\Providers;

use App\Events\AgendamentoConcluido;
use App\Events\AgendamentoCriado;
use App\Listeners\AgendarAvaliacaoPosServico;
use App\Listeners\DispararConfirmacaoWhatsapp;
use App\Listeners\IncrementarFidelidade;
use App\Listeners\LancarFinanceiro;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(AgendamentoCriado::class, DispararConfirmacaoWhatsapp::class);
        Event::listen(AgendamentoConcluido::class, LancarFinanceiro::class);
        Event::listen(AgendamentoConcluido::class, IncrementarFidelidade::class);
        Event::listen(AgendamentoConcluido::class, AgendarAvaliacaoPosServico::class);
    }
}
