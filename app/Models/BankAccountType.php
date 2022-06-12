<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccountType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $cast = [
        'is_active' => 'boolean',
    ];

    public function scopeWhereActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class);
    }
}
