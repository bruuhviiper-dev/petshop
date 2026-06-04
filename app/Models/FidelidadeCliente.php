<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class FidelidadeCliente extends Model
{
    use BelongsToPetshop;

    protected $table = 'fidelidade_clientes';

    protected $fillable = [
        'cliente_id',
        'petshop_id',
        'total_atendimentos',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
