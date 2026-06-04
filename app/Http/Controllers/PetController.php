<?php

namespace App\Http\Controllers;

use App\DTOs\PetData;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Cliente;
use App\Models\Pet;
use App\Services\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PetController extends Controller
{
    public function __construct(
        private readonly PetService $petService,
    ) {}

    /**
     * Exibe a ficha completa do pet com vacinas e histórico de atendimentos.
     */
    public function show(Cliente $cliente, Pet $pet): View
    {
        $pet->load([
            'vacinas',
            'agendamentos' => fn ($q) => $q->with(['servico', 'colaborador'])->orderByDesc('scheduled_at'),
        ]);

        return view('pets.show', compact('cliente', 'pet'));
    }

    /**
     * Exibe o formulário de cadastro de pet.
     */
    public function create(Cliente $cliente): View
    {
        return view('pets.create', compact('cliente'));
    }

    /**
     * Armazena um novo pet no banco de dados.
     */
    public function store(StorePetRequest $request, Cliente $cliente): RedirectResponse
    {
        $this->petService->criar(
            PetData::fromRequest($request->validated()),
            $cliente->id,
            $request->file('photo')
        );

        return redirect()->route('clientes.show', $cliente)->with('success', 'Pet cadastrado com sucesso!');
    }

    /**
     * Exibe o formulário de edição de pet.
     */
    public function edit(Cliente $cliente, Pet $pet): View
    {
        return view('pets.edit', compact('cliente', 'pet'));
    }

    /**
     * Atualiza os dados de um pet existente.
     */
    public function update(UpdatePetRequest $request, Cliente $cliente, Pet $pet): RedirectResponse
    {
        $this->petService->atualizar(
            $pet,
            PetData::fromRequest($request->validated()),
            $request->file('photo')
        );

        return redirect()->route('clientes.show', $cliente)->with('success', 'Pet atualizado!');
    }

    /**
     * Remove um pet e sua foto do banco de dados e storage.
     */
    public function destroy(Cliente $cliente, Pet $pet): RedirectResponse
    {
        if ($pet->photo) {
            Storage::disk('public')->delete($pet->photo);
        }
        $pet->delete();

        return redirect()->route('clientes.show', $cliente)->with('success', 'Pet removido.');
    }

    /**
     * Retorna a ficha completa do pet em JSON.
     */
    public function fichaCompleta(Pet $pet): JsonResponse
    {
        $pet->load([
            'vacinas',
            'cliente',
            'agendamentos' => fn ($q) => $q->with(['servico', 'colaborador'])->orderByDesc('scheduled_at'),
        ]);

        return response()->json($pet);
    }
}
