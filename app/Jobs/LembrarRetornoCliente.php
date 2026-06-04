<?php

namespace App\Jobs;

use App\Models\Agendamento;
use App\Models\NotificacaoLog;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LembrarRetornoCliente implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function handle(): void
    {
        Pet::with(['cliente', 'agendamentos' => fn($q) => $q->where('status', 'concluido')->latest('scheduled_at')->limit(1)])
            ->whereHas('agendamentos', fn($q) => $q->where('status', 'concluido'))
            ->each(function ($pet) {
                $ultimoAgendamento = $pet->agendamentos->first();
                if (!$ultimoAgendamento) return;

                $dataRetorno = Carbon::parse($ultimoAgendamento->scheduled_at)->addDays($pet->retorno_dias);
                if (!$dataRetorno->isToday()) return;

                $mensagem = "🐾 *Hora do retorno do {$pet->name}!*\n\n"
                    . "Olá {$pet->cliente->name}! Já faz {$pet->retorno_dias} dias desde o último atendimento.\n\n"
                    . "Que tal agendar um novo banho ou tosa para o {$pet->name}? 🛁\n\n"
                    . "Entre em contato ou agende online!";

                $payload = [
                    'instance' => config('services.whatsapp.instance'),
                    'number'   => $pet->cliente->phone,
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
                    Log::warning('WhatsApp retorno falhou', ['pet_id' => $pet->id]);
                } finally {
                    NotificacaoLog::create([
                        'agendamento_id' => $ultimoAgendamento->id,
                        'type'           => 'retorno',
                        'sent_at'        => now(),
                        'status'         => $status,
                        'payload'        => $payload,
                    ]);
                }
            });
    }
}
