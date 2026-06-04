<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use BelongsToPetshop;

    protected $table = 'clientes';

    protected $fillable = [
        'petshop_id',
        'name',
        'phone',
        'email',
        'birthdate',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function fidelidade()
    {
        return $this->hasOne(FidelidadeCliente::class);
    }
}
