<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::withCount('pets')
            ->when($request->q, fn($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function show(Cliente $cliente)
    {
        $cliente->load([
            'pets.vacinas',
            'agendamentos' => fn($q) => $q->with(['servico', 'colaborador'])->orderByDesc('scheduled_at')->limit(20),
        ]);

        return view('clientes.show', compact('cliente'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated());
        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());
        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente atualizado!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente removido.');
    }

    public function buscar(Request $request)
    {
        $q = $request->get('q', '');
        $clientes = Cliente::with('pets')
            ->where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id'    => $c->id,
                'name'  => $c->name,
                'phone' => $c->phone,
                'pets'  => $c->pets->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'species' => $p->species]),
            ]);

        return response()->json($clientes);
    }
}
