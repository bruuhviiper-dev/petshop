<?php

namespace App\Listeners;

use App\Events\AgendamentoConcluido;
use App\Models\FidelidadeCliente;
use App\Models\FidelidadeConfig;
use App\Models\NotificacaoLog;

class IncrementarFidelidade
{
    public function handle(AgendamentoConcluido $event): void
    {
        $agendamento = $event->agendamento;

        $fidelidade = FidelidadeCliente::firstOrCreate(
            ['cliente_id' => $agendamento->cliente_id, 'petshop_id' => $agendamento->petshop_id],
            ['total_atendimentos' => 0]
        );
        $fidelidade->increment('total_atendimentos');

        $config = FidelidadeConfig::where('petshop_id', $agendamento->petshop_id)->first();
        if (!$config) return;

        if ($fidelidade->total_atendimentos % $config->atendimentos_para_premio === 0) {
            NotificacaoLog::create([
                'agendamento_id' => $agendamento->id,
                'type'           => 'fidelidade',
                'sent_at'        => now(),
                'status'         => 'enviado',
                'payload'        => [
                    'mensagem'  => "Prêmio de fidelidade! {$config->desconto_percentual}% de desconto",
                    'cliente'   => $agendamento->cliente_id,
                    'total'     => $fidelidade->total_atendimentos,
                ],
            ]);
        }
    }
}
