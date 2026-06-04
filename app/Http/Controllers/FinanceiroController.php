<?php

namespace App\Http\Controllers;

use App\Models\Financeiro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $query = Financeiro::with('agendamento.colaborador')
            ->when($request->inicio, fn($q) => $q->where('paid_at', '>=', $request->inicio))
            ->when($request->fim, fn($q) => $q->where('paid_at', '<=', $request->fim . ' 23:59:59'))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderByDesc('paid_at');

        $receita = (clone $query)->where('type', 'receita')->sum('amount');
        $despesa = (clone $query)->where('type', 'despesa')->sum('amount');
        $lucro = $receita - $despesa;
        $ticketMedio = (clone $query)->where('type', 'receita')->avg('amount') ?? 0;

        $registros = $query->paginate(25)->withQueryString();

        return view('financeiro.index', compact('registros', 'receita', 'despesa', 'lucro', 'ticketMedio'));
    }

    public function exportar(Request $request)
    {
        $query = Financeiro::with('agendamento')
            ->when($request->inicio, fn($q) => $q->where('paid_at', '>=', $request->inicio))
            ->when($request->fim, fn($q) => $q->where('paid_at', '<=', $request->fim . ' 23:59:59'))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderByDesc('paid_at');

        $filename = 'financeiro_' . now()->format('Y-m-d') . '.csv';

        return Response::streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Tipo', 'Valor', 'Descrição', 'Pago em'], ';');
            $query->chunk(200, function ($registros) use ($handle) {
                foreach ($registros as $r) {
                    fputcsv($handle, [
                        $r->id,
                        $r->type,
                        number_format($r->amount, 2, ',', '.'),
                        $r->description,
                        $r->paid_at?->format('d/m/Y H:i') ?? '',
                    ], ';');
                }
            });
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
