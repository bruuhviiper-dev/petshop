<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;

class PetPolicy
{
    public function view(User $user, Pet $pet): bool
    {
        return $user->petshop && $user->petshop->id === $pet->cliente->petshop_id;
    }

    public function update(User $user, Pet $pet): bool
    {
        return $user->petshop && $user->petshop->id === $pet->cliente->petshop_id;
    }

    public function delete(User $user, Pet $pet): bool
    {
        return $user->petshop && $user->petshop->id === $pet->cliente->petshop_id;
    }
}
