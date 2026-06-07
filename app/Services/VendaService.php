<?php

namespace App\Services;

use App\Models\EstoqueMovimento;
use App\Models\Financeiro;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\VendaItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Regra de negócio do PDV: registra a venda, baixa o estoque, grava o
 * movimento de estoque e lança a receita no Financeiro — tudo em transação.
 */
class VendaService
{
    /**
     * @param array{cliente_id?:int|null, payment_method?:string, desconto?:float, itens:array<array{produto_id:int, quantity:int}>} $data
     */
    public function registrar(array $data, int $petshopId, ?int $userId): Venda
    {
        $itens = collect($data['itens'] ?? [])
            ->filter(fn ($i) => !empty($i['produto_id']) && (int) ($i['quantity'] ?? 0) > 0);

        if ($itens->isEmpty()) {
            throw ValidationException::withMessages(['itens' => 'Adicione ao menos um produto à venda.']);
        }

        return DB::transaction(function () use ($itens, $data, $petshopId, $userId) {
            $subtotal = 0;
            $linhas   = [];

            foreach ($itens as $i) {
                /** @var Produto $produto */
                $produto = Produto::lockForUpdate()->findOrFail($i['produto_id']);
                $qtd     = (int) $i['quantity'];

                if ($produto->stock_quantity < $qtd) {
                    throw ValidationException::withMessages([
                        'itens' => "Estoque insuficiente para \"{$produto->name}\" (disponível: {$produto->stock_quantity}).",
                    ]);
                }

                $linhaSub = round($produto->price * $qtd, 2);
                $subtotal += $linhaSub;
                $linhas[] = [$produto, $qtd, $produto->price, $linhaSub];
            }

            $desconto = round(max(0, (float) ($data['desconto'] ?? 0)), 2);
            $total    = max(0, round($subtotal - $desconto, 2));

            $venda = Venda::create([
                'petshop_id'     => $petshopId,
                'cliente_id'     => $data['cliente_id'] ?? null,
                'user_id'        => $userId,
                'subtotal'       => $subtotal,
                'desconto'       => $desconto,
                'total'          => $total,
                'payment_method' => $data['payment_method'] ?? 'dinheiro',
                'status'         => 'concluida',
            ]);

            foreach ($linhas as [$produto, $qtd, $preco, $linhaSub]) {
                VendaItem::create([
                    'venda_id'   => $venda->id,
                    'produto_id' => $produto->id,
                    'nome'       => $produto->name,
                    'quantity'   => $qtd,
                    'unit_price' => $preco,
                    'subtotal'   => $linhaSub,
                ]);

                $produto->decrement('stock_quantity', $qtd);

                EstoqueMovimento::create([
                    'petshop_id'  => $petshopId,
                    'produto_id'  => $produto->id,
                    'type'        => 'venda',
                    'quantity'    => -$qtd,
                    'stock_after' => $produto->stock_quantity,
                    'reason'      => "Venda #{$venda->id}",
                    'venda_id'    => $venda->id,
                ]);
            }

            // Lança a receita no Financeiro.
            Financeiro::create([
                'petshop_id'  => $petshopId,
                'type'        => 'receita',
                'amount'      => $total,
                'description' => "Venda PDV #{$venda->id}",
                'paid_at'     => now(),
            ]);

            return $venda->load('itens');
        });
    }

    /**
     * Ajusta o estoque de um produto (entrada/saída/ajuste) e registra o movimento.
     */
    public function ajustarEstoque(Produto $produto, int $quantidade, string $type, int $petshopId, ?string $reason = null): void
    {
        DB::transaction(function () use ($produto, $quantidade, $type, $petshopId, $reason) {
            $delta = $type === 'saida' ? -abs($quantidade) : abs($quantidade);
            $novo  = max(0, $produto->stock_quantity + $delta);

            $produto->update(['stock_quantity' => $novo]);

            EstoqueMovimento::create([
                'petshop_id'  => $petshopId,
                'produto_id'  => $produto->id,
                'type'        => $type,
                'quantity'    => $delta,
                'stock_after' => $novo,
                'reason'      => $reason,
            ]);
        });
    }
}
