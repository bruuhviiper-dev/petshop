<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;
use App\Services\VendaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PdvController extends Controller
{
    public function __construct(private readonly VendaService $vendaService) {}

    /** Tela do PDV (frente de caixa). */
    public function index(): View
    {
        $produtos = Produto::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'price', 'stock_quantity', 'unit']);

        $clientes = Cliente::orderBy('name')->get(['id', 'name', 'phone']);

        return view('pdv.index', compact('produtos', 'clientes'));
    }

    /** Processa a venda. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cliente_id'        => 'nullable|exists:clientes,id',
            'payment_method'    => 'required|in:dinheiro,pix,debito,credito',
            'desconto'          => 'nullable|numeric|min:0',
            'itens'             => 'required|array|min:1',
            'itens.*.produto_id'=> 'required|exists:produtos,id',
            'itens.*.quantity'  => 'required|integer|min:1',
        ]);

        $venda = $this->vendaService->registrar(
            $data,
            auth()->user()->currentPetshopId(),
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'venda'   => [
                'id'    => $venda->id,
                'total' => 'R$ ' . number_format($venda->total, 2, ',', '.'),
            ],
        ]);
    }

    /** Histórico de vendas. */
    public function historico(): View
    {
        $vendas = Venda::with(['cliente', 'itens'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('pdv.historico', compact('vendas'));
    }
}
