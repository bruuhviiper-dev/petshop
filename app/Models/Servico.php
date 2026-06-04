<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use BelongsToPetshop;

    protected $table = 'servicos';

    protected $fillable = [
        'petshop_id',
        'name',
        'duration_minutes',
        'price',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}
