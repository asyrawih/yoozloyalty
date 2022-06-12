<?php

namespace Platform\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Carbon\Carbon;

class CreditRuleConversion extends Model
{
    use GeneratesUuid;

    protected $table = 'credit_rule_conversions';

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

    /**
     * Field mutators.
     *
     * @var array
     */
    protected $casts = [
      'uuid' => EfficientUuid::class,
    ];

    public function getCreatedAtAttribute($date)
    {
        $user = User::withoutGlobalScopes()->find(auth()->id());

        return Carbon::parse($date, config('app.timezone'))
            ->setTimezone($user->getTimezone())
            ->format('M d, Y H:i');
    }

    public static function boot()
    {
        parent::boot();

        // On update
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->id;
            }
        });

        // On create
        self::creating(function ($model) {
            if (auth()->check()) {
                $model->account_id = auth()->user()->account_id;

                $model->created_by = auth()->user()->id;
            }
        });
    }

    /**
     * Relationships
     * -------------
     */

    public function campaign()
    {
        return $this->hasOne(\App\User::class, 'id', 'campaign_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\User::class, 'account_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'company_user', 'company_id', 'user_id');
    }
}
