<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Pet;
use App\Repositories\RelatorioRepository;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RelatorioRepository $relatorioRepository,
    ) {}

    /**
     * Exibe o painel principal com métricas, gráficos e próximos agendamentos.
     */
    public function index(): View
    {
        $petshopId = auth()->user()->petshop->id;
        $hoje      = Carbon::today();
        $mesAtual  = Carbon::now();

        $agendamentosHoje = Agendamento::where('petshop_id', $petshopId)
            ->whereDate('scheduled_at', $hoje)
            ->get();
        $porStatus    = $agendamentosHoje->groupBy('status')->map->count();
        $totalSlots   = 20;
        $taxaOcupacao = $totalSlots > 0
            ? round(($agendamentosHoje->count() / $totalSlots) * 100)
            : 0;

        $faturamentoMes      = $this->relatorioRepository->faturamentoMes($petshopId, $mesAtual);
        $faturamentoMesAnterior = $this->relatorioRepository->faturamentoMes($petshopId, $mesAtual->copy()->subMonth());
        $topClientes         = $this->relatorioRepository->topClientes($petshopId);
        $topServicos         = $this->relatorioRepository->topServicos($petshopId);
        $faturamento30Dias   = $this->relatorioRepository->faturamento30Dias($petshopId);

        $proximosAgendamentos = Agendamento::with(['pet', 'servico', 'colaborador'])
            ->where('petshop_id', $petshopId)
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['pendente', 'confirmado'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $petsRetornoAtrasado = Pet::with('cliente')
            ->whereHas('agendamentos', fn ($q) => $q->where('status', 'concluido')->where('petshop_id', $petshopId))
            ->get()
            ->filter(function (Pet $pet): bool {
                $ultimo = $pet->agendamentos()
                    ->where('status', 'concluido')
                    ->latest('scheduled_at')
                    ->first();
                if (!$ultimo) {
                    return false;
                }

                return Carbon::parse($ultimo->scheduled_at)->addDays($pet->retorno_dias)->isPast();
            })->take(10);

        return view('dashboard', compact(
            'agendamentosHoje',
            'porStatus',
            'taxaOcupacao',
            'faturamentoMes',
            'faturamentoMesAnterior',
            'topClientes',
            'topServicos',
            'proximosAgendamentos',
            'faturamento30Dias',
            'petsRetornoAtrasado'
        ));
    }
}
