<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendamentoPublicoRequest;
use App\Jobs\EnviarConfirmacaoAgendamento;
use App\Models\Agendamento;
use App\Models\Pet;
use App\Models\Petshop;
use App\Models\Servico;
use App\Services\AgendamentoService;
use App\Services\ClienteService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendamentoPublicoController extends Controller
{
    public function __construct(
        private readonly AgendamentoService $agendamentoService,
        private readonly ClienteService $clienteService,
    ) {}

    /**
     * Exibe a página pública de agendamento para um petshop.
     */
    public function show(string $slug): View
    {
        $petshop  = Petshop::where('slug', $slug)->where('active', true)->firstOrFail();
        $servicos = Servico::where('petshop_id', $petshop->id)->where('active', true)->get();

        return view('publico.agendar', compact('petshop', 'servicos'));
    }

    /**
     * Retorna os slots de horário disponíveis para uma data e serviço.
     */
    public function horarios(Request $request, string $slug): JsonResponse
    {
        $petshop = Petshop::where('slug', $slug)->where('active', true)->firstOrFail();
        $date    = Carbon::parse($request->get('data', today()));

        $servicoId = $request->get('servico_id');
        $duracao   = 60;

        if ($servicoId) {
            $servico = Servico::where('petshop_id', $petshop->id)->find($servicoId);
            if ($servico) {
                $duracao = $servico->duration_minutes;
            }
        }

        $slots = $this->agendamentoService->calcularSlotsDisponiveis($date, $petshop->id, $duracao);

        return response()->json(['slots' => $slots]);
    }

    /**
     * Cria um novo agendamento público pelo formulário de 3 etapas.
     */
    public function store(AgendamentoPublicoRequest $request, string $slug): JsonResponse
    {
        $petshop = Petshop::where('slug', $slug)->where('active', true)->firstOrFail();
        $servico = Servico::where('petshop_id', $petshop->id)->findOrFail($request->servico_id);

        $cliente = $this->clienteService->firstOrCreate(
            $petshop->id,
            $request->dono_telefone,
            $request->dono_nome
        );

        $pet = Pet::firstOrCreate(
            ['cliente_id' => $cliente->id, 'name' => $request->pet_nome],
            [
                'cliente_id' => $cliente->id,
                'species'    => $request->pet_especie,
                'breed'      => $request->pet_raca,
                'retorno_dias' => 30,
            ]
        );

        $agendamento = Agendamento::create([
            'petshop_id'   => $petshop->id,
            'cliente_id'   => $cliente->id,
            'pet_id'       => $pet->id,
            'servico_id'   => $servico->id,
            'scheduled_at' => Carbon::parse($request->data . ' ' . $request->horario),
            'valor'        => $servico->price,
            'status'       => 'pendente',
        ]);

        dispatch(new EnviarConfirmacaoAgendamento($agendamento->id));

        return response()->json([
            'success'     => true,
            'agendamento' => [
                'id'           => $agendamento->id,
                'petshop'      => $petshop->name,
                'pet'          => $pet->name,
                'servico'      => $servico->name,
                'scheduled_at' => $agendamento->scheduled_at->format('d/m/Y H:i'),
                'valor'        => 'R$ ' . number_format($servico->price, 2, ',', '.'),
            ],
        ]);
    }
}
