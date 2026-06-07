<?php

namespace App\Traits;

use App\Models\Petshop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait BelongsToPetshop
{
    public static function bootBelongsToPetshop(): void
    {
        static::addGlobalScope('petshop', function (Builder $builder) {
            $model = $builder->getModel();
            $table = $model->getTable();

            if (!Schema::hasColumn($table, 'petshop_id')) {
                return;
            }

            if (Auth::check() && ($petshopId = Auth::user()->currentPetshopId())) {
                $builder->where("{$table}.petshop_id", $petshopId);
            }
        });

        static::creating(function ($model) {
            if (!Schema::hasColumn($model->getTable(), 'petshop_id')) {
                return;
            }
            if (empty($model->petshop_id) && Auth::check() && ($petshopId = Auth::user()->currentPetshopId())) {
                $model->petshop_id = $petshopId;
            }
        });
    }

    public function scopeDoMeuPetshop(Builder $query): Builder
    {
        if (Auth::check() && ($petshopId = Auth::user()->currentPetshopId())) {
            return $query->where($this->getTable() . '.petshop_id', $petshopId);
        }
        return $query;
    }

    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }
}
