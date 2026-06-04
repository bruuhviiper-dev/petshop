<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Financeiro extends Model
{
    use BelongsToPetshop;

    protected $table = 'financeiro';

    protected $fillable = [
        'petshop_id',
        'agendamento_id',
        'type',
        'amount',
        'description',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class);
    }
}
