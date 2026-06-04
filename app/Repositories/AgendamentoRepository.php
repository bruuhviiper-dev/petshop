<?php

namespace App\Repositories;

use App\Models\Agendamento;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Repository para consultas de agendamentos.
 */
class AgendamentoRepository
{
    /**
     * Busca todos os agendamentos de um dia específico para um petshop.
     */
    public function findByDateAndPetshop(Carbon $date, int $petshopId): Collection
    {
        return Agendamento::with(['cliente', 'pet', 'servico', 'colaborador'])
            ->where('petshop_id', $petshopId)
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Retorna os próximos agendamentos pendentes ou confirmados de um petshop.
     */
    public function findUpcoming(int $petshopId, int $limit = 5): Collection
    {
        return Agendamento::with(['pet', 'servico', 'colaborador'])
            ->where('petshop_id', $petshopId)
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['pendente', 'confirmado'])
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Retorna agendamentos confirmados para amanhã (para envio de lembrete 24h via WhatsApp).
     */
    public function findForWhatsapp24h(): Collection
    {
        $amanha = Carbon::tomorrow();

        return Agendamento::with(['cliente', 'pet', 'servico', 'petshop'])
            ->where('status', 'confirmado')
            ->whereDate('scheduled_at', $amanha)
            ->get();
    }

    /**
     * Retorna agendamentos confirmados na próxima hora (para envio de lembrete 1h via WhatsApp).
     */
    public function findForWhatsapp1h(): Collection
    {
        $inicio = now()->addMinutes(55);
        $fim    = now()->addMinutes(65);

        return Agendamento::with(['cliente', 'pet', 'servico', 'petshop'])
            ->where('status', 'confirmado')
            ->whereBetween('scheduled_at', [$inicio, $fim])
            ->get();
    }

    /**
     * Retorna as horas já ocupadas em um dia para um petshop, dado o tempo de duração de serviço.
     *
     * @return array<string>
     */
    public function getSlotsOcupados(Carbon $date, int $petshopId, int $duracaoMinutos): array
    {
        return Agendamento::where('petshop_id', $petshopId)
            ->whereDate('scheduled_at', $date)
            ->whereNotIn('status', ['cancelado'])
            ->pluck('scheduled_at')
            ->map(fn ($d) => Carbon::parse($d)->format('H:i'))
            ->toArray();
    }
}
