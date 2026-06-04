<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class NotificacaoLog extends Model
{
    use BelongsToPetshop;

    protected $table = 'notificacoes_log';

    protected $fillable = [
        'agendamento_id',
        'type',
        'sent_at',
        'status',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class);
    }
}
