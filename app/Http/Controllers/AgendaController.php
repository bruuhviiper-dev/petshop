<?php

namespace App\Http\Controllers;

use App\Events\AgendamentoConcluido;
use App\Events\AgendamentoCriado;
use App\Http\Requests\StoreAgendamentoRequest;
use App\Models\Agendamento;
use App\Models\Colaborador;
use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request, ?string $data = null)
    {
        $date = $data ? Carbon::parse($data) : Carbon::today();

        $agendamentos = Agendamento::with(['cliente', 'pet', 'servico', 'colaborador'])
            ->whereDate('scheduled_at', $date)
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy('colaborador_id');

        $colaboradores = Colaborador::with('user')->get();
        $servicos = Servico::where('active', true)->get();

        $proximosCinco = Agendamento::with(['pet', 'servico'])
            ->whereDate('scheduled_at', $date)
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['pendente', 'confirmado'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return view('agenda.index', compact('agendamentos', 'colaboradores', 'servicos', 'date', 'proximosCinco'));
    }

    public function semana(Request $request)
    {
        $inicio = Carbon::parse($request->get('inicio', Carbon::now()->startOfWeek()));
        $fim = $inicio->copy()->endOfWeek();

        $agendamentos = Agendamento::with(['cliente', 'pet', 'servico', 'colaborador'])
            ->whereBetween('scheduled_at', [$inicio, $fim])
            ->orderBy('scheduled_at')
            ->get();

        return response()->json($agendamentos);
    }

    public function store(StoreAgendamentoRequest $request)
    {
        $servico = Servico::findOrFail($request->servico_id);
        $agendamento = Agendamento::create(array_merge(
            $request->validated(),
            ['valor' => $servico->price]
        ));

        event(new AgendamentoCriado($agendamento));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'agendamento' => $agendamento->load(['cliente', 'pet', 'servico', 'colaborador'])]);
        }

        return redirect()->route('agenda.index')->with('success', 'Agendamento criado com sucesso!');
    }

    public function updateStatus(Request $request, Agendamento $agendamento)
    {
        $request->validate(['status' => 'required|in:pendente,confirmado,em_andamento,concluido,cancelado']);

        $oldStatus = $agendamento->status;
        $agendamento->update($request->only('status', 'scheduled_at'));

        if ($agendamento->status === 'concluido' && $oldStatus !== 'concluido') {
            event(new AgendamentoConcluido($agendamento));
        }

        return response()->json(['success' => true, 'agendamento' => $agendamento->fresh()]);
    }
}
