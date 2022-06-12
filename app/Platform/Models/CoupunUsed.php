<?php

namespace Platform\Models;

use Illuminate\Database\Eloquent\Model;
use Platform\Controllers\Core;
use Carbon\Carbon;

class CoupunUsed extends Model
{
    protected $table = 'coupun_useds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date, config('app.timezone'))->setTimezone(auth()->user()->getTimezone())->format('M d, Y H:i');
    }

    public static function boot() {
        parent::boot();

        // On update
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        // On create
        self::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }

    /**
     * Relationships
     * -------------
     */

    public function users()
    {
        return $this->belongsTo(\App\Customer::class, 'created_by', 'id');
    }

    public function reward()
    {
        return $this->belongsTo(\Platform\Models\Reward::class, 'reward_id', 'id');
    }

    public function coupunCode()
    {
        return $this->belongsTo(\Platform\Models\CoupunCode::class, 'coupun_code_id', 'id');
    }
}
