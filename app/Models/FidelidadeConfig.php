<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class FidelidadeConfig extends Model
{
    use BelongsToPetshop;

    protected $table = 'fidelidade_config';

    protected $fillable = [
        'petshop_id',
        'atendimentos_para_premio',
        'desconto_percentual',
    ];

    protected function casts(): array
    {
        return [
            'desconto_percentual' => 'decimal:2',
        ];
    }
}
