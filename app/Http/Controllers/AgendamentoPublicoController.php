<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendamentoPublicoRequest;
use App\Jobs\EnviarConfirmacaoAgendamento;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Horario;
use App\Models\Pet;
use App\Models\Petshop;
use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendamentoPublicoController extends Controller
{
    public function show(string $slug)
    {
        $petshop = Petshop::where('slug', $slug)->where('active', true)->firstOrFail();
        $servicos = Servico::where('petshop_id', $petshop->id)->where('active', true)->get();
        return view('publico.agendar', compact('petshop', 'servicos'));
    }

    public function horarios(Request $request, string $slug)
    {
        $petshop = Petshop::where('slug', $slug)->where('active', true)->firstOrFail();
        $date = Carbon::parse($request->get('data', today()));
        $weekday = $date->dayOfWeek;

        $horario = Horario::where('petshop_id', $petshop->id)
            ->where('weekday', $weekday)
            ->first();

        if (!$horario || $horario->closed) {
            return response()->json(['slots' => []]);
        }

        $servicoId = $request->get('servico_id');
        $duracao = 60;
        if ($servicoId) {
            $servico = Servico::where('petshop_id', $petshop->id)->find($servicoId);
            if ($servico) $duracao = $servico->duration_minutes;
        }

        $abertura = Carbon::parse($date->format('Y-m-d') . ' ' . $horario->open);
        $fechamento = Carbon::parse($date->format('Y-m-d') . ' ' . $horario->close);

        $agendados = Agendamento::where('petshop_id', $petshop->id)
            ->whereDate('scheduled_at', $date)
            ->whereNotIn('status', ['cancelado'])
            ->pluck('scheduled_at')
            ->map(fn($d) => Carbon::parse($d)->format('H:i'))
            ->toArray();

        $slots = [];
        $current = $abertura->copy();
        while ($current->copy()->addMinutes($duracao)->lte($fechamento)) {
            $hora = $current->format('H:i');
            if (!in_array($hora, $agendados)) {
                $slots[] = $hora;
            }
            $current->addMinutes(30);
        }

        return response()->json(['slots' => $slots]);
    }

    public function store(AgendamentoPublicoRequest $request, string $slug)
    {
        $petshop = Petshop::where('slug', $slug)->where('active', true)->firstOrFail();
        $servico = Servico::where('petshop_id', $petshop->id)->findOrFail($request->servico_id);

        $cliente = Cliente::firstOrCreate(
            ['petshop_id' => $petshop->id, 'phone' => $request->dono_telefone],
            ['name' => $request->dono_nome, 'petshop_id' => $petshop->id]
        );

        $pet = Pet::firstOrCreate(
            ['cliente_id' => $cliente->id, 'name' => $request->pet_nome],
            [
                'cliente_id' => $cliente->id,
                'species'    => $request->pet_especie,
                'breed'      => $request->pet_raca,
            ]
        );

        $agendamento = Agendamento::create([
            'petshop_id'  => $petshop->id,
            'cliente_id'  => $cliente->id,
            'pet_id'      => $pet->id,
            'servico_id'  => $servico->id,
            'scheduled_at'=> Carbon::parse($request->data . ' ' . $request->horario),
            'valor'       => $servico->price,
            'status'      => 'pendente',
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
