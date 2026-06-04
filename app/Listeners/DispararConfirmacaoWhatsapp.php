<?php

namespace App\Listeners;

use App\Events\AgendamentoCriado;
use App\Jobs\EnviarConfirmacaoAgendamento;

class DispararConfirmacaoWhatsapp
{
    public function handle(AgendamentoCriado $event): void
    {
        dispatch(new EnviarConfirmacaoAgendamento($event->agendamento->id));
    }
}
