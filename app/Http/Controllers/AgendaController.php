<?php

namespace App\Http\Controllers;

use App\Events\AgendamentoCriado;
use App\Http\Requests\StoreAgendamentoRequest;
use App\Models\Agendamento;
use App\Models\Colaborador;
use App\Models\Servico;
use App\Repositories\AgendamentoRepository;
use App\Services\AgendamentoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function __construct(
        private readonly AgendamentoService $agendamentoService,
        private readonly AgendamentoRepository $agendamentoRepository,
    ) {}

    /**
     * Exibe a agenda do dia especificado (ou hoje, se não informado).
     */
    public function index(Request $request, ?string $data = null): View
    {
        $petshopId = auth()->user()->currentPetshopId();
        $date      = $data ? Carbon::parse($data) : Carbon::today();

        $agendamentos = $this->agendamentoRepository
            ->findByDateAndPetshop($date, $petshopId)
            ->groupBy('colaborador_id');

        $colaboradores = Colaborador::with('user')->get();
        $servicos      = Servico::where('active', true)->get();

        $proximosCinco = Agendamento::with(['pet', 'servico'])
            ->whereDate('scheduled_at', $date)
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['pendente', 'confirmado'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return view('agenda.index', compact('agendamentos', 'colaboradores', 'servicos', 'date', 'proximosCinco'));
    }

    /**
     * Retorna os agendamentos da semana em JSON.
     */
    public function semana(Request $request): JsonResponse
    {
        $inicio = Carbon::parse($request->get('inicio', Carbon::now()->startOfWeek()));
        $fim    = $inicio->copy()->endOfWeek();

        $agendamentos = Agendamento::with(['cliente', 'pet', 'servico', 'colaborador'])
            ->whereBetween('scheduled_at', [$inicio, $fim])
            ->orderBy('scheduled_at')
            ->get();

        return response()->json($agendamentos);
    }

    /**
     * Cria um novo agendamento a partir dos dados do formulário interno.
     */
    public function store(StoreAgendamentoRequest $request): JsonResponse|RedirectResponse
    {
        $servico = Servico::findOrFail($request->servico_id);

        // Campos da tabela (sem os de recorrência) + valor do serviço.
        $base = collect($request->validated())
            ->except(['recorrencia', 'repeticoes'])
            ->put('valor', $servico->price)
            ->all();

        // Recorrência: gera N agendamentos no intervalo escolhido (semanal/quinzenal/mensal).
        $intervalo = match ($request->input('recorrencia')) {
            'weekly'   => 7,
            'biweekly' => 14,
            'monthly'  => 30,
            default    => 0,
        };
        $total = $intervalo > 0 ? max(1, min((int) $request->input('repeticoes', 1), 52)) : 1;

        $inicio   = Carbon::parse($base['scheduled_at']);
        $primeiro = null;

        for ($i = 0; $i < $total; $i++) {
            $dados = $base;
            $dados['scheduled_at'] = $inicio->copy()->addDays($intervalo * $i);
            $agendamento = Agendamento::create($dados);

            // Dispara a notificação de confirmação apenas para o primeiro da série.
            if ($i === 0) {
                $primeiro = $agendamento;
                event(new AgendamentoCriado($agendamento));
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success'     => true,
                'total'       => $total,
                'agendamento' => $primeiro->load(['cliente', 'pet', 'servico', 'colaborador']),
            ]);
        }

        $msg = $total > 1 ? "{$total} agendamentos recorrentes criados!" : 'Agendamento criado com sucesso!';
        return redirect()->route('agenda.index')->with('success', $msg);
    }

    /**
     * Atualiza o status de um agendamento existente.
     */
    public function updateStatus(Request $request, Agendamento $agendamento): JsonResponse
    {
        $validated = $request->validate([
            'status'         => 'required|in:pendente,confirmado,em_andamento,concluido,cancelado',
            'colaborador_id' => 'nullable|exists:colaboradores,id',
            'scheduled_at'   => 'nullable|date',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $agendamento = $this->agendamentoService->atualizarStatus($agendamento, $validated['status']);

        // Atribuição do responsável e demais campos (regra: agendamento entra pendente
        // e sem colaborador; o admin/colaborador o assume aqui).
        $updates = [];
        if ($request->has('colaborador_id')) {
            $updates['colaborador_id'] = $validated['colaborador_id'] ?: null;
        }
        if ($request->filled('scheduled_at')) {
            $updates['scheduled_at'] = $validated['scheduled_at'];
        }
        if ($request->has('notes')) {
            $updates['notes'] = $validated['notes'];
        }
        if ($updates) {
            $agendamento->update($updates);
        }

        return response()->json([
            'success'     => true,
            'agendamento' => $agendamento->fresh(['cliente', 'pet', 'servico', 'colaborador']),
        ]);
    }
}
