<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\View\View;

class PetCarteirinhaController extends Controller
{
    /**
     * Carteirinha digital pública do pet (acesso por token, sem login).
     */
    public function show(string $token): View
    {
        $pet = Pet::where('public_token', $token)
            ->with([
                'cliente.petshop',
                'vacinas' => fn ($q) => $q->orderByDesc('date_applied'),
            ])
            ->firstOrFail();

        $petshop = $pet->cliente?->petshop;
        abort_if(!$petshop, 404);

        return view('publico.carteirinha', compact('pet', 'petshop'));
    }
}
