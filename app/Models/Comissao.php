<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Comissao extends Model
{
    use BelongsToPetshop;

    protected $table = 'comissoes';

    protected $fillable = [
        'colaborador_id',
        'agendamento_id',
        'amount',
        'paid',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class);
    }
}
