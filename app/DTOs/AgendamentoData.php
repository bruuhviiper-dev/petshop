<?php

namespace App\DTOs;

use Carbon\Carbon;

/**
 * Data Transfer Object para criação e atualização de agendamentos.
 */
readonly class AgendamentoData
{
    public function __construct(
        public int $clienteId,
        public int $petId,
        public int $servicoId,
        public ?int $colaboradorId,
        public Carbon $scheduledAt,
        public ?string $notes = null,
    ) {}

    /**
     * Cria uma instância a partir de um array de dados validados.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            clienteId: (int) $data['cliente_id'],
            petId: (int) $data['pet_id'],
            servicoId: (int) $data['servico_id'],
            colaboradorId: isset($data['colaborador_id']) && $data['colaborador_id'] !== ''
                ? (int) $data['colaborador_id']
                : null,
            scheduledAt: Carbon::parse($data['scheduled_at']),
            notes: $data['notes'] ?? null,
        );
    }
}
