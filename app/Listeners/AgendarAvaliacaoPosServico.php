<?php

namespace App\Listeners;

use App\Events\AgendamentoConcluido;
use App\Jobs\SolicitarAvaliacaoPos;

class AgendarAvaliacaoPosServico
{
    public function handle(AgendamentoConcluido $event): void
    {
        dispatch(new SolicitarAvaliacaoPos($event->agendamento->id))->delay(now()->addHours(2));
    }
}
