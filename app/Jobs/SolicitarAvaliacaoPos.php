<?php

namespace App\Jobs;

use App\Models\Agendamento;
use App\Models\NotificacaoLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolicitarAvaliacaoPos implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public int $agendamentoId) {}

    public function handle(): void
    {
        $agendamento = Agendamento::with(['cliente', 'pet', 'servico', 'petshop'])->findOrFail($this->agendamentoId);

        $mensagem = "⭐ *Como foi o atendimento?*\n\n"
            . "Olá {$agendamento->cliente->name}! O {$agendamento->pet->name} já foi embora?\n\n"
            . "Gostaríamos de saber como foi o *{$agendamento->servico->name}* na {$agendamento->petshop->name}.\n\n"
            . "Sua avaliação é muito importante para nós! 🙏";

        $payload = [
            'instance' => config('services.whatsapp.instance'),
            'number'   => $agendamento->cliente->phone,
            'message'  => $mensagem,
        ];

        $status = 'enviado';
        try {
            if (config('services.whatsapp.url')) {
                Http::withToken(config('services.whatsapp.token'))
                    ->post(config('services.whatsapp.url'), $payload)
                    ->throw();
            }
        } catch (\Exception $e) {
            $status = 'falha';
            Log::warning('WhatsApp avaliação falhou', ['agendamento_id' => $this->agendamentoId]);
            throw $e;
        } finally {
            NotificacaoLog::create([
                'agendamento_id' => $agendamento->id,
                'type'           => 'avaliacao',
                'sent_at'        => now(),
                'status'         => $status,
                'payload'        => $payload,
            ]);
        }
    }
}
