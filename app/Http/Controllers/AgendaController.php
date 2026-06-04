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
        $petshopId = auth()->user()->petshop->id;
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
        $servico     = Servico::findOrFail($request->servico_id);
        $agendamento = Agendamento::create(array_merge(
            $request->validated(),
            ['valor' => $servico->price]
        ));

        event(new AgendamentoCriado($agendamento));

        if ($request->expectsJson()) {
            return response()->json([
                'success'     => true,
                'agendamento' => $agendamento->load(['cliente', 'pet', 'servico', 'colaborador']),
            ]);
        }

        return redirect()->route('agenda.index')->with('success', 'Agendamento criado com sucesso!');
    }

    /**
     * Atualiza o status de um agendamento existente.
     */
    public function updateStatus(Request $request, Agendamento $agendamento): JsonResponse
    {
        $request->validate(['status' => 'required|in:pendente,confirmado,em_andamento,concluido,cancelado']);

        $agendamento = $this->agendamentoService->atualizarStatus($agendamento, $request->status);

        if ($request->has('scheduled_at')) {
            $agendamento->update(['scheduled_at' => $request->scheduled_at]);
        }

        return response()->json(['success' => true, 'agendamento' => $agendamento->fresh()]);
    }
}
