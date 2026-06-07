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
        'public_token',
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

    /** Garante um token público para a carteirinha digital e retorna-o. */
    public function ensurePublicToken(): string
    {
        if (!$this->public_token) {
            $this->forceFill(['public_token' => \Illuminate\Support\Str::random(32)])->save();
        }

        return $this->public_token;
    }
}
