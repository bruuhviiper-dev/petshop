<?php

namespace App\DTOs;

use Carbon\Carbon;

/**
 * Data Transfer Object para criação e atualização de clientes.
 */
readonly class ClienteData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email = null,
        public ?Carbon $birthdate = null,
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
            phone: $data['phone'],
            email: $data['email'] ?? null,
            birthdate: isset($data['birthdate']) && $data['birthdate']
                ? Carbon::parse($data['birthdate'])
                : null,
        );
    }
}
