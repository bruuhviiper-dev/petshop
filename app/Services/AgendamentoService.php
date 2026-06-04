<?php

namespace App\Services;

use App\DTOs\AgendamentoData;
use App\Events\AgendamentoConcluido;
use App\Models\Agendamento;
use App\Models\Horario;
use App\Repositories\AgendamentoRepository;
use Carbon\Carbon;

/**
 * Service responsável pela lógica de negócio de agendamentos.
 */
class AgendamentoService
{
    public function __construct(
        private readonly AgendamentoRepository $repository,
    ) {}

    /**
     * Cria um novo agendamento com o valor do serviço associado.
     */
    public function criar(AgendamentoData $data, int $petshopId): Agendamento
    {
        $servico = \App\Models\Servico::find($data->servicoId);

        $agendamento = Agendamento::create([
            'petshop_id'     => $petshopId,
            'cliente_id'     => $data->clienteId,
            'pet_id'         => $data->petId,
            'servico_id'     => $data->servicoId,
            'colaborador_id' => $data->colaboradorId,
            'scheduled_at'   => $data->scheduledAt,
            'notes'          => $data->notes,
            'status'         => 'pendente',
            'valor'          => $servico?->price ?? 0,
        ]);

        return $agendamento->load(['cliente', 'pet', 'servico', 'colaborador']);
    }

    /**
     * Atualiza o status de um agendamento e dispara eventos relacionados.
     */
    public function atualizarStatus(Agendamento $agendamento, string $status): Agendamento
    {
        $oldStatus = $agendamento->status;
        $agendamento->update(['status' => $status]);

        if ($status === 'concluido' && $oldStatus !== 'concluido') {
            event(new AgendamentoConcluido($agendamento));
        }

        return $agendamento->fresh();
    }

    /**
     * Calcula os slots de horário disponíveis para agendamento em uma data.
     *
     * @return array<string>
     */
    public function calcularSlotsDisponiveis(Carbon $date, int $petshopId, int $duracaoMinutos): array
    {
        $weekday = $date->dayOfWeek;

        $horario = Horario::where('petshop_id', $petshopId)
            ->where('weekday', $weekday)
            ->first();

        if (!$horario || $horario->closed) {
            return [];
        }

        $abertura   = Carbon::parse($date->format('Y-m-d') . ' ' . $horario->open);
        $fechamento = Carbon::parse($date->format('Y-m-d') . ' ' . $horario->close);
        $ocupados   = $this->repository->getSlotsOcupados($date, $petshopId, $duracaoMinutos);

        $slots   = [];
        $current = $abertura->copy();

        while ($current->copy()->addMinutes($duracaoMinutos)->lte($fechamento)) {
            $hora = $current->format('H:i');
            if (!in_array($hora, $ocupados)) {
                $slots[] = $hora;
            }
            $current->addMinutes(30);
        }

        return $slots;
    }
}
