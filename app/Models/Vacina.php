<?php

namespace App\Models;

use App\Traits\BelongsToPetshop;
use Illuminate\Database\Eloquent\Model;

class Vacina extends Model
{
    use BelongsToPetshop;

    protected $table = 'vacinas';

    protected $fillable = [
        'pet_id',
        'name',
        'date_applied',
        'next_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_applied' => 'date',
            'next_date' => 'date',
        ];
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
