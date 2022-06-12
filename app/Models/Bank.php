<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bank extends Model
{
    protected $fillable = [
        'bank_name',
        'branch_name',
        'branch_code',
        'account_number',
        'account_name',
        'account_type_id',
        'is_active'
    ];

    protected $cast = [
        'is_active' => 'boolean'
    ];

    public function scopeWhereActive(Builder $query): Builder
    {
        return $query->where('is_active', 1);
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(BankAccountType::class);
    }
}
