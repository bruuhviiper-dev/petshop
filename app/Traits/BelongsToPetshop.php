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

            if (Auth::check() && Auth::user()->petshop) {
                $builder->where("{$table}.petshop_id", Auth::user()->petshop->id);
            }
        });

        static::creating(function ($model) {
            if (!Schema::hasColumn($model->getTable(), 'petshop_id')) {
                return;
            }
            if (empty($model->petshop_id) && Auth::check() && Auth::user()->petshop) {
                $model->petshop_id = Auth::user()->petshop->id;
            }
        });
    }

    public function scopeDoMeuPetshop(Builder $query): Builder
    {
        if (Auth::check() && Auth::user()->petshop) {
            return $query->where($this->getTable() . '.petshop_id', Auth::user()->petshop->id);
        }
        return $query;
    }

    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }
}
