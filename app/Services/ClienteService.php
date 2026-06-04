<?php

namespace App\Services;

use App\DTOs\ClienteData;
use App\Models\Cliente;
use Illuminate\Support\Collection;

/**
 * Service responsável pela lógica de negócio de clientes.
 */
class ClienteService
{
    /**
     * Cria um novo cliente para um petshop.
     */
    public function criar(ClienteData $data, int $petshopId): Cliente
    {
        return Cliente::create([
            'petshop_id' => $petshopId,
            'name'       => $data->name,
            'phone'      => $data->phone,
            'email'      => $data->email,
            'birthdate'  => $data->birthdate,
        ]);
    }

    /**
     * Atualiza os dados de um cliente existente.
     */
    public function atualizar(Cliente $cliente, ClienteData $data): Cliente
    {
        $cliente->update([
            'name'      => $data->name,
            'phone'     => $data->phone,
            'email'     => $data->email,
            'birthdate' => $data->birthdate,
        ]);

        return $cliente->fresh();
    }

    /**
     * Busca clientes de um petshop por nome ou telefone.
     *
     * @return Collection<int, Cliente>
     */
    public function buscar(string $query, int $petshopId): Collection
    {
        return Cliente::with('pets')
            ->where('petshop_id', $petshopId)
            ->where(function ($q) use ($query): void {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();
    }

    /**
     * Cria ou recupera um cliente pelo telefone, atualizando o nome se necessário.
     */
    public function firstOrCreate(int $petshopId, string $phone, string $name): Cliente
    {
        return Cliente::firstOrCreate(
            ['petshop_id' => $petshopId, 'phone' => $phone],
            ['name' => $name, 'petshop_id' => $petshopId]
        );
    }
}
