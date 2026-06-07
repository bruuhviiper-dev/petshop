<?php

namespace App\Policies;

use App\Models\Agendamento;
use App\Models\User;

class AgendamentoPolicy
{
    public function view(User $user, Agendamento $agendamento): bool
    {
        return $user->currentPetshopId() && $user->currentPetshopId() === $agendamento->petshop_id;
    }

    public function update(User $user, Agendamento $agendamento): bool
    {
        return $user->currentPetshopId() && $user->currentPetshopId() === $agendamento->petshop_id;
    }

    public function delete(User $user, Agendamento $agendamento): bool
    {
        return $user->currentPetshopId() && $user->currentPetshopId() === $agendamento->petshop_id;
    }
}
