<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class EstoqueMovimento extends Model
{
    use BelongsToPetshop;

    protected $table = 'estoque_movimentos';

    protected $fillable = [
        'petshop_id',
        'produto_id',
        'type',
        'quantity',
        'stock_after',
        'reason',
        'venda_id',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
