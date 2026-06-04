<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use BelongsToPetshop;

    protected $table = 'horarios';

    protected $fillable = [
        'petshop_id',
        'weekday',
        'open',
        'close',
        'closed',
    ];

    protected function casts(): array
    {
        return [
            'closed' => 'boolean',
        ];
    }

    public function getDayNameAttribute(): string
    {
        $days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        return $days[$this->weekday] ?? '';
    }
}
