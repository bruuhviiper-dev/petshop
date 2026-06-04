<?php

namespace App\DTOs;

/**
 * Data Transfer Object para criação e atualização de pets.
 */
readonly class PetData
{
    public function __construct(
        public string $name,
        public string $species,
        public ?string $breed = null,
        public ?float $weight = null,
        public ?string $temperament = null,
        public ?string $allergies = null,
        public ?string $notes = null,
        public int $retornoDias = 30,
    ) {}

    /**
     * Cria uma instância a partir de um array de dados validados.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            species: $data['species'],
            breed: $data['breed'] ?? null,
            weight: isset($data['weight']) && $data['weight'] !== '' ? (float) $data['weight'] : null,
            temperament: $data['temperament'] ?? null,
            allergies: $data['allergies'] ?? null,
            notes: $data['notes'] ?? null,
            retornoDias: isset($data['retorno_dias']) ? (int) $data['retorno_dias'] : 30,
        );
    }
}
