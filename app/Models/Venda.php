<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use BelongsToPetshop;

    protected $table = 'vendas';

    protected $fillable = [
        'petshop_id',
        'cliente_id',
        'user_id',
        'subtotal',
        'desconto',
        'total',
        'payment_method',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'desconto' => 'decimal:2',
            'total'    => 'decimal:2',
        ];
    }

    public function itens()
    {
        return $this->hasMany(VendaItem::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
