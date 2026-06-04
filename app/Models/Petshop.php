<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'logo',
        'primary_color',
        'phone',
        'address',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function colaboradores()
    {
        return $this->hasMany(Colaborador::class);
    }

    public function servicos()
    {
        return $this->hasMany(Servico::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function financeiro()
    {
        return $this->hasMany(Financeiro::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function fidelidadeConfig()
    {
        return $this->hasOne(FidelidadeConfig::class);
    }
}
