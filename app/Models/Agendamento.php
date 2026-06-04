<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use BelongsToPetshop;

    protected $table = 'agendamentos';

    protected $fillable = [
        'petshop_id',
        'cliente_id',
        'pet_id',
        'colaborador_id',
        'servico_id',
        'scheduled_at',
        'status',
        'notes',
        'photo_before',
        'photo_after',
        'valor',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'valor' => 'decimal:2',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }

    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }

    public function comissoes()
    {
        return $this->hasMany(Comissao::class);
    }

    public function notificacoesLog()
    {
        return $this->hasMany(NotificacaoLog::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pendente'     => 'amber',
            'confirmado'   => 'blue',
            'em_andamento' => 'purple',
            'concluido'    => 'green',
            'cancelado'    => 'gray',
            default        => 'gray',
        };
    }
}
