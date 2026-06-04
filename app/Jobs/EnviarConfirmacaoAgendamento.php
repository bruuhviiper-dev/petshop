<?php

namespace App\Jobs;

use App\Models\Agendamento;
use App\Models\NotificacaoLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnviarConfirmacaoAgendamento implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public int $agendamentoId) {}

    public function handle(): void
    {
        $agendamento = Agendamento::with(['cliente', 'pet', 'servico', 'petshop'])->findOrFail($this->agendamentoId);

        $mensagem = "✅ *{$agendamento->petshop->name}*\n\n"
            . "Olá {$agendamento->cliente->name}! Seu agendamento foi confirmado.\n\n"
            . "🐾 Pet: *{$agendamento->pet->name}*\n"
            . "✂️ Serviço: *{$agendamento->servico->name}*\n"
            . "📅 Data: *{$agendamento->scheduled_at->format('d/m/Y \à\s H:i')}*\n\n"
            . "Até lá! 🐕";

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
            Log::warning('WhatsApp confirmação falhou', ['agendamento_id' => $this->agendamentoId, 'error' => $e->getMessage()]);
            throw $e;
        } finally {
            NotificacaoLog::create([
                'agendamento_id' => $agendamento->id,
                'type'           => 'confirmacao',
                'sent_at'        => now(),
                'status'         => $status,
                'payload'        => $payload,
            ]);
        }
    }
}
