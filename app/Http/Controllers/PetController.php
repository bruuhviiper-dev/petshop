<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Cliente;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function show(Cliente $cliente, Pet $pet)
    {
        $pet->load(['vacinas', 'agendamentos' => fn($q) => $q->with(['servico', 'colaborador'])->orderByDesc('scheduled_at')]);
        return view('pets.show', compact('cliente', 'pet'));
    }

    public function create(Cliente $cliente)
    {
        return view('pets.create', compact('cliente'));
    }

    public function store(StorePetRequest $request, Cliente $cliente)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('pets', 'public');
        }
        $pet = $cliente->pets()->create($data);
        return redirect()->route('clientes.show', $cliente)->with('success', 'Pet cadastrado com sucesso!');
    }

    public function edit(Cliente $cliente, Pet $pet)
    {
        return view('pets.edit', compact('cliente', 'pet'));
    }

    public function update(UpdatePetRequest $request, Cliente $cliente, Pet $pet)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            if ($pet->photo) Storage::disk('public')->delete($pet->photo);
            $data['photo'] = $request->file('photo')->store('pets', 'public');
        }
        $pet->update($data);
        return redirect()->route('clientes.show', $cliente)->with('success', 'Pet atualizado!');
    }

    public function destroy(Cliente $cliente, Pet $pet)
    {
        if ($pet->photo) Storage::disk('public')->delete($pet->photo);
        $pet->delete();
        return redirect()->route('clientes.show', $cliente)->with('success', 'Pet removido.');
    }

    public function fichaCompleta(Pet $pet)
    {
        $pet->load(['vacinas', 'cliente', 'agendamentos' => fn($q) => $q->with(['servico', 'colaborador'])->orderByDesc('scheduled_at')]);
        return response()->json($pet);
    }
}
