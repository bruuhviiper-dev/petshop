<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Financeiro;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();
        $inicioMesAnterior = Carbon::now()->subMonth()->startOfMonth();
        $fimMesAnterior = Carbon::now()->subMonth()->endOfMonth();

        $agendamentosHoje = Agendamento::whereDate('scheduled_at', $hoje)->get();
        $porStatus = $agendamentosHoje->groupBy('status')->map->count();

        $totalSlots = 20;
        $taxaOcupacao = $totalSlots > 0
            ? round(($agendamentosHoje->count() / $totalSlots) * 100)
            : 0;

        $faturamentoMes = Financeiro::where('type', 'receita')
            ->whereBetween('paid_at', [$inicioMes, $fimMes])
            ->sum('amount');

        $faturamentoMesAnterior = Financeiro::where('type', 'receita')
            ->whereBetween('paid_at', [$inicioMesAnterior, $fimMesAnterior])
            ->sum('amount');

        $topClientes = Cliente::withCount(['agendamentos' => fn($q) => $q->where('status', 'concluido')])
            ->orderByDesc('agendamentos_count')
            ->limit(5)
            ->get();

        $topServicos = Agendamento::where('status', 'concluido')
            ->select('servico_id', DB::raw('count(*) as total'))
            ->with('servico')
            ->groupBy('servico_id')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $proximosAgendamentos = Agendamento::with(['pet', 'servico', 'colaborador'])
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['pendente', 'confirmado'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $faturamento30Dias = collect(range(29, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date'  => $date->format('d/m'),
                'total' => Financeiro::where('type', 'receita')
                    ->whereDate('paid_at', $date)
                    ->sum('amount'),
            ];
        });

        $petsRetornoAtrasado = Pet::with('cliente')
            ->whereHas('agendamentos', function ($q) {
                $q->where('status', 'concluido');
            })
            ->get()
            ->filter(function ($pet) {
                $ultimo = $pet->agendamentos()
                    ->where('status', 'concluido')
                    ->latest('scheduled_at')
                    ->first();
                if (!$ultimo) return false;
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
