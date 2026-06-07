<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Services\VendaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdutoController extends Controller
{
    public function __construct(private readonly VendaService $vendaService) {}

    public function index(Request $request): View
    {
        $busca = $request->get('q');

        $produtos = Produto::query()
            ->when($busca, fn ($q) => $q->where('name', 'like', "%{$busca}%")->orWhere('sku', 'like', "%{$busca}%"))
            ->orderBy('name')
            ->get();

        return view('produtos.index', compact('produtos', 'busca'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'nullable|string|max:100',
            'category'       => 'nullable|string|max:100',
            'price'          => 'required|numeric|min:0',
            'cost'           => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock'      => 'nullable|integer|min:0',
            'unit'           => 'nullable|string|max:10',
        ]);
        $data['active'] = true;

        Produto::create($data);

        return back()->with('success', 'Produto cadastrado!');
    }

    public function update(Request $request, Produto $produto): RedirectResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'sku'       => 'nullable|string|max:100',
            'category'  => 'nullable|string|max:100',
            'price'     => 'required|numeric|min:0',
            'cost'      => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'unit'      => 'nullable|string|max:10',
        ]);
        $data['active'] = $request->boolean('active');

        $produto->update($data);

        return back()->with('success', 'Produto atualizado!');
    }

    public function destroy(Produto $produto): RedirectResponse
    {
        $produto->delete();

        return back()->with('success', 'Produto removido.');
    }

    /** Ajuste manual de estoque (entrada/saída/ajuste). */
    public function ajustarEstoque(Request $request, Produto $produto): RedirectResponse
    {
        $data = $request->validate([
            'type'     => 'required|in:entrada,saida,ajuste',
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
        ]);

        $this->vendaService->ajustarEstoque(
            $produto,
            $data['quantity'],
            $data['type'],
            auth()->user()->currentPetshopId(),
            $data['reason'] ?? null
        );

        return back()->with('success', 'Estoque atualizado!');
    }
}
