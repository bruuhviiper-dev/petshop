<?php

namespace App\Listeners;

use App\Events\AgendamentoConcluido;
use App\Models\Comissao;
use App\Models\Financeiro;

class LancarFinanceiro
{
    public function handle(AgendamentoConcluido $event): void
    {
        $agendamento = $event->agendamento;

        Financeiro::firstOrCreate(
            ['agendamento_id' => $agendamento->id, 'type' => 'receita'],
            [
                'petshop_id'  => $agendamento->petshop_id,
                'amount'      => $agendamento->valor,
                'description' => "Atendimento #{$agendamento->id} — {$agendamento->servico?->name}",
                'paid_at'     => now(),
            ]
        );

        if ($agendamento->colaborador_id && $agendamento->colaborador) {
            $comissaoValor = $agendamento->valor * ($agendamento->colaborador->comissao_percentual / 100);
            Comissao::firstOrCreate(
                ['agendamento_id' => $agendamento->id],
                [
                    'colaborador_id' => $agendamento->colaborador_id,
                    'amount'         => round($comissaoValor, 2),
                ]
            );
        }
    }
}
