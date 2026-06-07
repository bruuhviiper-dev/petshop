<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use BelongsToPetshop;

    protected $table = 'produtos';

    protected $fillable = [
        'petshop_id',
        'name',
        'sku',
        'category',
        'price',
        'cost',
        'stock_quantity',
        'min_stock',
        'unit',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price'          => 'decimal:2',
            'cost'           => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_stock'      => 'integer',
            'active'         => 'boolean',
        ];
    }

    public function movimentos()
    {
        return $this->hasMany(EstoqueMovimento::class);
    }

    /** Estoque igual ou abaixo do mínimo configurado. */
    public function getEstoqueBaixoAttribute(): bool
    {
        return $this->stock_quantity <= $this->min_stock;
    }
}
