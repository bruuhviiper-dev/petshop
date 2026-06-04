<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    use BelongsToPetshop;

    protected $table = 'colaboradores';

    protected $fillable = [
        'user_id',
        'petshop_id',
        'cargo',
        'comissao_percentual',
    ];

    protected function casts(): array
    {
        return [
            'comissao_percentual' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function comissoes()
    {
        return $this->hasMany(Comissao::class);
    }
}
