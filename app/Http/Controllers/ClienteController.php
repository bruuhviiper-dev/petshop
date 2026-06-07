<?php

namespace App\Http\Controllers;

use App\DTOs\ClienteData;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\ClienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function __construct(
        private readonly ClienteService $clienteService,
    ) {}

    /**
     * Lista os clientes do petshop com paginação e busca.
     */
    public function index(Request $request): View
    {
        $clientes = Cliente::withCount('pets')
            ->when($request->q, fn ($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    /**
     * Exibe a ficha completa de um cliente com seus pets e histórico.
     */
    public function show(Cliente $cliente): View
    {
        $cliente->load([
            'pets.vacinas',
            'agendamentos' => fn ($q) => $q->with(['servico', 'colaborador'])->orderByDesc('scheduled_at')->limit(20),
        ]);

        return view('clientes.show', compact('cliente'));
    }

    /**
     * Exibe o formulário de criação de cliente.
     */
    public function create(): View
    {
        return view('clientes.create');
    }

    /**
     * Armazena um novo cliente no banco de dados.
     */
    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $cliente = $this->clienteService->criar(
            ClienteData::fromRequest($request->validated()),
            auth()->user()->currentPetshopId()
        );

        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Exibe o formulário de edição de cliente.
     */
    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Atualiza os dados de um cliente existente.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $this->clienteService->atualizar(
            $cliente,
            ClienteData::fromRequest($request->validated())
        );

        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente atualizado!');
    }

    /**
     * Remove um cliente do banco de dados.
     */
    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente removido.');
    }

    /**
     * Busca clientes por nome ou telefone para uso em autocomplete.
     */
    public function buscar(Request $request): JsonResponse
    {
        $query    = $request->get('q', '');
        $clientes = $this->clienteService
            ->buscar($query, auth()->user()->currentPetshopId())
            ->map(fn ($c) => [
                'id'    => $c->id,
                'name'  => $c->name,
                'phone' => $c->phone,
                'pets'  => $c->pets->map(fn ($p) => [
                    'id'      => $p->id,
                    'name'    => $p->name,
                    'species' => $p->species,
                ]),
            ]);

        return response()->json($clientes);
    }
}
