<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use BelongsToPetshop;

    protected $table = 'pets';

    protected $fillable = [
        'cliente_id',
        'name',
        'species',
        'breed',
        'weight',
        'temperament',
        'allergies',
        'notes',
        'photo',
        'retorno_dias',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vacinas()
    {
        return $this->hasMany(Vacina::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function ultimoAgendamento()
    {
        return $this->hasOne(Agendamento::class)->latestOfMany('scheduled_at');
    }
}
