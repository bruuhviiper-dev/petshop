<?php

namespace App\Jobs;

use App\Models\Agendamento;
use App\Models\NotificacaoLog;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnviarLembrete24h implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function handle(): void
    {
        $amanha = Carbon::tomorrow();

        Agendamento::with(['cliente', 'pet', 'servico', 'petshop'])
            ->whereDate('scheduled_at', $amanha)
            ->where('status', 'confirmado')
            ->each(function ($agendamento) {
                $mensagem = "🔔 *Lembrete de Agendamento — {$agendamento->petshop->name}*\n\n"
                    . "Olá {$agendamento->cliente->name}! Lembrando do agendamento de amanhã:\n\n"
                    . "🐾 Pet: *{$agendamento->pet->name}*\n"
                    . "✂️ Serviço: *{$agendamento->servico->name}*\n"
                    . "📅 Horário: *{$agendamento->scheduled_at->format('H:i')}*\n\n"
                    . "Até amanhã! 🐕";

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
                    Log::warning('WhatsApp lembrete24h falhou', ['agendamento_id' => $agendamento->id]);
                } finally {
                    NotificacaoLog::create([
                        'agendamento_id' => $agendamento->id,
                        'type'           => 'lembrete_24h',
                        'sent_at'        => now(),
                        'status'         => $status,
                        'payload'        => $payload,
                    ]);
                }
            });
    }
}
