<?php

namespace App\Jobs;

use App\Models\Agendamento;
use App\Models\NotificacaoLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnviarLembrete1h implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function handle(): void
    {
        $de = now()->addHour()->startOfMinute();
        $ate = now()->addHour()->addMinutes(30);

        Agendamento::with(['cliente', 'pet', 'servico', 'petshop'])
            ->whereBetween('scheduled_at', [$de, $ate])
            ->where('status', 'confirmado')
            ->each(function ($agendamento) {
                $mensagem = "⏰ *Lembrete — {$agendamento->petshop->name}*\n\n"
                    . "Olá {$agendamento->cliente->name}! Seu agendamento é daqui a 1 hora:\n\n"
                    . "🐾 *{$agendamento->pet->name}* — {$agendamento->servico->name}\n"
                    . "🕐 Horário: *{$agendamento->scheduled_at->format('H:i')}*\n\n"
                    . "Não se esqueça! 🐾";

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
                    Log::warning('WhatsApp lembrete1h falhou', ['agendamento_id' => $agendamento->id]);
                } finally {
                    NotificacaoLog::create([
                        'agendamento_id' => $agendamento->id,
                        'type'           => 'lembrete_1h',
                        'sent_at'        => now(),
                        'status'         => $status,
                        'payload'        => $payload,
                    ]);
                }
            });
    }
}
