<?php

namespace App\Repositories;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Colaborador;
use App\Models\Financeiro;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Repository para consultas de relatórios e métricas financeiras.
 */
class RelatorioRepository
{
    /**
     * Retorna o total de receitas de um petshop em um determinado mês.
     */
    public function faturamentoMes(int $petshopId, Carbon $mes): float
    {
        return (float) Financeiro::where('petshop_id', $petshopId)
            ->where('type', 'receita')
            ->whereBetween('paid_at', [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()])
            ->sum('amount');
    }

    /**
     * Retorna o faturamento diário dos últimos 30 dias para um petshop.
     *
     * @return Collection<int, array{date: string, total: float}>
     */
    public function faturamento30Dias(int $petshopId): Collection
    {
        return collect(range(29, 0))->map(function (int $daysAgo) use ($petshopId): array {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'date'  => $date->format('d/m'),
                'total' => (float) Financeiro::where('petshop_id', $petshopId)
                    ->where('type', 'receita')
                    ->whereDate('paid_at', $date)
                    ->sum('amount'),
            ];
        });
    }

    /**
     * Retorna os serviços mais realizados em um petshop.
     *
     * @return Collection<int, Agendamento>
     */
    public function topServicos(int $petshopId, int $limit = 3): Collection
    {
        return Agendamento::where('petshop_id', $petshopId)
            ->where('status', 'concluido')
            ->select('servico_id', DB::raw('count(*) as total'))
            ->with('servico')
            ->groupBy('servico_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Retorna os clientes com mais atendimentos concluídos em um petshop.
     *
     * @return Collection<int, Cliente>
     */
    public function topClientes(int $petshopId, int $limit = 5): Collection
    {
        return Cliente::where('petshop_id', $petshopId)
            ->withCount(['agendamentos' => fn ($q) => $q->where('status', 'concluido')])
            ->orderByDesc('agendamentos_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Retorna as comissões agrupadas por colaborador para um petshop.
     *
     * @return Collection<int, Colaborador>
     */
    public function comissoesPorColaborador(int $petshopId): Collection
    {
        return Colaborador::with(['user', 'comissoes.agendamento.servico'])
            ->where('petshop_id', $petshopId)
            ->get()
            ->each(function (Colaborador $col): void {
                $col->total_pendente = $col->comissoes->where('paid', false)->sum('amount');
                $col->total_pago     = $col->comissoes->where('paid', true)->sum('amount');
            });
    }
}
